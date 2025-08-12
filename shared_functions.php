<?php
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
	function send_sms($conn,$phone_number,$message,$acc_id, $send_sms = 0){
		if ($message == "" || $message == null || is_array($message)) {
			// do not send.
			return;
		}
		// get the sms api keys
		$sms_api_keys = getSMSKeys($conn);
		$apikey = $sms_api_keys[0];
		$partnerID = $sms_api_keys[1];
		$shortcode = $sms_api_keys[2];
		$sms_sender = $sms_api_keys[3];

		// send the sms
		$mobile = $phone_number; // Bulk messages can be comma separated
		$message_status = 0;
		if($send_sms == 1){
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
			}elseif ($sms_sender == "hostpinnacle") {
				// API URL
				$url = "https://smsportal.hostpinnacle.co.ke/SMSApi/send";
				
				// Prepare POST fields
				$postData = [
					"userid"     => $apikey,
					"password"     => $partnerID,
					"senderid"   => urlencode($shortcode),
					"msg"        => urlencode($message),
					"mobile"   => formatKenyanPhone($mobile),
					"sendMethod" => "quick",
					"msgType"    => "text",  // or 'unicode' if sending special characters
					"output"     => "json"   // Response format: json, xml, plain
				];
				// return $postData;
				
				// Initialize cURL
				$ch = \curl_init();
				\curl_setopt_array($ch, [
					CURLOPT_URL            => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_POST           => true,
					CURLOPT_POSTFIELDS     => $postData,
					CURLOPT_SSL_VERIFYPEER => false
				]);
				$response = \curl_exec($ch);
				\curl_close($ch);
				$message_status = 1;
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
	function isJson($string)
	{
		return ((is_string($string) &&
			(is_object(json_decode($string)) ||
				is_array(json_decode($string))))) ? true : false;
	}
?>