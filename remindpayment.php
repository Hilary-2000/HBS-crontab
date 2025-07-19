<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');
	
	// allowed ip address
	include "allowed_ip.php";
	
	// loop through every organization database to activate and deactivate clients
	$dbname = "mikrotik_cloud_manager";
	$hostname = "localhost";
	$dbusername = 'hillary';
	$dbpassword = "Francis=Son123";
	// $dbusername = 'root';
	// $dbpassword = "";
	$conn1 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// Check connection
	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
		exit();
	}

	if ($conn1) {
		$select = "SELECT `organization_database` FROM `organizations` GROUP BY `organization_database`;";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$result_1 = $stmt->get_result();
		if ($result_1){
			while ($rowed = $result_1->fetch_assoc()) {
				// get connection to the database and get the values of the users that are due that minute
				$dbname = $rowed['organization_database'];
				$hostname = 'localhost';
				$dbusername = 'hillary';
				$dbpassword = 'Francis=Son123';
				
				$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
				// Check connection
				if (mysqli_connect_errno()) {
					die("Failed to connect to MySQL: " . mysqli_connect_error());
					exit();
				}
			
				// check if the connection is valid
				if ($conn) {
					// echo "Connected";
					// date tommorow
					$tommorow = date("Ymd",strtotime("1 day"));
					// date today
					$today = date("Ymd");
					// date one day after
					$yesterday = date("Ymd",strtotime("-1 day"));
					// echo $yesterday." ".$today." ".$tommorow;
			
					// get the users that are to pay yesterday
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$yesterday%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[2]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
			
					// get the users that are to pay today
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$today%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[1]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
					
					// get the users that are to pay tommorow
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$tommorow%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[0]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
	}

	function getSMSKeys($conn){
		// get the sms keys
		$sms_api_keys = [];
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_api_key';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_partner_id';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_shortcode';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_sender';";
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
	function send_sms($conn,$phone_number,$message,$acc_id){
		// get the sms api keys
		$sms_api_keys = getSMSKeys($conn);
		$apikey = $sms_api_keys[0];
		$partnerID = $sms_api_keys[1];
		$shortcode = $sms_api_keys[2];
		$sms_sender = $sms_api_keys[3];

		// send the sms
		$mobile = $phone_number; // Bulk messages can be comma separated

		if($sms_sender == "celcom"){
			$finalURL = "https://isms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
			$ch = \curl_init();
			\curl_setopt($ch, CURLOPT_URL, $finalURL);
			\curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			\curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = \curl_exec($ch);
			\curl_close($ch);
			$res = json_decode($response);
			// return $res;
			// echo json_encode($mobile)." pen <br>";
			$message_status = 0;
			$values = $res->responses[0];
			foreach ($values as  $key => $value) {
				// echo $key;
				if ($key == "response-code") {
					if ($value == "200") {
						// if its 200 the message is sent delete the
						$message_status = 1;
					}
				}
			}
		}elseif($sms_sender == "afrokatt"){
			$finalURL = "https://account.afrokatt.com/sms/api?action=send-sms&api_key=".urlencode($apikey)."&to=".$mobile."&from=".$shortcode."&sms=".urlencode($message);
			$ch = \curl_init();
			\curl_setopt($ch, CURLOPT_URL, $finalURL);
			\curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			\curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$response = \curl_exec($ch);
			\curl_close($ch);
			$res = json_decode($response);
			$values = $res->code;
			if (isset($res->code)) {
				if($res->code == "200"){
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
		return [];
	}
	function message_content($data,$user_id,$conn,$trans_amount,$freeze_days = null, $freeze_date = null) {
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
		$data = str_replace("[days_frozen]", $freeze_days." Day(s)",$data);
		$data = str_replace("[unfreeze_date]", date("dS M Y \a\\t h:iA",strtotime($freeze_date)),$data);
		return $data;
	}
?>
