<?php
session_start();
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// get connection local database and get the values of the users that are due that minute
    // MAIN DATABASE
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

    // CLIENT INFORMATION
    $client_id = "HLC101";

	// get connection remote database and get the values of the users that are due that minute
    // CLIENT DATABASE
	$dbname = 'my_isp_clients';
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

    if ($conn && $conn2) {
        // update the sms balances
        echo isset($_SESSION['PREV_BAL_LOCALE']) ? $_SESSION['PREV_BAL_LOCALE']:"Not set";

        // get balance remotely
        $select = "SELECT * FROM `sms_clients` WHERE `licence_acc_number` = '".$client_id."'";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $results = $stmt->get_result();
        $remote_sms_balance = 0;
        if ($results) {
            if ($row = $results->fetch_assoc()) {
                $remote_sms_balance = $row['sms_balance'];
            }
        }

        // get balance locally
        $select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_balance'";
        $stmt = $conn2->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $local_sms_balance = 0;
        if ($result) {
            if ($row = $result->fetch_assoc()) {
                $local_sms_balance = $row['value'];
            }
        }


        if (isset($_SESSION['PREV_BAL_REMOTE']) && isset($_SESSION['PREV_BAL_LOCALE'])) {
            // check if the previous remote balance is added
            if ($_SESSION['PREV_BAL_REMOTE'] < $remote_sms_balance) {
                // if the previous remote balance is added this means the user has recharged the sms
                // get the difference of the previous locale and the current locale
                $used_sms = $_SESSION['PREV_BAL_LOCALE'] - $local_sms_balance;
                $new_balance = $remote_sms_balance - $used_sms;
                $update = "UPDATE `sms_clients` SET `sms_balance` = '".$new_balance."' WHERE `licence_acc_number` = '".$client_id."'";
                $stmt = $conn->prepare($update);
                $stmt->execute();
                // set the balance for the local server
                $updates = "UPDATE `settings` SET `value` = '".$new_balance."' WHERE `keyword` = 'sms_balance'";
                $stmt = $conn2->prepare($updates);
                $stmt->execute();
                $_SESSION['PREV_BAL_REMOTE'] = $new_balance;
                $_SESSION['PREV_BAL_LOCALE'] = $new_balance;
            }else {
                $used_sms = $_SESSION['PREV_BAL_LOCALE'] - $local_sms_balance;
                $new_balance = $remote_sms_balance - $used_sms;
                // if the previous remote is still the same just deduct the current one and add update the new one
                $update = "UPDATE `sms_clients` SET `sms_balance` = '".$new_balance."' WHERE `licence_acc_number` = '".$client_id."'";
                $stmt = $conn->prepare($update);
                $stmt->execute();
                $_SESSION['PREV_BAL_REMOTE'] = $new_balance;
                $_SESSION['PREV_BAL_LOCALE'] = $new_balance;
            }
        }else {
            $_SESSION['PREV_BAL_REMOTE'] = $remote_sms_balance;
            $_SESSION['PREV_BAL_LOCALE'] = $local_sms_balance;
        }
        echo isset($_SESSION['PREV_BAL_LOCALE']) ? "<br>".$_SESSION['PREV_BAL_LOCALE']:"<br>Not set";
    }
?>