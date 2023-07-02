<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// get connection local database and get the values of the users that are due that minute
    // LOCAL
	$dbname = 'hypbits';
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
	$dbname = 'my_isp';
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
                if ($value == "transaction_tables" || $value == "sms_clients" || $value == "sms_clients_packages" || $value == "transaction_sms_tables"  || $value == "sms_tables" || $value == "client_tables" || $value == "verification_codes" || $value == "admin_tables" || $value == "Expenses") {
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
                if ($value == "transaction_tables" || $value == "sms_clients" || $value == "sms_clients_packages" || $value == "transaction_sms_tables"  || $value == "sms_tables" || $value == "client_tables" || $value == "verification_codes" || $value == "admin_tables" || $value == "Expenses") {
                    continue;
                }
                array_push($my_tables_remote,$value);
            }
        }
        var_dump($my_tables_remote);
        for ($index=0; $index < count($my_tables_remote); $index++) { 
            sync_tables($conn, $my_tables_local[$index], $conn2, $my_tables_remote[$index]);
        }
        // sync transaction tables
        $transaction_table = "transaction_tables";
        sync_tables($conn2, $transaction_table, $conn, $transaction_table);

        // sync expenses
        $table_name = "Expenses";
        syncTable($conn,$conn2,$table_name,$pdo1,$pdo2);
        echo "<br>Sync done successfully!";


        // TRANSACTION END
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
                $values_remote.="\"".$value."\",";
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
                $values_loc.="\"".$value."\",";
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
                $values_remote.="\"".$value."\",";
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
                $values_loc.="\"".$value."\",";
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
        $select = "SELECT * FROM `client_tables`";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $clients_ids = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($clients_ids,$row['client_id']);
                // echo $row['client_id']."<br>";
            }
        }

        // select all the new records after the index of the last record in the online database;
        $select = "SELECT * FROM `client_tables`";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $values_loc = "";
        while ($row = $result->fetch_assoc()) {
            if (!ispresent($clients_ids,$row['client_id'])) {
                $values_loc.="(";
                foreach ($row as $key => $value) {
                    // echo $key." ".$value."<br>";
                    $value = str_replace("\"","\\\"",$value);
                    $values_loc.="\"".($value)."\",";
                }
                $values_loc = substr($values_loc,0,strlen($values_loc)-1)."),";
            }
        }

        // echo $values_loc;
        $values_loc = substr($values_loc,0,strlen($values_loc)-1);
        if (strlen($values_loc) > 1) {
            // we take the records and insert it in the database in the cloud
            $insert = "INSERT INTO  `client_tables` ".$transtable_column." VALUES ".$values_loc;
            // echo "<hr>".$insert."<hr>";
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
                $next_exp_locale = $row_locale['next_expiration_date'];
                $next_exp_remote = $row_remote['next_expiration_date'];
                $client_freeze_status1 = $row_remote['client_freeze_status'];
                $client_freeze_untill1 = $row_remote['client_freeze_untill'];
                $client_freeze_status2 = $row_locale['client_freeze_status'];
                $client_freeze_untill2 = $row_locale['client_freeze_untill'];
                $reffered_by2 = $row_remote['reffered_by'];
                $reffered_by1 = $row_locale['reffered_by'];
                $freeze_date_2 = $row_remote['freeze_date'];
                $freeze_date_1 = $row_locale['freeze_date'];
                if ($wallet_locale == $wallet_remote && $next_exp_locale == $next_exp_remote && 
                    $client_freeze_status1 == $client_freeze_status2 && 
                    $client_freeze_untill1 == $client_freeze_untill2 && 
                    $reffered_by1 == $reffered_by2 &&
                    $freeze_date_1 == $freeze_date_2){
                    // UPDATE THE WHOLE RECORD EXCEPT THE WALLET
                    $update_col = explode(",",$transtable_column);
                    $col_value = "";
                    for ($indexed=0; $indexed < count($update_col); $indexed++) { 
                        $col_ind = $update_col[$indexed];
                        if ($col_ind == "freeze_date" || $col_ind == "wallet_amount" || $col_ind == "client_id" || $col_ind == "next_expiration_date" || $col_ind == "client_freeze_status" || $col_ind == "client_freeze_untill" || $col_ind == "reffered_by") {
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
                }else{
                    // just update the wallet
                    $client_id = $row_locale['client_id'];
                    $next_expiration_date = $row_remote['next_expiration_date'];
                    $client_freeze_status = $row_remote['client_freeze_status'];
                    $client_freeze_untill = $row_remote['client_freeze_untill'];
                    $reffered_by = str_replace("\"","'",$row_remote['reffered_by']);
                    $freeze_date = $row_remote['freeze_date'];
                    $update = "UPDATE `client_tables` SET `freeze_date` = \"".$freeze_date."\",`wallet_amount` = \"$wallet_remote\" , `next_expiration_date` = \"$next_expiration_date\", `client_freeze_status` = \"$client_freeze_status\", `client_freeze_untill` = \"$client_freeze_untill\", `reffered_by` = \"$reffered_by\" WHERE `client_id` = \"$client_id\"";
                    // echo $update;
                    $stmt = $conn->prepare($update);
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
?>