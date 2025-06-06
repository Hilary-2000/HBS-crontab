<?php
	// mikrotik disable all active users who`s dates are due
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
		$main_result = $stmt->get_result();
		if ($main_result) {
			while ($rowed = $main_result->fetch_assoc()) {
				// database name
				$database_name = $rowed['organization_database'];
				echo $database_name."<br>";
				
				// get connection to the database and get the values of the users that are due that minute
				// Connect REMOTE
				$dbname = $database_name;
				$hostname = 'localhost';
				$dbusername = 'hillary';
				$dbpassword = "Francis=Son123";
				
				$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
				// Check connection
				if (mysqli_connect_errno()) {
					// die("Failed to connect to MySQL: " . mysqli_connect_error());
					// exit();
					continue;
				}
			
				// if connected
				if ($conn) {
					echo "We are connected!<br>";
			
					// get the clients who have their account expired
					$select = "SELECT * FROM `client_tables` WHERE `next_expiration_date` < ? AND `payments_status` = '1' AND `deleted` = '0'";
					$stmt = $conn->prepare($select);
					$today_date = date("YmdHis");
					$stmt->bind_param("s",$today_date);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						$in = 0;
						while ($row = $result->fetch_assoc()) {
							$in++;
							$wallet_amount = $row['wallet_amount'];
							$monthly_payment = $row['monthly_payment'];
							$min_amount = $row['min_amount'];
							$client_id = $row['client_id'];
							$client_contacts = $row['clients_contacts'];
			
							$next_expiry_date = null;
							$wallet_balance = 0;
							if ($wallet_amount >= $monthly_payment) {
								// add a month to the expiry date
								$next_expiry_date = date("YmdHis",strtotime("1 Month"));
								$wallet_balance = $wallet_amount - $monthly_payment;
			
								// update the client
								$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ? WHERE `client_id` = ?";
								$stmt = $conn->prepare($update);
								$client_status = 1;
								$stmt->bind_param("sss",$next_expiry_date,$wallet_balance,$row['client_id']);
								$stmt->execute();
			
								// activate the client
								activate_user($row,$rowed['organization_database']);
			
								// activate the user
								if ($row['client_status'] == 0) {
									$message_contents = get_sms($conn);
									$message = $message_contents[2]->messages[0]->message;
									if ($message) {
										$trans_amount = 0;
										$message = message_content($message,$client_id,$conn,$trans_amount);
										echo $message."<br>";
										// activate the user in the router
										// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been renewed. your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
										send_sms($conn,$client_contacts,$message,$client_id);
									}
									// activate_user($row,$rowed['organization_database']);
			
								}else {
									$message_contents = get_sms($conn);
									$message = $message_contents[2]->messages[1]->message;
									if ($message) {
										$trans_amount = 0;
										$message = message_content($message,$client_id,$conn,$trans_amount);
										echo $message."<br>";
										// send the user an SMS
										// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been extended for the next 30 days. Your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
										send_sms($conn,$client_contacts,$message,$client_id);
									}
									// activate the router
									// activate_user($row,$rowed['organization_database']);
								}
							}else {
								
								$minimum_pay = ceil($monthly_payment * ($min_amount / 100));
								
								// the minimum pay should not be less than 0
								if ($minimum_pay != 0) {
									// $minimum_pay = ceil($monthly_payment/4);
									if ($wallet_amount < $minimum_pay) {
										if ($row['client_status'] == 1) {
											// send them a message that they are deactivated
											$message_contents = get_sms($conn);
											$message = $message_contents[2]->messages[2]->message;
											if ($message) {
												$trans_amount = 0;
												$message = message_content($message,$client_id,$conn,$trans_amount);
												// send the user an SMS
												echo $message."<br>";
												send_sms($conn,$client_contacts,$message,$client_id);
											}
			
											// function to deactivate client
											deactivate_client($row,$rowed['organization_database']);
										}
									}else {
										// if the amount in the wallet is greater than the minimum
										// get the percentage of the amount and know till when 
										// will the amount take them for a 30 day period
										$percentage = ($wallet_amount/$monthly_payment) * 100;
										$dayed = round(($percentage/100) * 30,1);
										// check if it gives hours and days so that they days and hours are added
				
										// split to get days and hours
										$days = explode(".",$dayed);
										$time_period = "0 hours";
										if (count($days) > 0) {
											// means it has both days and hours
											$day = $days[0];
											$hours = round(($days[1]/10) * 24);
											$time_period = $day." days ".$hours." hours";
										}else {
											$time_period = $days[0]." days";
										}
										$NextExpDate1 = date("dS M Y H:i:s",strtotime($time_period));
										$NextExpDate = date("YmdHis",strtotime($time_period));
										$wallet_amount = 0;
										$account_status = 1;
										// the next date of expiry is already found
										// update the user next date of expiration and the user active status and the new wallet amount
										$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ? WHERE `client_id` = ?";
										$stmt = $conn->prepare($update);
										$stmt->bind_param("sss",$NextExpDate,$wallet_amount,$client_id);
										$stmt->execute();
				
										// send sms
										$message_contents = get_sms($conn);
										$message = $message_contents[2]->messages[1]->message;
										if ($message) {
											$trans_amount = 0;
											$message = message_content($message,$client_id,$conn,$trans_amount);
											echo $message."<br>";
											// send the user an SMS
											send_sms($conn,$client_contacts,$message,$client_id);
										}
										// activate the router
										activate_user($row,$rowed['organization_database']);
									}
								}else {
									// deactivate the client
									deactivate_client($row,$rowed['organization_database']);
								}
							}
						}
					}
			
					// select all client whose expiration date is not due and activate them if they are inactive
					$select = "SELECT * FROM `client_tables` WHERE `client_status` = '0' AND `payments_status` = '1' AND `next_expiration_date` > ?  AND `deleted` = '0'";
					$stmt = $conn->prepare($select);
					$stmt->bind_param("s",$today_date);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						// loop through the clients and activate them
						activate_user($row,$rowed['organization_database']);
						$change++;
					}
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
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
	
		// return $curl_data;
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
	
		// return $curl_data;
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
		$min_amount = 0;
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
				$min_amount = $row['min_amount'];
			}
		}

		// minimum payment
		$minimum_pay = $monthly_payment > 0 ? ceil($monthly_payment * ($min_amount / 100)) : "Null";


		// $minimum_pay = $monthly_payment > 0 ? $monthly_payment/4 : "Null";
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
		return $data;
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
?>