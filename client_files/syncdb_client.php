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
        // get tables for my local database because its the primary database except transaction table
        // because tansaction table interacts with safaricom request from the cloud;
        $select = "show tables;";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $tables = [];
        while ($row = $result->fetch_assoc()) {
            array_push($tables,$row);
        }
        $my_tables_local = [];
        for ($index=0; $index < count($tables); $index++) { 
            foreach ($tables[$index] as $key => $value) {
                // echo $key." = ".$value."<br>";
                if ($value == "transaction_tables" || $value == "sms_tables" || $value == "client_tables" || $value == "verification_codes" || $value == "admin_tables") {
                    continue;
                }
                array_push($my_tables_local,$value);
            }
        }

        $select = "show tables;";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $tables = [];
        while ($row = $result->fetch_assoc()) {
            array_push($tables,$row);
        }
        $my_tables_remote = [];
        for ($index=0; $index < count($tables); $index++) { 
            foreach ($tables[$index] as $key => $value) {
                // echo $key." = ".$value."<br>";
                if ($value == "transaction_tables" || $value == "sms_tables" || $value == "client_tables" || $value == "verification_codes" || $value == "admin_tables") {
                    continue;
                }
                array_push($my_tables_remote,$value);
            }
        }
        // var_dump($my_tables_remote);
        for ($index=0; $index < count($my_tables_remote); $index++) { 
            sync_tables($conn, $my_tables_local[$index], $conn2, $my_tables_remote[$index]);
        }

        // the transaction table 
        // at the transaction table we insert the new fields of payment
        // so at the primary table table 1 we get the last index of where the transaction was last reached
        // then get the data from the secondary table from where the last index was left off
        // then insert the data in the primary table
        // then after that check the previous rows for any changes update it to the primary table

        // start by getting the last index
        $select = "SELECT `transaction_id` FROM `transaction_tables` ORDER by `transaction_id` DESC LIMIT 1";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction_id = 0;
        if ($row = $result->fetch_assoc()) {
            $transaction_id = $row['transaction_id'];
        }
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
        if (strlen($values) > 0) {
            // insert the row in the database;
            $stmt = $conn->prepare($insert);
            $stmt->execute();
            // if($stmt->execute()){
            //     echo "<br>Updated new records to the transaction table";
            //     // delete the records in the secondary table before the last index
            //     $delete = "DELETE FROM `transaction_tables` WHERE `transaction_id` <= $transaction_id";
            //     $stmt = $conn2->prepare($delete);
            //     // <--- Update changes made locally!
            //     if($stmt->execute()){
            //         // proceed and insert the primary records to the secondary records
            //         $select = "SELECT * FROM `transaction_tables` WHERE `transaction_id` <= $transaction_id";
            //         $stmt = $conn->prepare($select);
            //         $stmt->execute();
            //         $result = $stmt->get_result();
            //         $values = "";
            //         while ($row = $result->fetch_assoc()) {
            //             $values.="(";
            //             foreach ($row as $key => $value) {
            //                 // echo $key." ".$value."<br>";
            //                 $values.="'".$value."',";
            //             }
            //             $values = substr($values,0,strlen($values)-1)."),";
            //         }
            //         $values = substr($values,0,strlen($values)-1);
            //         $insert = "INSERT INTO `transaction_tables` ".$transtable_column." VALUES ".$values;
            //         $stmt = $conn2->prepare($insert);
            //         if($stmt->execute()){
            //             echo "<br>Transaction table updated successfully";
            //         }else{
            //             echo "<br> An error occured during update!";
            //         }
            //     }else {
            //         echo "<br>An error occured during syncing!";
            //     }
            // }else {
            //     echo "<br>An error occured during update";
            // }
        }else {
        // delete everything in the transaction table and update with the new transaction table from the local database
            // echo "<br>Updated new records to the transaction table";
            // // delete the records in the secondary table before the last index
            // $delete = "DELETE FROM `transaction_tables`";
            // $stmt = $conn2->prepare($delete);
            // if($stmt->execute()){
            //     // proceed and insert the primary records to the secondary records
            //     $select = "SELECT * FROM `transaction_tables`";
            //     $stmt = $conn->prepare($select);
            //     $stmt->execute();
            //     $result = $stmt->get_result();
            //     $values = "";
            //     while ($row = $result->fetch_assoc()) {
            //         $values.="(";
            //         foreach ($row as $key => $value) {
            //             // echo $key." ".$value."<br>";
            //             $values.="'".$value."',";
            //         }
            //         $values = substr($values,0,strlen($values)-1)."),";
            //     }
            //     $values = substr($values,0,strlen($values)-1);
            //     $insert = "INSERT INTO `transaction_tables` ".$transtable_column." VALUES ".$values;
            //     $stmt = $conn2->prepare($insert);
            //     if($stmt->execute()){
            //         echo "<br>Transaction table updated successfully";
            //     }else{
            //         echo "<br> An error occured during update!";
            //     }
            // }else {
            //     echo "<br>An error occured during syncing!";
            // }
        }
        // SMS SECTION
        // get the tables columns
        $select = "DESC `sms_tables`";
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
        // echo $transtable_column;

        // messages get all smses send a minute ago and populate add them from local and from remote
        $minute_ago = date("YmdHis",strtotime("-5 minute"));
        echo "<br> A minute ago ".$minute_ago;
        // get data from the remote table
        $select = "SELECT * FROM `sms_tables` WHERE `date_sent` > $minute_ago";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_remote = "";
        while ($row = $result->fetch_assoc()) {
            $values_remote.="(";
            foreach ($row as $key => $value) {
                if ($key == "sms_id") {
                    continue;
                }
                // echo $key." ".$value."<br>";
                $values_remote.="'".$value."',";
            }
            $values_remote = substr($values_remote,0,strlen($values_remote)-1)."),";
        }
        $values_remote = substr($values_remote,0,strlen($values_remote)-1);

        // get data from the remote table
        $select = "SELECT * FROM `sms_tables` WHERE `date_sent` > $minute_ago";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_loc = "";
        while ($row = $result->fetch_assoc()) {
            $values_loc.="(";
            foreach ($row as $key => $value) {
                // echo $key." ".$value."<br>";
                if ($key == "sms_id") {
                    continue;
                }
                $values_loc.="'".$value."',";
            }
            $values_loc = substr($values_loc,0,strlen($values_loc)-1)."),";
        }
        // echo $values_loc;
        $values_loc = substr($values_loc,0,strlen($values_loc)-1);

        if (strlen($values_remote) > 0 ) {
            // insert into the local database
            // get column values_remote
            $insert = "INSERT INTO `sms_tables` ".$transtable_column." VALUES ".$values_remote."";
            // echo $insert;
            $stmt = $conn->prepare($insert);
            if($stmt->execute()){
                echo "<br>Remote synced successfully!";
            }
        }
        if (strlen($values_loc) > 0 ) {
            // insert into the local database
            // get column values_loc
            $insert = "INSERT INTO `sms_tables` ".$transtable_column." VALUES ".$values_loc."";
            // echo $insert;
            $stmt = $conn2->prepare($insert);
            if($stmt->execute()){
                echo "<br>locale synced successfully!";
            }
        }

        // verification codes
        // get verification codes that are new a minute ago and add them to the verfication table both locally and remotely
        
        $select = "DESC `verification_codes`";
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
                    if ($value == "id") {
                        continue;
                    }
                    $transtable_column .= $value.",";
                }
            }
            // echo"<br>";
        }
        $transtable_column = substr($transtable_column,0,strlen($transtable_column)-1).")";
        // echo $transtable_column;
        // get all VERIFICATIONS senT a minute ago and populate add them from local and from remote
        $minute_ago = date("YmdHis",strtotime("-5 minute"));
        echo "<br> A minute ago ".$minute_ago;
        // get data from the remote table
        $select = "SELECT * FROM `verification_codes` WHERE `date_generated` > $minute_ago";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_remote = "";
        while ($row = $result->fetch_assoc()) {
            $values_remote.="(";
            foreach ($row as $key => $value) {
                if ($key == "id") {
                    continue;
                }
                // echo $key." ".$value."<br>";
                $values_remote.="'".$value."',";
            }
            $values_remote = substr($values_remote,0,strlen($values_remote)-1)."),";
        }
        $values_remote = substr($values_remote,0,strlen($values_remote)-1);
        // echo $values_remote;
        // get data from the remote table
        $select = "SELECT * FROM `verification_codes` WHERE `date_generated` > $minute_ago";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_loc = "";
        while ($row = $result->fetch_assoc()) {
            $values_loc.="(";
            foreach ($row as $key => $value) {
                // echo $key." ".$value."<br>";
                if ($key == "id") {
                    continue;
                }
                $values_loc.="'".$value."',";
            }
            $values_loc = substr($values_loc,0,strlen($values_loc)-1)."),";
        }
        $values_loc = substr($values_loc,0,strlen($values_loc)-1);
        // echo $values_loc;
        if (strlen($values_remote) > 0 ) {
            // insert into the local database
            // get column values_remote
            $insert = "INSERT INTO `verification_codes` ".$transtable_column." VALUES ".$values_remote."";
            // echo $insert;
            $stmt = $conn->prepare($insert);
            if($stmt->execute()){
                echo "<br>Remote synced successfully!";
            }
        }
        if (strlen($values_loc) > 0 ) {
            // insert into the local database
            // get column values_loc
            $insert = "INSERT INTO `verification_codes` ".$transtable_column." VALUES ".$values_loc."";
            echo $insert;
            $stmt = $conn2->prepare($insert);
            if($stmt->execute()){
                echo "<br>locale synced successfully!";
            }
        }
        // END OF VERIFICATION CODE
        // SYNC CLLIENTS TABLES
        // INSERT NEW CLIENT RECORDS
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
                $values_loc.="'".$value."',";
            }
            $values_loc = substr($values_loc,0,strlen($values_loc)-1)."),";
        }
        // echo $values_loc;
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
        // after deleting the clients data we check for the wallet change amount
        $similar = 0;
        $transtable_column = substr(trim($transtable_column),1,(strlen($transtable_column)-2));
        for ($index=0; $index < count($client_remote); $index++) { 
            $row_remote = $client_remote[$index];
            $row_locale = $client_locale[$index];
            if ($row_remote != $row_locale) {
                // IF NOT SIMILAR CHECK IF THE WALLET AMOUNT IS DIFFERENT
                // IF IT DIFFERENT THE REMOTE VALUE IS CORRECT OVER THE LOCALE VALUE
                $wallet_locale = $row_locale['wallet_amount'];
                $wallet_remote = $row_remote['wallet_amount'];
                $last_changed_locale = date("YmdHis",strtotime($row_locale['last_changed']));
                $last_changed_remote = date("YmdHis",strtotime($row_remote['last_changed']));
                $reffered_by2 = $row_remote['reffered_by'];
                $reffered_by1 = $row_locale['reffered_by'];
                if ($wallet_locale == $wallet_remote && $last_changed_locale == $last_changed_remote){
                    // UPDATE THE WHOLE RECORD EXCEPT THE WALLET
                    $update_col = explode(",",$transtable_column);
                    $col_value = "";
                    for ($indexed=0; $indexed < count($update_col); $indexed++) { 
                        $col_ind = $update_col[$indexed];
                        if ($col_ind == "wallet_amount" || $col_ind == "last_changed" || $col_ind == "reffered_by") {
                            continue;
                        }
                        // $col_value .= "`$update_col[$indexed]` = \"".$row_locale[$col_ind]."\" ,";
                        $col_value .= "`$update_col[$indexed]` = \"".mysqli_escape_string($conn,$row_locale[$col_ind])."\" ,";
                    }
                    $col_value = substr($col_value,0,strlen($col_value)-1);
                    $client_id = $row_locale['client_id'];
                    $update = "UPDATE `client_tables` SET ".$col_value." WHERE `client_id` = '$client_id'";
                    // echo $update;
                    $stmt = $conn2->prepare($update);
                    $stmt->execute();
                }
                else{
                    if ($last_changed_locale > $last_changed_remote) {
                        echo "<br>".$last_changed_locale." ".$last_changed_remote."<br>";
                        // just update the wallet and the date changed from the locale to the remote server
                        $client_id = $row_locale['client_id'];
                        $last_changed_locale = $row_locale['last_changed'];
                        $reffered_by = $row_locale['reffered_by'];
                        $last_change = date("YmdHis");
                        $update = "UPDATE `client_tables` SET `wallet_amount` = '$wallet_locale' , `last_changed` = '$last_changed_locale', `reffered_by` = '$reffered_by' WHERE `client_id` = '$client_id'";
                        $stmt = $conn2->prepare($update);
                        if($stmt->execute()){
                            echo "<br>Exp updated successfully!";
                        }else {
                            echo "<br>Error occured!";
                        }
                    }else {
                        // just update the wallet and the date changed from the remote to the locale server
                        $client_id = $row_locale['client_id'];
                        $last_changed_remote = $row_remote['last_changed'];
                        $reffered_by = $row_remote['reffered_by'];
                        $last_change = date("YmdHis");
                        $update = "UPDATE `client_tables` SET `wallet_amount` = '$wallet_remote' , `last_changed` = '$last_changed_remote', `reffered_by` = '$reffered_by' WHERE `client_id` = '$client_id'";
                        $stmt = $conn->prepare($update);
                        if($stmt->execute()){
                            echo "<br>Exp updated successfully!";
                        }else {
                            echo "<br>Error occured!";
                        }
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
    function sync_tables($conn, $table1, $conn2, $table2){
        // describe a table with its collumns
        $select = "DESC $table1";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $my_rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($my_rows,$row);
        }
        $table1_column = [];
        for ($index=0; $index < count($my_rows); $index++) { 
            $row_data = $my_rows[$index];
            foreach ($row_data as $key => $value) {
                // echo $key." ".$value." | ";
                if ($key == "Field") {
                    array_push($table1_column,$value);
                }
            }
            // echo"<br>";
        }
        // describe a table with its collumns
        $select = "DESC $table2";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $my_rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($my_rows,$row);
        }
        $table2_column = [];
        for ($index=0; $index < count($my_rows); $index++) { 
            $row_data = $my_rows[$index];
            foreach ($row_data as $key => $value) {
                // echo $key." ".$value." | ";
                if ($key == "Field") {
                    array_push($table2_column,$value);
                }
            }
            // echo"<br>";
        }
        if ($table1_column == $table2_column) {
            // echo "Similar Columns";
            // delete the data in that database and insert new records
            $select = "SELECT * FROM `".$table1."`";
            $stmt = $conn->prepare($select);
            $stmt->execute();
            $result = $stmt->get_result();
            $table1_data = [];
            while ($row = $result->fetch_assoc()) {
                array_push($table1_data,$row);
            }
            // loop through the colums to create the insert statement
            $insert = "INSERT INTO `".$table2."` (";
            $column_list = "";// add column list in the insert statement
            // echo "{";
            for ($id=0; $id < count($table1_column); $id++) { 
                $column_list.=$table1_column[$id].",";
                // echo $table1_column[$id]."<br>";
            }
            // echo "}";
            $column_list = substr($column_list,0,(strlen($column_list)-1));
            $insert.=$column_list.")";
            // echo $insert;
            // add values in the insert statement
            $values = "";
            for ($ind=0; $ind < count($table1_data); $ind++) { 
                // echo "{<br>";
                $values.="(";
                foreach ($table1_data[$ind] as $key => $value) {
                    // echo $key." = ".$value." <br>";
                    $values.="'".$value."',";
                }
                $values = substr($values,0,(strlen($values)-1));
                $values.=") ,";
                // echo "}";
                // break;
            }
            if (strlen($values)) {
                $values = substr($values,0,(strlen($values)-1));
            }
            // echo $values;
            // add values to insert
            $insert.="VALUES ".$values;
            // echo $insert;



            // after creating the insert statement
            // proceed and delete data from the secondary table
            $delete = "DELETE FROM `".$table2."`";
            $stmt = $conn2->prepare($delete);
            echo "<br>deleting data in ".$table2."<br>";
            if($stmt->execute()){
                echo "Deleted<br>";
                // insert the data in that table
                echo "Inserting data in ".$table2."<br>";
                $stmt = $conn2->prepare($insert);
                if($stmt->execute()){
                    echo "data synced successfully!";
                }else{
                    echo "An error occured during syncing";
                }
            }
        }else{
            echo "Columns not similar";
            if ($table1 == $table2) {
                echo "Table are similar";
            }else{
                echo "Table are not similar";
            }
        }
    }
?>