<?php
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

    if ($conn) {
        // loop through each transaction and get if its assigned or not
        $select = "SELECT * FROM `transaction_tables` WHERE `transaction_status` = '0'";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // get the clients details when their account number is present
                $acc_no = $row['transaction_account'];
                $trans_id = $row['transaction_id'];
                $amount_paid = $row['transacion_amount'];
                $client_present = getClient($conn,$acc_no);
                if (count($client_present) > 0) {
                    $client_id = $client_present['client_id'];
                    $client_wallet = $client_present['wallet_amount'];
                    $client_contact = $client_present['clients_contacts'];
                    // if the client is present update the transaction account id and the transaction status to assigned
                    $update = "UPDATE `transaction_tables` SET `transaction_status` = '1', `transaction_acc_id` = '".$client_id."' WHERE `transaction_id` = '".$trans_id."'";
                    // echo $update;
                    $stmt = $conn->prepare($update);
                    $stmt->execute();
                    // get the smount paid and update the clients wallet
                    $new_wallet = $client_wallet+$amount_paid;
                    $update = "UPDATE `client_tables` SET `wallet_amount` = '".$new_wallet."' WHERE `client_id` = '".$client_id."'";
                    $stmt = $conn->prepare($update);
                    $stmt->execute();
                    $message = get_sms($conn);
                    $message = $message[1]->messages[1]->message;
                    if ($message) {
                        $message = message_content($message,$client_id,$conn,$amount_paid);
                        // echo $message;
                        // send the message to the client
                        send_sms($conn,$client_contact,$message,$client_id);
                    }
                }
            }
        }
    }
    function getClient($conn,$client_id){
        $select = "SELECT * FROM `client_tables` WHERE `client_account` = '$client_id'";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $row=$result->fetch_assoc();
        }
        return [];
    }

	function get_sms($conn){
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'Messages';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				return json_decode($row['value']);
			}
		}
	}

	function message_content($data,$user_id,$conn,$trans_amount) {
		$exp_date = date("dS-M-Y");
		$reg_date = date("dS-M-Y");
		$monthly_payment = 0;
		$full_name = "Null";
		$f_name = "Null";
		$address = "Null";
		$internet_speeds = "Null";
		$contacts = "Null";
		$account_no = "Null";
		$wallet = "Null";
		$username = "Null";
		$password = "Null";
		$trans_amount = isset($trans_amount)?$trans_amount:"Null";
		// var date = new Date();
		$select = "SELECT * FROM `client_tables` WHERE `client_id` = $user_id";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				$exp_date = $row['next_expiration_date'];
				$reg_date = $row['clients_reg_date'];
				$monthly_payment = $row['monthly_payment'];
				$full_name = $row['client_name'];
				$f_name = ucfirst(lcfirst((explode(" ",$full_name)[0])));
				$address = $row['client_address'];
				$internet_speeds = $row['max_upload_download'];
				$contacts = $row['clients_contacts'];
				$account_no = $row['client_account'];
				$wallet = $row['wallet_amount'];
				$username = $row['client_username'];
				$password = $row['client_password'];
			}
		}
		$today = date("dS-M-Y");
		$now = date("H:i:s");
		$time = $exp_date;
		$exp_date = date("dS-M-Y",strtotime($exp_date));
		$exp_time = date("H:i:s",strtotime($time));
		$reg_date = date("dS-M-Y",strtotime($reg_date));
		$data = str_replace("[client_name]", $full_name, $data);
		$data = str_replace("[client_f_name]", $f_name, $data);
		$data = str_replace("[client_addr]", $address, $data);
		$data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
		$data = str_replace("[reg_date]", $reg_date, $data);
		$data = str_replace("[int_speeds]", $internet_speeds, $data);
		$data = str_replace("[monthly_fees]", "Ksh ".$monthly_payment, $data);
		$data = str_replace("[client_phone]", $contacts, $data);
		$data = str_replace("[acc_no]", $account_no, $data);
		$data = str_replace("[client_wallet]", "Ksh ".$wallet, $data);
		$data = str_replace("[username]", $username, $data);
		$data = str_replace("[password]", $password, $data);
		$data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
		$data = str_replace("[today]", $today, $data);
		$data = str_replace("[now]", $now,$data);
		return $data;
	}
    
	function send_sms($conn,$phone_number,$message,$acc_id){
		// get the sms api keys
		$sms_api_keys = getSMSKeys($conn);
		$apikey = $sms_api_keys[0];
		$partnerID = $sms_api_keys[1];
		$shortcode = $sms_api_keys[2];

		// send the sms
		$mobile = $phone_number; // Bulk messages can be comma separated

		$finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
		$ch = \curl_init();
		\curl_setopt($ch, CURLOPT_URL, $finalURL);
		\curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		\curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = \curl_exec($ch);
		\curl_close($ch);
		$res = json_decode($response);
		// return $res;
		$message_status = 0;
		$values = $res->responses[0];
		// return $values;
		foreach ($values as  $key => $value) {
			// echo $key;
			if ($key == "response-code") {
				if ($value == "200") {
					// if its 200 the message is sent delete the
					$message_status = 1;
				}
			}
		}

		// save the message details in the database
		$insert = "INSERT INTO `sms_tables` (`sms_content`,`date_sent`,`recipient_phone`,`sms_status`,`account_id`,`sms_type`) VALUES (?,?,?,?,?,?)";
		$stmt = $conn->prepare($insert);
		$now = date("YmdHis");
		$sms_type = 2;
		$stmt->bind_param("ssssss",$message,$now,$phone_number,$message_status,$acc_id,$sms_type);
		$stmt->execute();
	}
	function getSMSKeys($conn){
		// get the sms keys
		$sms_api_keys = [];
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_api_key';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_partner_id';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_shortcode';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		return $sms_api_keys;
	}
?>