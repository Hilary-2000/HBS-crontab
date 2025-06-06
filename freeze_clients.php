<?php

	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// allow only certain ip addresses
	$allowed_ip_address = "172.71.178.94";
	$server_ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
	if (php_sapi_name() === 'cli') {
		// Running from CLI (Terminal)
		$server_ip_address = '172.71.178.94'; // Assume local execution
	} else {
		// Running from Web
		$server_ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
	}
	if ($allowed_ip_address !== $server_ip_address) {
		echo "Server ip address not allowed \"".$server_ip_address."\"";
		return 0;
	}

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
	
				// get connection local database and get the values of the users that are due that minute
				// LOCALE
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
				// get the users from the remote server that are to be deactivated and activate those that are activated
				if ($conn) {
					echo "Connected! $dbname<br>";
					// first unblock all clients
					// select client whose freezing date is due
					// if there are those that their account are still inactive activate them
					$date_today = date("YmdHis");
					// echo $date_today;
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_untill` < '$date_today' AND `client_freeze_status` = '1' AND `client_freeze_untill` != '00000000000000' AND `client_freeze_untill` != ''";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						echo json_encode($row)."<br>";
						// get the cleint status
						$payments_status = $row['payments_status'];
						$client_status = $row['client_status'];
						$client_id = $row['client_id'];
						$client_phone = $row['clients_contacts'];
						echo "<br>Frozen date due but still inactive $client_id!";
						$unblock = 1;
						
						// echo $unblock;
						if ($unblock == 1) {
							echo "Unblocked <br>";
							// unblock the user then locally update the clients payments status and account status
							// while remotely change the freezing status
							$update = "UPDATE `client_tables` SET `client_status` = '1', `payments_status` = '1',`client_freeze_status` = '0', `client_freeze_untill` = '' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								// SEND THE CLIENT A MESSAGE OF ACTIVATION
								$message_contents = get_sms($conn);
								if (count($message_contents) > 4) {
									$message = $message_contents[5]->messages;
									$send_message = "";
									for ($index=0; $index < count($message); $index++) {
										if ($message[$index]->Name == "account_unfrozen") {
											$send_message = $message[$index]->message;
										}
									}
									if (strlen($send_message) > 0 && $send_message != null) {
										$trans_amount = 0;
										$message = message_content($send_message,$client_id,$conn,$trans_amount);
										// echo $message."<br>";
										// send_sms($conn,$client_phone,$message,$client_id);
									}
								}else {
									echo  "<br>An error occured!";
								}
							}
							// NOW enable the user
							activate_user($row,$rowed['organization_database']);
						}
					}
					
					// then second freeze clients that are to be frozen
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_status` = '1' AND `client_freeze_untill` > '$date_today' AND `client_freeze_untill` != '00000000000000' AND `client_freeze_untill` != ''";
					// echo $select;
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						// echo json_encode($row)."<br>";
						// check clients that are frozen and need to be deactivated
						$payments_status = $row['payments_status'];
						$client_status = $row['client_status'];
						$client_id = $row['client_id'];
						$block = ($payments_status == "1" || $client_status == "1") ? 1 : 0;
						
						// block users
						if ($block == 1) {
							// block the user
							// update the locale database that the user is deactivated and also deactivate them from the mikrotik router
							$update = "UPDATE `client_tables` SET `client_status` = '0', `payments_status` = '0' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								echo "<br>Client deactivated";
								deactivate_client($row,$rowed['organization_database']);
							}else {
								echo "<br>error occured";
							}
						}

						$time = date("Hi");
						// deactivate those that are to be frozen incase they are activated manually after 6 hours!
						if ($time == "1230" || $time == "0030" || $time == "1830" || $time == "0630") {
							// block the user
							// update the locale database that the user is deactivated and also deactivate them from the mikrotik router
							$update = "UPDATE `client_tables` SET `client_status` = '0', `payments_status` = '0' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								echo "<br>Client deactivated";
								deactivate_client($row,$rowed['organization_database']);
							}else {
								echo "<br>error occured";
							}
						}
					}

					// FREEZE THE INDEFINATE clients
					$today = date("YmdHis");
					$select = "SELECT * FROM `client_tables` WHERE (`client_freeze_untill` > '".$today."' OR `client_freeze_untill` = '00000000000000') AND (`freeze_date` <= '".$today."'  OR `freeze_date` = '') AND `client_freeze_status` = '1'  AND client_status = '1'";
					// echo $select."<br>";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while($row = $result->fetch_assoc()){
							// echo json_encode($row)."<br>";
							$update = "UPDATE `client_tables` SET `client_freeze_status` = '1',  `date_changed` = '".date("YmdHis")."', `payments_status` = '0', `freeze_date` = '".date("YmdHis")."' WHERE `client_id` = '".$row['client_id']."'";
							$stmt = $conn->prepare($update);
							$stmt->execute();
			
							// deactivate the user
							deactivate_client($row,$rowed['organization_database']);
						}
					}
			
					// get all clients that are to be deactivated in the future
					$today = date("YmdHis");
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_untill` > '".$today."' AND `freeze_date` <= '".$today."' AND `client_freeze_status` = '0'";
					// echo $select;
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							// echo json_encode($row)."<br>";
							$update = "UPDATE `client_tables` SET `client_freeze_status` = '1',  `date_changed` = '".date("YmdHis")."', `payments_status` = '0', `freeze_date` = '".date("YmdHis")."' WHERE `client_id` = '".$row['client_id']."'";
							$stmt = $conn->prepare($update);
							$stmt->execute();
			
							// deactivate the user
							deactivate_client($row,$rowed['organization_database']);
			
							// SEND THE CLIENT A MESSAGE OF freezing
							$message_contents = get_sms($conn);
							if (count($message_contents) > 4) {
								$message = $message_contents[5]->messages;
								$send_message = "";
								for ($index=0; $index < count($message); $index++) {
									if ($message[$index]->Name == "account_frozen") {
										$send_message = $message[$index]->message;
									}
								}
								if (strlen($send_message) > 0 && $send_message != null) {
									$trans_amount = 0;
									$freeze_start = date_create($row['freeze_date']);
									$freeze_end = date_create($row['client_freeze_untill']);
									$diff=date_diff($freeze_start,$freeze_end);
									// $days = $diff->format("%R %a days");
									$day_frozen = $diff->format("%a");
									$freeze_dates = $row['client_freeze_untill'] == "00000000000000" ? "" : $row['client_freeze_untill'];
									$message = message_content($send_message,$row['client_id'],$conn,$trans_amount,$day_frozen,$row['freeze_date'],$freeze_dates);
									// echo "<br>".$message.json_encode($row);
									send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
								}
							}else {
								echo  "<br>An error occured!";
							}
						}
					}
				}
			}
		}
	}else{
		echo "Cannot connect to the main database";
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

    function activate_user($client_data,$database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/activate/".$client_data['client_id']."/".$database_name;
		// $url = "http://192.168.88.240:8000/activate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		return $curl_data;
    }
    function deactivate_client($client_data, $database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/deactivate/".$client_data['client_id']."/".$database_name;
		// $url = "http://192.168.88.240:8000/deactivate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		return $curl_data;
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
	function message_content($data,$user_id,$conn,$trans_amount,$freeze_days = null,$future_freeze_date = null,$freeze_date = null) {
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
		$minimum_pay = $monthly_payment > 0 ? $monthly_payment/4 : "Null";
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
		$data = str_replace("[min_amnt]", $minimum_pay,$data);
		$data = str_replace("[days_frozen]", $freeze_days." Day(s)",$data);
		$data = str_replace("[frozen_date]", date("D dS M Y",strtotime($future_freeze_date)),$data);
		$data = str_replace("[unfreeze_date]", ($freeze_date == "Indefinite" ? "Indefinite Date" : date("dS M Y \a\\t h:iA",strtotime($freeze_date))),$data);
		return $data;
	}
?>