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
        
        // get the tables columns
        $select = "DESC `client_tables`";
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
                    if ($value == "sms_id") {
                        continue;
                    }
                    $transtable_column .= $value.",";
                }
            }
            // echo"<br>";
        }
        $transtable_column = substr($transtable_column,0,strlen($transtable_column)-1).")";
        // END OF TABLE COLUMNS
        $select = "SELECT * FROM `client_tables` ORDER BY `client_id` DESC LIMIT 1";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $last_record = 0;
        if ($row = $result->fetch_assoc()) {
            $last_record = $row['client_id'];
        }

        // select all the new records after the index of the last record in the online database;
        $select = "SELECT * FROM `client_tables` WHERE `client_id` > $last_record";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_loc = "";
        while ($row = $result->fetch_assoc()) {
            $values_loc.="(";
            foreach ($row as $key => $value) {
                // echo $key." ".$value."<br>";
                $values_loc.="\"".$value."\",";
            }
            $values_loc = substr($values_loc,0,strlen($values_loc)-1)."),";
        }
        echo $values_loc;
        $values_loc = substr($values_loc,0,strlen($values_loc)-1);
        if (strlen($values_loc) > 1) {
            // we take the records and insert it in the database in the cloud
            $insert = "INSERT INTO  `client_tables` ".$transtable_column." VALUES ".$values_loc;
            $stmt = $conn2->prepare($insert);
            if($stmt->execute()){
                echo "<br>Update was successfull";
            }else {
                echo "<br>An error occured during update";
            }
        }
        // get data from local
        $select = "SELECT * FROM `client_tables`";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $client_locale = [];
        $client_locale_id = [];
        $present = 0;
        while ($row = $result->fetch_assoc()) {
            array_push($client_locale,$row);
            foreach ($row  as $key => $value) {
                if ($key == "client_id") {
                    array_push($client_locale_id,$value);
                }
            }
        }
        // check for the clients in the other databse that are not present there and delete them
        $select = "SELECT * FROM `client_tables`";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $client_remote = [];
        $clients_to_delete = [];
        $present = 0;
        while ($row = $result->fetch_assoc()) {
            $present = 0;
            foreach ($row  as $key => $value) {
                if ($key == "client_id") {
                    if (!ispresent($client_locale_id,$value)) {
                        array_push($clients_to_delete,$value);
                        $present = 1;
                    }
                }
            }
            if ($present == 0) {
                array_push($client_remote,$row);
            }
        }
        // var_dump($clients_to_delete);
        // we delete the clients that were deleted in the local server
        $delete = "DELETE FROM `client_tables` WHERE `client_id` = ?";
        $stmt = $conn2->prepare($delete);
        for ($ind=0; $ind < count($clients_to_delete); $ind++) { 
            $stmt->bind_param("s",$clients_to_delete[$ind]);
            $stmt->execute();
        }
        // // after deleting the clients data we check for the wallet change amount
        $similar = 0;
        // echo "br-".count($client_remote)."client count<br>";
        $transtable_column = substr(trim($transtable_column),1,(strlen($transtable_column)-2));
        for ($index=0; $index < count($client_remote); $index++) { 
            $row_remote = $client_remote[$index];
            $row_locale = $client_locale[$index];
            if ($row_remote != $row_locale) {
                // IF NOT SIMILAR CHECK IF THE WALLET AMOUNT IS DIFFERENT
                // IF IT DIFFERENT THE REMOTE VALUE IS CORRECT OVER THE LOCALE VALUE
                $wallet_locale = $row_locale['wallet_amount'];
                $wallet_remote = $row_remote['wallet_amount'];
                $next_exp_locale = $row_locale['next_expiration_date'];
                $next_exp_remote = $row_remote['next_expiration_date'];
                // echo $next_exp_remote."count<br>";
                $client_freeze_status1 = $row_remote['client_freeze_status'];
                $client_freeze_untill1 = $row_remote['client_freeze_untill'];
                $client_freeze_status2 = $row_locale['client_freeze_status'];
                $client_freeze_untill2 = $row_locale['client_freeze_untill'];
                $reffered_by2 = $row_remote['reffered_by'];
                $reffered_by1 = $row_locale['reffered_by'];
                if ($wallet_locale == $wallet_remote && $next_exp_locale == $next_exp_remote && 
                    $client_freeze_status1 == $client_freeze_status2 && $client_freeze_untill1 == $client_freeze_untill2 && $reffered_by1 == $reffered_by2){
                    // UPDATE THE WHOLE RECORD EXCEPT THE WALLET
                    $update_col = explode(",",$transtable_column);
                    $col_value = "";
                    for ($indexed=0; $indexed < count($update_col); $indexed++) { 
                        $col_ind = $update_col[$indexed];
                        if ($col_ind == "wallet_amount" || $col_ind == "client_id" || $col_ind == "next_expiration_date" || $col_ind == "client_freeze_status" || $col_ind == "client_freeze_untill" || $col_ind == "reffered_by") {
                            continue;
                        }
                        $col_value .= "`$update_col[$indexed]` = \"".mysqli_escape_string($conn,$row_locale[$col_ind])."\" ,";
                    }
                    $col_value = substr($col_value,0,strlen($col_value)-1);
                    $client_id = $row_locale['client_id'];
                    $update = "UPDATE `client_tables` SET ".$col_value." WHERE `client_id` = '$client_id'";
                    echo $update."<br>";

                    if(update_client_remote($update,$conn2,$client_id) == 1){
                        echo "Index ".$index."<br>";
                    }
                }else{
                    // just update the wallet
                    $client_id = $row_locale['client_id'];
                    $next_expiration_date = $row_remote['next_expiration_date'];
                    $client_freeze_status = $row_remote['client_freeze_status'];
                    $client_freeze_untill = $row_remote['client_freeze_untill'];
                    $reffered_by = $row_remote['reffered_by'];
                    $update = "UPDATE `client_tables` SET `wallet_amount` = '$wallet_remote' , `next_expiration_date` = '$next_expiration_date', `client_freeze_status` = '$client_freeze_status', `client_freeze_untill` = '$client_freeze_untill', `reffered_by` = '$reffered_by' WHERE `client_id` = '$client_id'";
                    $stmt = $conn->prepare($update);
                    echo $update."<br>";
                    // echo "<br>remote exp date".$next_expiration_date." next exp date";
                    if($stmt->execute()){
                        echo "<br>Exp updated successfully!";
                    }else {
                        echo "<br>Error occured!";
                    }
                }
            }else {
                $similar++;
            }
        }
        echo "<br>Similar ".$similar;
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