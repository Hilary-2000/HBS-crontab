<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// get connection local database and get the values of the users that are due that minute
    // LOCAL
	$dbname = 'my_isp';
	$hostname = 'localhost';
	$dbusername = 'root';
	$dbpassword = '';
	if(!isset($_SESSION)) {
		session_start(); 
	}
	$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// Check connection
	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
		exit();
	}

	// get connection remote database and get the values of the users that are due that minute
    // CLOUD
	$dbname = 'hypbits';
	$hostname = 'localhost';
	$dbusername = 'root';
	$dbpassword = '';
	if(!isset($_SESSION)) {
		session_start(); 
	}
	$conn2 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// Check connection
	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
		exit();
	}

    // connect to PDO
    $pdo1 = new PDO("mysql:host=localhost;dbname=my_isp", "root", "");
    $pdo2 = new PDO("mysql:host=localhost;dbname=hypbits", "root", "");

	// check if the connection is valid
	if ($conn && $conn2) {
        $table_name = "Expenses";
        syncTable($conn,$conn2,$table_name,$pdo1,$pdo2);
    }else {
        echo "No connections";
    }

    function syncTable($conn,$conn2,$table_name,$pdo1,$pdo2){
        // get the data from the different tables
        $describe_table = "DESC $table_name";
        $stmt = $conn->prepare($describe_table);
        $stmt->execute();
        $result = $stmt->get_result();
        $description_1 = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($description_1,$row);
            }
        }


        // get the data from the different tables
        $describe_table = "DESC $table_name";
        $stmt = $conn2->prepare($describe_table);
        $stmt->execute();
        $result = $stmt->get_result();
        $description_2 = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($description_2,$row);
            }
        }
        
        if ($description_1 == $description_2) {
            // create the insert statement
            $primary_key = null;
            $insert = "INSERT INTO `$table_name` (";
            $VALUES = "";
            for ($index=0; $index < count($description_1); $index++) { 
                $insert.="`".$description_1[$index]['Field']."`,";
                if ($description_1[$index]['Key'] == "PRI") {
                    $primary_key = $description_1[$index]['Field'];
                }
                $VALUES.="?,";
            }
            $VALUES = substr($VALUES,0,(strlen($VALUES)-1));
            $insert = substr($insert,0,(strlen($insert)-1)).") VALUES ($VALUES)";
            // create the update
            $bind_param = "";
            $insert_bind_param = "";
            $update = "UPDATE `$table_name` SET ";
            for ($index=0; $index < count($description_1); $index++) {
                $update.="`".$description_1[$index]['Field']."` = ?,";
                $bind_param.="s";
                $insert_bind_param.="s";
            }
            $bind_param.="s";
            $update = substr($update,0,(strlen($update)-1))." WHERE `$primary_key` = ?";
            
            

            /**
             * THIS STEP ONLY WILL WORK ON INSERTING NEW RECORDS 
             * IT STARTS HERE
             */

            // table 1
            $select = "SELECT * FROM $table_name";
            $stmt = $conn->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_1_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_1_data,$row);
                }
            }
    
            // table 2
            $select = "SELECT * FROM $table_name";
            $stmt = $conn2->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_2_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_2_data,$row);
                }
            }

            // lets go through the whole data and get the table that has the latest data
            $count_table_1 = count($table_1_data);
            $count_table_2 = count($table_2_data);

            $counter = $count_table_1 >= $count_table_2 ? $count_table_1 : $count_table_2;
            
            for ($index=0; $index < $counter; $index++) {
                if (count($table_1_data) <= $index && count($table_2_data) > count($table_1_data)) {
                    // we want to insert the data for that particular row in this table
                    // table is connected to connection 1
                    $values = [];
                    foreach ($table_2_data[$index] as $key => $value) {
                        array_push($values,$value);
                    }
                    $stmt = $pdo1->prepare($insert);
                    
                    // loop through the values and bind them
                    for ($ind=0; $ind < count($values); $ind++) { 
                        $stmt->bindParam($ind+1,$values[$ind]);
                    }
                    $stmt->execute();
                }

                // if table data 2 les than table data one we add the new rows
                if (count($table_2_data) <= $index && count($table_1_data) > count($table_2_data)) {
                    // we want to insert the data for that particular row in this table
                    // table is connected to connection 1
                    $values = [];
                    foreach ($table_1_data[$index] as $key => $value) {
                        array_push($values,$value);
                    }
                    $stmt = $pdo2->prepare($insert);
                    
                    // loop through the values and bind them
                    for ($ind=0; $ind < count($values); $ind++) { 
                        $stmt->bindParam($ind+1,$values[$ind]);
                    }
                    $stmt->execute();
                }
            }
            /**
             * ENDS HERE
             */

             /**
              * UPDATING THE RECORDS STARTS HERE
              */

            // table 1
            $select = "SELECT * FROM $table_name";
            $stmt = $conn->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_1_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_1_data,$row);
                }
            }
    
            // table 2
            $select = "SELECT * FROM $table_name";
            $stmt = $conn2->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_2_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_2_data,$row);
                }
            }

            // lets go through the whole data and get the table that has the latest data
            $count_table_1 = count($table_1_data);
            $count_table_2 = count($table_2_data);

            $counter = $count_table_1 >= $count_table_2 ? $count_table_1 : $count_table_2;

            for ($index=0; $index < $counter; $index++) {
                // lastly check if the data has been modified by using the latest date to determine the one that is correct
                if (count($table_1_data) == count($table_2_data)) {
                    if ($table_1_data[$index]['date_changed'] > $table_2_data[$index]['date_changed']) {
                        // update tabled data 2 because table data 1 has the correct data
                        $values = [];
                        $primary_key_value = "";
                        foreach ($table_1_data[$index] as $key => $value) {
                            array_push($values,$value);
                            if ($key == $primary_key) {
                                $primary_key_value = $value;
                            }
                        }
    
                        $stmt = $pdo2->prepare($update);
                        $counts = 0;
                        for ($ind=0; $ind < count($values); $ind++) { 
                            $stmt->bindParam($ind+1,$values[$ind]);
                            $counts +=1;
                        }
                        // bind param the key
                        $stmt->bindParam($counts+1,$primary_key_value);
                        $stmt->execute();
                        echo "UPDATE TABLE 2 <br>";
                    }
    
                    // lastly check if the data has been modified by using the latest date to determine the one that is correct
                    if ($table_2_data[$index]['date_changed'] > $table_1_data[$index]['date_changed']) {
                        // update tabled data 2 because table data 1 has the correct data
                        $values = [];
                        $primary_key_value = "";
                        foreach ($table_2_data[$index] as $key => $value) {
                            array_push($values,$value);
                            if ($key == $primary_key) {
                                $primary_key_value = $value;
                            }
                        }
    
                        $stmt = $pdo1->prepare($update);
                        $counts = 0;
                        for ($ind=0; $ind < count($values); $ind++) { 
                            $stmt->bindParam($ind+1,$values[$ind]);
                            $counts +=1;
                        }
                        // bind param the key
                        $stmt->bindParam($counts+1,$primary_key_value);
                        $stmt->execute();
                        echo "UPDATE TABLE 1 <br>";
                    }
                }
            }
            /**
             * UPDATING ENDS HERE
             */

             /**
             * THIS STEP ONLY WILL WORK ON deleting deleted RECORDS 
             * IT STARTS HERE
             */
            
            // table 1
            $select = "SELECT * FROM $table_name";
            $stmt = $conn->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_1_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_1_data,$row);
                }
            }
    
            // table 2
            $select = "SELECT * FROM $table_name";
            $stmt = $conn2->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table_2_data = [];
            if ($result) {
                while($row = $result->fetch_assoc()){
                    array_push($table_2_data,$row);
                }
            }

            // lets go through the whole data and get the table that has the latest data
            $count_table_1 = count($table_1_data);
            $count_table_2 = count($table_2_data);

            $counter = $count_table_1 >= $count_table_2 ? $count_table_1 : $count_table_2;
            
            for ($index=0; $index < $counter; $index++) {
                if (count($table_1_data) == count($table_2_data)){
                    if ($table_1_data[$index]['deleted'] == 1 || $table_1_data[$index]['deleted'] == 1) {
                        $primary_key_value = "";
                        foreach ($table_2_data[$index] as $key => $value) {
                            if ($key == $primary_key) {
                                $primary_key_value = $value;
                            }
                        }
                        // delete the record
                        $delete = "DELETE FROM `$table_name` WHERE `$primary_key` = ?";
                        // table 1
                        $stmt = $conn->prepare($delete);
                        $stmt->bind_param("s",$primary_key_value);
                        $stmt->execute();

                        // table 2
                        $stmt = $conn2->prepare($delete);
                        $stmt->bind_param("s",$primary_key_value);
                        $stmt->execute();
                    }
                }
            }
            /**
             * ENDS HERE
             */

        }else {
            echo "Tables don`t match!";
        }
    }
    function ispresent($array1,$value){
        for ($i=0; $i < count($array1); $i++) { 
            if ($array1[$i] == $value) {
                return true;
            }
        }
        return false;
    }
    function update_client_remote($sql_statement,$conn2,$client_id){
        // $stmt = $conn2->prepare($sql_statement);
        // if($stmt->execute()){
        //     echo "Updated $client_id <br>";
        // }else{
        //     echo "Not Updated $client_id <br>";
        // }
        echo $sql_statement;
        if (mysqli_query($conn2, $sql_statement)) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . mysqli_error($conn2);
        }
    }
?>