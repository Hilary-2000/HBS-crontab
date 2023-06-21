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
	$dbname = 'my_isp2';
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

	// check if the connection is valid
	if ($conn && $conn2) {
        // start by getting the last index
        $select = "SELECT `transaction_id` FROM `transaction_tables` ORDER by `transaction_id` DESC LIMIT 1";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction_id = 0;
        if ($row = $result->fetch_assoc()) {
            $transaction_id = $row['transaction_id'];
        }
        // echo $transaction_id;
        // select from the secondary table from the last transaction id
        $select = "SELECT * FROM `transaction_tables` WHERE `transaction_id` > ".$transaction_id."";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values = "";
        while ($row = $result->fetch_assoc()) {
            $values.="(";
            foreach ($row as $key => $value) {
                // echo $key." ".$value."<br>";
                $values.="'".$value."',";
            }
            $values = substr($values,0,strlen($values)-1)."),";
        }
        // echo $values;
        $values = substr($values,0,strlen($values)-1);
        // echo $values;
        // get the tables columns
        $select = "DESC `transaction_tables`";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $my_rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($my_rows,$row);
        }
        $transtable_column = "(";
        for ($index=0; $index < count($my_rows); $index++) { 
            $row_data = $my_rows[$index];
            foreach ($row_data as $key => $value) {
                // echo $key." ".$value." | ";
                if ($key == "Field") {
                    $transtable_column .= $value.",";
                }
            }
            // echo"<br>";
        }
        $transtable_column = substr($transtable_column,0,strlen($transtable_column)-1).")";
        // echo $transtable_column;
        $insert = "INSERT INTO `transaction_tables` ".$transtable_column." VALUES ".$values;
        // echo $insert.strlen($values);
        if (strlen($values) > 0) {
            // insert the row in the database;
            $stmt = $conn->prepare($insert);
            // $stmt->execute();
            if($stmt->execute()){
                echo "<br>Updated new records to the transaction table";
                // delete the records in the secondary table before the last index
                $delete = "DELETE FROM `transaction_tables` WHERE `transaction_id` <= $transaction_id";
                $stmt = $conn2->prepare($delete);
                // <--- Update changes made locally!
                if($stmt->execute()){
                    // proceed and insert the primary records to the secondary records
                    $select = "SELECT * FROM `transaction_tables` WHERE `transaction_id` <= $transaction_id";
                    $stmt = $conn->prepare($select);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $values = "";
                    while ($row = $result->fetch_assoc()) {
                        $values.="(";
                        foreach ($row as $key => $value) {
                            // echo $key." ".$value."<br>";
                            $values.="'".$value."',";
                        }
                        $values = substr($values,0,strlen($values)-1)."),";
                    }
                    $values = substr($values,0,strlen($values)-1);
                    $insert = "INSERT INTO `transaction_tables` ".$transtable_column." VALUES ".$values;
                    $stmt = $conn2->prepare($insert);
                    if($stmt->execute()){
                        echo "<br>Transaction table updated successfully";
                    }else{
                        echo "<br> An error occured during update 1!";
                    }
                }else {
                    echo "<br>An error occured during syncing!";
                }
            }else {
                echo "<br>An error occured during update 2";
            }
        }else {
            // delete everything in the transaction table and update with the new transaction table from the local database
            echo "<br>Updated new records to the transaction table";
            // delete the records in the secondary table before the last index
            $delete = "DELETE FROM `transaction_tables`";
            $stmt = $conn->prepare($delete);
            if($stmt->execute()){
                // proceed and insert the primary records to the secondary records
                $select = "SELECT * FROM `transaction_tables`";
                $stmt = $conn2->prepare($select);
                $stmt->execute();
                $result = $stmt->get_result();
                $values = "";
                while ($row = $result->fetch_assoc()) {
                    $values.="(";
                    foreach ($row as $key => $value) {
                        // echo $key." ".$value."<br>";
                        $values.="'".$value."',";
                    }
                    $values = substr($values,0,strlen($values)-1)."),";
                }
                $values = substr($values,0,strlen($values)-1);
                $insert = "INSERT INTO `transaction_tables` ".$transtable_column." VALUES ".$values;
                $stmt = $conn->prepare($insert);
                if($stmt->execute()){
                    echo "<br>Transaction table updated successfully";
                }else{
                    echo "<br> An error occured during update!";
                }
            }else {
                echo "<br>An error occured during syncing!";
            }
        }
        // echo "<br>Similar ".$similar;
        echo "<br>Sync done successfully!";
    }else {
        echo "No connections";
    }

    function ispresent($array1,$value){
        for ($i=0; $i < count($array1); $i++) { 
            if ($array1[$i] == $value) {
                return true;
            }
        }
        return false;
    }
?>