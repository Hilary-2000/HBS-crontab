<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// get connection to the database and get the values of the users that are due that minute
	// Connect REMOTE
	$dbname = 'my_isp';
	$hostname = '3.144.33.165';
	$dbusername = 'jose';
	$dbpassword = 'Francis=Son123';
	if(!isset($_SESSION)) {
		session_start(); 
	}
	$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// Check connection
	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
		exit();
	}
    // LOCAL
	$dbname = 'my_isp';
	$hostname = 'localhost';
	$dbusername = 'hilla';
	$dbpassword = 'Francis=Son123';
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
		// echo "Connected";
		// get the clients who have their account expired
		$select = "SELECT * FROM `client_tables` WHERE `next_expiration_date` < ? AND `payments_status` = '1'";
		$stmt = $conn->prepare($select);
		$today_date = date("YmdHis");
		// echo $today_date;
		$stmt->bind_param("s",$today_date);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			$change = 0;
			$deactivate = "";
			while ($row = $result->fetch_assoc()) {
				// fetch the clients data
				$client_id = $row['client_id'];
				$client_name = $row['client_name'];
				$client_contacts = $row['clients_contacts'];
				$client_status = $row['client_status'];
				$router_name = $row['router_name'];
				$client_network = $row['client_network'];
				$client_account = $row['client_account'];
				$min_amount = $row['min_amount'];
				// echo $row['client_id']." ".$row['client_name']." ".$row['router_name']." ".$row['client_network']."<br>";
				
				// check the wallet if it has less amount
				$wallet = $row['wallet_amount'];
				$monthly_payment = $row['monthly_payment'];
				if ($wallet >= $monthly_payment) {
					// the user can pay for the next month
					// add a month from today as their next day of expiration
					// the user is notified their account has been streched for another thirty days from today
					// when activating just activate the users that are inactive

					// deduct the wallet amount and update the user
					$new_wallet = $wallet - $monthly_payment;
					$account_status = 1;

					// add a month from today
					$dated = date("Y-m-d H:i:s");
					$date=date_create($dated);
					date_add($date,date_interval_create_from_date_string("1 Month"));
					$month_later = date_format($date,"YmdHis");
					$NextExpDate = date_format($date,"dS M Y H:i:s");

					// update the user next date of expiration and the user active status and the new wallet amount
					$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ?, `client_status` = ? WHERE `client_id` = ?";
					$stmt = $conn->prepare($update);
					$stmt->bind_param("ssss",$month_later,$new_wallet,$account_status,$client_id);
					$stmt->execute();

					

					// activate the user
					if ($client_status == 0) {
						echo $client_name." Activated<br>";
						$message_contents = get_sms($conn);
						$message = $message_contents[2]->messages[0]->message;
						if ($message) {
							$trans_amount = 0;
							$message = message_content($message,$client_id,$conn,$trans_amount);
							// echo $message;
							// activate the user in the router
							// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been renewed. your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
							send_sms($conn,$client_contacts,$message,$client_id);
						}
						activateUser($client_id);
						$change++;

					}else {
						echo $client_name." extended<br>";
						$message_contents = get_sms($conn);
						$message = $message_contents[2]->messages[1]->message;
						if ($message) {
							$trans_amount = 0;
							$message = message_content($message,$client_id,$conn,$trans_amount);
							// send the user an SMS
							// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been extended for the next 30 days. Your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
							send_sms($conn,$client_contacts,$message,$client_id);
						}
						// activate the router
						activateUser($client_id);
						$change++;
					}
				}else {
					// echo $wallet;
					// user cant pay for the next month
					// the user is notified that their account is de-activated
					// their account is de-activated and if their active status is deactivated (0)s
					// don`t deactivated in the mikrotik router. this will be checked after 30 minutes 
					// deactivate only (1)s
					
					$minimum_pay = ceil($monthly_payment * ($min_amount / 100));
					
					// the minimum pay should not be less than 0
					if ($minimum_pay != 0) {
						// $minimum_pay = ceil($monthly_payment/4);
						if ($wallet < $minimum_pay) {
							if ($client_status == 1) {
								// send them a message that they are deactivated
								$message_contents = get_sms($conn);
								$message = $message_contents[2]->messages[2]->message;
								if ($message) {
									$trans_amount = 0;
									$message = message_content($message,$client_id,$conn,$trans_amount);
									// send the user an SMS
									send_sms($conn,$client_contacts,$message,$client_id);
								}
								// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been deactivated.No enough funds in your wallet to make the payment.";
								// de_activate($client_id);
								$deactivate .= $client_id.",";
								$change++;
							}
							echo $client_name." deactivated<br>";
						}else {
							// if the amount in the wallet is greater than 100
							// get the percentage of the amount and know till when 
							// will the amount take them for a 30 day period
							$percentage = ($wallet/$monthly_payment) * 100;
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
							$wallet = 0;
							$account_status = 1;
							// the next date of expiry is already found
							// update the user next date of expiration and the user active status and the new wallet amount
							$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ?, `client_status` = ? WHERE `client_id` = ?";
							$stmt = $conn->prepare($update);
							$stmt->bind_param("ssss",$NextExpDate,$wallet,$account_status,$client_id);
							$stmt->execute();
	
							// send sms
							$message_contents = get_sms($conn);
							$message = $message_contents[2]->messages[1]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$client_id,$conn,$trans_amount);
								// echo $message;
								// send the user an SMS
								send_sms($conn,$client_contacts,$message,$client_id);
							}
							// activate the router
							activateUser($client_id);
							$change++;
							echo "Less than the monthly payment";
						}
					}
				}
			}
			if (strlen($deactivate) > 0) {
				$deactivate = substr($deactivate,0,(strlen($deactivate)-1));
				de_activate($deactivate,$conn2);
			}
		}

		// select all client whose expiration date is not due and activate them if they are inactive
		$select = "SELECT * FROM `client_tables` WHERE `client_status` = '0' AND `payments_status` = '1' AND `next_expiration_date` > ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$today_date);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			// loop through the clients and activate them
			$client_id = $row['client_id'];
			activateUser($client_id);
			$change++;
			echo "Activate ".$client_id."<br>";
		}
		if ($change > 0) {
			sync_client();
		}
		echo "END";
	}

	function activateUser($client_id){

		$curl_handle = curl_init();

        $url = "http://192.254.141.82:81/activate_user/".$client_id;
		// header("Location: ".$url."", true, 301);
        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);
		echo $curl_data;
	}

	function de_activate($client_id,$conn2){
		$curl_handle = curl_init();

        $url = "http://192.254.141.82:81/deactivate_user/".$client_id;
		// header("Location: ".$url."", true, 301);

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);
		echo $curl_data;
		$client_ids = explode(",",$client_id);
		// connect to the local server and deactivate the clients with those ids
		for ($i=0; $i < count($client_ids); $i++) { 
			$update = "UPDATE `client_tables` SET `client_status` = '0' WHERE `client_id` = ?";
			$stmt = $conn2->prepare($update);
			$stmt->bind_param("s",$client_ids[$i]);
			$stmt->execute();
		}

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
			$finalURL = "https://account.afrokatt.com/sms/api?action=send-sms&api_key=".urlencode($apikey)."&to=".$mobile."&from=".$shortcode."&sms=".urlencode($message)."&unicode=1";
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
	function sync_client(){
        // get ip
        // Initiate curl session in a variable (resource)
        $curl_handle = curl_init();

        $url = "http://192.254.141.82:81/crontab/syncclients.php";

        // Set the curl URL option
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        // This option will return data as a string instead of direct output
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        // Execute curl & store data in a variable
        $curl_data = curl_exec($curl_handle);

        curl_close($curl_handle);
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
?>
