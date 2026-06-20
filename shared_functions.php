<?php
    function activate_user($client_data,$database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/activate/".$client_data['client_id']."/".$database_name;
		// $url = "http://192.168.86.16:8000/activate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		// return $curl_data;
		// echo "Activated ".$client_data['client_name']."<br>";
    }

    function deactivate_client($client_data, $database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/deactivate/".$client_data['client_id']."/".$database_name;
		// $url = "http://192.168.86.16:8000/deactivate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		// return $curl_data;
		// echo "Deactivated ".$client_data['client_name']."<br>";
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
				if ($values != null) {
					$message_status = 1;
					foreach ($values as  $key => $value) {
						// echo $key;
						if ($key == "response-code") {
							if ($value == "200") {
								// if its 200 the message is sent delete the
								$message_status = 1;
							}
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
				$formatted_mobile = formatKenyanPhone($mobile);
				if (!$formatted_mobile) return;
				$postData = [
					"userid"     => $apikey,
					"password"     => $partnerID,
					"senderid"   => urlencode($shortcode),
					"msg"        => urlencode($message),
					"mobile"   => $formatted_mobile,
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
			}elseif ($sms_sender == "talksasa") {
				$url = "https://bulksms.talksasa.com/api/v3/sms/send";
				$phone = explode(",",$mobile);
				$phone = array_filter(array_map(function($num) {
					return formatKenyanPhone($num);
				}, $phone));
				if (empty($phone)) return;
				$phone = implode(",", $phone);

				$payload = [
					"recipient" => $phone,
					"sender_id" => $shortcode,
					"message" => $message,
					"type" => "plain", // or 'unicode' if sending special characters
				];

				$ch = curl_init($url);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FAILONERROR => false,
					CURLOPT_HTTPHEADER => [
						"Authorization: Bearer " . $apikey,
						"Accept: application/json",
						"Content-Type: application/json",
					],
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => json_encode($payload),
				]);

				$response = curl_exec($ch);
				$error = curl_error($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$message_status = 0;
				$response = json_decode($response, true);
				if (isset($response['status']) && $response['status'] == "success") {
					$message_status = 1;
					// echo "Message sent successfully.";
				}
			}elseif ($sms_sender == "blessedtexts") {
				$url = "https://sms.blessedtexts.com/api/sms/v1/sendsms";
				$phone = explode(",",$mobile);
				$phone = array_filter(array_map(function($num) {
					return formatKenyanPhone($num);
				}, $phone));
				if (empty($phone)) return;
				$phone = implode(",", $phone);

				$payload = [
					"phone" => $phone,
					"sender_id" => $shortcode,
					"message" => $message,
					"api_key" => $apikey
				];

				$ch = curl_init($url);
				curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FAILONERROR    => false,
					CURLOPT_HTTPHEADER     => [
						"Accept: application/json",
						"Content-Type: application/json",
					],
					CURLOPT_POST           => true,
					CURLOPT_POSTFIELDS     => json_encode($payload),
				]);

				$response = curl_exec($ch);
				$error    = curl_error($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$message_status = 0;
				$decoded = json_decode($response, true);
				if(isset($decoded['status_code']) && $decoded['status_code'] == "1000"){
					$message_status = 1;
					// echo "Message sent successfully.";
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
	function isJson($string)
	{
		return ((is_string($string) &&
			(is_object(json_decode($string)) ||
				is_array(json_decode($string))))) ? true : false;
	}

	function getPreferredChannel($conn) {
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'preferred_channel';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result && ($row = $result->fetch_assoc())) {
			return strtolower(trim($row['value']));
		}
		return 'sms';
	}

	function getWhatsAppKeys($conn) {
		$keys = [];
		foreach (['whatsapp_phone_id', 'whatsapp_access_token'] as $keyword) {
			$select = "SELECT `value` FROM `settings` WHERE `keyword` = ?;";
			$stmt = $conn->prepare($select);
			$stmt->bind_param("s", $keyword);
			$stmt->execute();
			$result = $stmt->get_result();
			$keys[] = ($result && ($row = $result->fetch_assoc())) ? $row['value'] : null;
		}
		return $keys; // [phone_id, access_token]
	}

	function getWhatsAppTemplate($internal_name) {
		global $hostname, $dbusername, $dbpassword;
		$conn_main = new mysqli($hostname, $dbusername, $dbpassword, 'mikrotik_cloud_manager');
		if (mysqli_connect_errno()) return null;
		$select = "SELECT `template_name`, `language`, `variables` FROM `whatsapp_automation_templates` WHERE `internal_name` = ? AND `is_active` = 1 AND `deleted` = '0'";
		$stmt = $conn_main->prepare($select);
		$stmt->bind_param("s", $internal_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$template = null;
		if ($result && ($row = $result->fetch_assoc())) {
			$template = [
				'template_name' => $row['template_name'],
				'language'      => $row['language'],
				'variables'     => json_decode($row['variables'], true) ?? [],
			];
		}
		$conn_main->close();
		return $template;
	}

	function resolveWhatsAppVariables($variables, $client_id, $conn, $extra = []) {
		$client = [];
		if ($client_id) {
			$select = "SELECT * FROM `client_tables` WHERE `client_id` = ?";
			$stmt = $conn->prepare($select);
			$stmt->bind_param("s", $client_id);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result && ($row = $result->fetch_assoc())) {
				$client = $row;
			}
		}
		$full_name = $client['client_name'] ?? '';
		$f_name    = $full_name ? ucfirst(lcfirst(explode(' ', $full_name)[0])) : '';
		$exp_raw   = $client['next_expiration_date'] ?? '';
		$exp_date  = $exp_raw ? date("dS-M-Y", strtotime($exp_raw)) . ' at ' . date("H:i:s", strtotime($exp_raw)) : '';
		$monthly   = $client['monthly_payment'] ?? 0;
		$min_pay   = $monthly > 0 ? 'Ksh ' . ceil($monthly / 4) : '';
		$map = [
			'client_name'           => $full_name,
			'client_f_name'         => $f_name,
			'acc_no'                => $client['client_account'] ?? '',
			'exp_date'              => $exp_date,
			'monthly_fees'          => 'Ksh ' . $monthly,
			'client_wallet'         => 'Ksh ' . ($client['wallet_amount'] ?? ''),
			'client_phone'          => $client['clients_contacts'] ?? '',
			'username'              => $client['client_username'] ?? '',
			'password'              => $client['client_password'] ?? '',
			'today'                 => date("dS-M-Y"),
			'now'                   => date("H:i:s"),
			'trans_amnt'            => 'Ksh ' . ($extra['trans_amount'] ?? '0'),
			'min_amnt'              => $min_pay,
			'days_frozen'           => ($extra['freeze_days'] ?? '') . ' Day(s)',
			'frozen_date'           => isset($extra['freeze_date']) ? date("D dS M Y", strtotime($extra['freeze_date'])) : '',
			'unfreeze_date'         => isset($extra['unfreeze_date'])
				? ($extra['unfreeze_date'] === 'Indefinite' ? 'Indefinite Date' : date("dS M Y \\a\\t h:iA", strtotime($extra['unfreeze_date'])))
				: '',
			'refferer_trans_amount' => 'Ksh ' . ($extra['refferer_trans_amount'] ?? ''),
			'refferer_name'         => $extra['refferer_name'] ?? '',
		];
		$params = [];
		foreach ($variables as $var) {
			$params[] = (string)($map[$var] ?? '');
		}
		return $params;
	}

	function send_whatsapp($conn, $phone_number, $message, $acc_id, $send_whatsapp = 0, $template_key = null, $extra = []) {
		if ($message == "" || $message == null || is_array($message)) {
			return;
		}
		$message_status = 0;
		if ($send_whatsapp == 1) {
			$wa_keys      = getWhatsAppKeys($conn);
			$phone_id     = $wa_keys[0];
			$access_token = $wa_keys[1];
			if ($phone_id && $access_token && $template_key) {
				$formatted = formatKenyanPhone($phone_number);
				if (!$formatted) return;
				$template = getWhatsAppTemplate($template_key);
				if ($template) {
					$params = resolveWhatsAppVariables($template['variables'], $acc_id, $conn, $extra);
					$components = [];
					if (!empty($params)) {
						$components[] = [
							"type"       => "body",
							"parameters" => array_map(fn($v) => ["type" => "text", "text" => $v], $params),
						];
					}
					$payload = [
						"messaging_product" => "whatsapp",
						"to"                => $formatted,
						"type"              => "template",
						"template"          => [
							"name"       => $template['template_name'],
							"language"   => ["code" => $template['language']],
							"components" => $components,
						],
					];
					$url = "https://graph.facebook.com/v20.0/{$phone_id}/messages";
					$ch  = curl_init($url);
					curl_setopt_array($ch, [
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_POST           => true,
						CURLOPT_POSTFIELDS     => json_encode($payload),
						CURLOPT_HTTPHEADER     => [
							"Authorization: Bearer {$access_token}",
							"Content-Type: application/json",
						],
						CURLOPT_SSL_VERIFYPEER => false,
					]);
					$response = curl_exec($ch);
					curl_close($ch);
					$decoded = json_decode($response, true);
					if (isset($decoded['messages'][0]['id'])) {
						$message_status = 1;
					}
				}
			}
		}
		$insert = "INSERT INTO `sms_tables` (`sms_content`,`date_sent`,`recipient_phone`,`sms_status`,`account_id`,`sms_type`) VALUES (?,?,?,?,?,?)";
		$stmt = $conn->prepare($insert);
		$now      = date("YmdHis");
		$sms_type = 3;
		$stmt->bind_param("ssssss", $message, $now, $phone_number, $message_status, $acc_id, $sms_type);
		$stmt->execute();
	}

	// ─── Email support ────────────────────────────────────────────────────────

	function getEmailSmtpSettings($conn) {
		$stmt = $conn->prepare("SELECT `value` FROM `settings` WHERE `keyword` = 'email_settings' LIMIT 1");
		if (!$stmt) return null;
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result && ($row = $result->fetch_assoc())) {
			$cfg = json_decode($row['value'], true);
			if (!empty($cfg['host']) && !empty($cfg['username']) && !empty($cfg['password'])) {
				return $cfg;
			}
		}
		return null;
	}

	function getOrgName($conn) {
		$res = $conn->query("SELECT DATABASE()");
		$db  = $res ? $res->fetch_row()[0] : '';
		if (!$db) return 'Your ISP';
		global $hostname, $dbusername, $dbpassword;
		$mgr = new mysqli($hostname, $dbusername, $dbpassword, 'mikrotik_cloud_manager');
		if (mysqli_connect_errno()) return 'Your ISP';
		$stmt = $mgr->prepare("SELECT organization_name FROM organizations WHERE organization_database = ? LIMIT 1");
		$stmt->bind_param('s', $db);
		$stmt->execute();
		$result = $stmt->get_result();
		$name   = ($result && ($row = $result->fetch_assoc())) ? $row['organization_name'] : 'Your ISP';
		$mgr->close();
		return $name;
	}

	function getEmailDefaults() {
		return [
			'new_client_welcome' => [
				'subject' => 'Welcome to [org_name] – Your Account is Ready',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Welcome to <strong>[org_name]</strong>! Your internet account has been created successfully.</p>
<p><strong>Account Details</strong><br>Account Number: <strong>[account_number]</strong><br>Monthly Plan: <strong>[monthly_fees]</strong><br>Expiry Date: <strong>[exp_date]</strong></p>
<p>If you have any questions, do not hesitate to contact us.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'payment_received' => [
				'subject' => 'Payment Received – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>We have received your payment of <strong>[trans_amount]</strong>. Thank you!</p>
<p><strong>Account Summary</strong><br>Account: <strong>[account_number]</strong><br>Wallet Balance: <strong>[wallet_balance]</strong><br>Expiry Date: <strong>[exp_date]</strong></p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'payment_below_minimum' => [
				'subject' => 'Payment Received – Below Minimum Threshold',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>We have received your payment of <strong>[trans_amount]</strong>. However, your payment is below the minimum required amount of <strong>[min_amount]</strong>.</p>
<p>Please top up the remaining balance to restore full service.</p>
<p><strong>Account:</strong> [account_number]<br><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'payment_wrong_account' => [
				'subject' => 'Payment Received for Unrecognised Account',
				'body'    => '<p>Dear Customer,</p>
<p>We have received a payment of <strong>[trans_amount]</strong> referencing an account number that does not exist in our system.</p>
<p>Please contact us so we can resolve this for you.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_renewed' => [
				'subject' => 'Your Account Has Been Renewed – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account has been renewed successfully.</p>
<p><strong>Account:</strong> [account_number]<br><strong>New Expiry Date:</strong> [exp_date]<br><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Enjoy uninterrupted internet access!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_extended' => [
				'subject' => 'Your Account Has Been Extended – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account has been extended.</p>
<p><strong>Account:</strong> [account_number]<br><strong>New Expiry Date:</strong> [exp_date]<br><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_deactivated' => [
				'subject' => 'Account Deactivated – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) has been deactivated.</p>
<p>To reactivate your account, please make a payment or contact us for assistance.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_frozen' => [
				'subject' => 'Your Account Has Been Frozen – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) has been frozen for <strong>[days_frozen]</strong>.</p>
<p>Your account will be automatically restored on <strong>[unfreeze_date]</strong>.</p>
<p>If you have any queries, please contact us.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_freeze_scheduled' => [
				'subject' => 'Upcoming Account Freeze Notice – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>This is a notice that your account (<strong>[account_number]</strong>) is scheduled to be frozen on <strong>[freeze_date]</strong> for <strong>[days_frozen]</strong>.</p>
<p>It will be automatically restored on <strong>[unfreeze_date]</strong>.</p>
<p>Contact us if you have any questions.</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'account_unfrozen' => [
				'subject' => 'Your Account Has Been Reactivated – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Great news! Your internet account (<strong>[account_number]</strong>) has been unfrozen and is now active.</p>
<p><strong>Expiry Date:</strong> [exp_date]</p>
<p>Welcome back!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'referral_commission' => [
				'subject' => 'Referral Commission Credited – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>A referral commission of <strong>[trans_amount]</strong> has been credited to your wallet.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Thank you for referring new clients to us!</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'payment_reminder_day_before' => [
				'subject' => 'Your Account Expires Tomorrow – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) expires <strong>tomorrow</strong>.</p>
<p>Please make a payment to avoid service interruption.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
			'payment_reminder_day_after' => [
				'subject' => 'Your Account Has Expired – [org_name]',
				'body'    => '<p>Dear <strong>[client_name]</strong>,</p>
<p>Your internet account (<strong>[account_number]</strong>) expired <strong>yesterday</strong>.</p>
<p>Please make a payment to restore your service.</p>
<p><strong>Wallet Balance:</strong> [wallet_balance]</p>
<p>Best regards,<br><strong>[org_name]</strong></p>',
			],
		];
	}

	function getEmailTemplate($internal_name, $conn) {
		$stmt = $conn->prepare("SELECT subject, html_body FROM email_templates WHERE name = ? LIMIT 1");
		if (!$stmt) return null;
		$stmt->bind_param('s', $internal_name);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result && ($row = $result->fetch_assoc())) {
			return ['subject' => $row['subject'], 'body' => $row['html_body']];
		}
		$defaults = getEmailDefaults();
		return $defaults[$internal_name] ?? null;
	}

	function resolveEmailVars($template, $client, $extra, $org_name) {
		$exp_raw  = $client['next_expiration_date'] ?? '';
		$exp_date = $exp_raw ? date("D jS M Y", strtotime($exp_raw)) . ' at ' . date("g:i:sA", strtotime($exp_raw)) : '';
		$map = [
			'[client_name]'    => $client['client_name'] ?? '',
			'[account_number]' => $client['client_account'] ?? '',
			'[monthly_fees]'   => 'Ksh ' . number_format($client['monthly_payment'] ?? 0),
			'[exp_date]'       => $exp_date,
			'[wallet_balance]' => 'Ksh ' . number_format($client['wallet_amount'] ?? 0),
			'[trans_amount]'   => 'Ksh ' . number_format($extra['trans_amount'] ?? 0),
			'[min_amount]'     => 'Ksh ' . number_format($extra['min_amount'] ?? 0),
			'[days_frozen]'    => ($extra['freeze_days'] ?? '0') . ' Day(s)',
			'[unfreeze_date]'  => $extra['unfreeze_date'] ?? '',
			'[freeze_date]'    => $extra['freeze_date'] ?? $extra['frozen_date'] ?? '',
			'[org_name]'       => $org_name,
			'[receipt]'        => '',
		];
		return str_replace(array_keys($map), array_values($map), $template);
	}

	function send_email($conn, $message, $acc_id, $send_flag = 0, $template_key = null, $extra = []) {
		// Look up client to get email address and data for variable resolution
		$client = [];
		if ($acc_id) {
			$stmt = $conn->prepare("SELECT * FROM client_tables WHERE client_id = ? LIMIT 1");
			if ($stmt) {
				$stmt->bind_param('s', $acc_id);
				$stmt->execute();
				$result = $stmt->get_result();
				if ($result && ($row = $result->fetch_assoc())) {
					$client = $row;
				}
			}
		}
		$email_address = $client['client_email'] ?? '';
		$message_status = 0;

		if ($send_flag == 1 && filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
			$org_name = getOrgName($conn);
			$tpl      = $template_key ? getEmailTemplate($template_key, $conn) : null;

			if ($tpl) {
				$subject  = resolveEmailVars($tpl['subject'], $client, $extra, $org_name);
				$html_body = resolveEmailVars($tpl['body'],    $client, $extra, $org_name);
			} else {
				$subject   = 'Message from ' . $org_name;
				$html_body = '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
			}

			$full_html = '<!DOCTYPE html><html><head><meta charset="utf-8"></head>'
				. '<body style="font-family:Arial,sans-serif;color:#333;line-height:1.6;max-width:600px;margin:0 auto;padding:20px;">'
				. $html_body
				. '</body></html>';

			global $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_from_name;
			$orgSmtp = getEmailSmtpSettings($conn);
			$host      = $orgSmtp['host']      ?? $smtp_host      ?? '';
			$port      = $orgSmtp['port']      ?? $smtp_port      ?? 587;
			$username  = $orgSmtp['username']  ?? $smtp_username  ?? '';
			$password  = $orgSmtp['password']  ?? $smtp_password  ?? '';
			$from_name = $orgSmtp['from_name'] ?? $smtp_from_name ?? '';
			$enc       = $orgSmtp['encryption'] ?? 'tls';

			// Skip sending if no SMTP credentials are configured
			if (empty($host) || empty($username) || empty($password)) {
				return;
			}

			require_once __DIR__ . '/phpmailer/src/Exception.php';
			require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
			require_once __DIR__ . '/phpmailer/src/SMTP.php';

			try {
				$mail = new PHPMailer\PHPMailer\PHPMailer(true);
				$mail->isSMTP();
				$mail->Host     = $host;
				$mail->SMTPAuth = true;
				$mail->Username = $username;
				$mail->Password = $password;
				if ($enc === 'ssl') {
					$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
				} elseif ($enc === 'none') {
					$mail->SMTPSecure = false;
					$mail->SMTPAuth   = false;
				} else {
					$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
				}
				$mail->Port = $port;
				$mail->setFrom($username, $from_name);
				$mail->addAddress($email_address);
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body    = $full_html;
				$mail->send();
				$message_status = 1;
			} catch (\Exception $_e) {
				// Silently fail so the cron job continues
			}
		}

		// Log to sms_tables (channel='email') whether or not sending succeeded
		$insert = "INSERT INTO `sms_tables` (`sms_content`,`date_sent`,`recipient_phone`,`sms_status`,`account_id`,`sms_type`,`channel`) VALUES (?,?,?,?,?,?,'email')";
		$stmt = $conn->prepare($insert);
		if ($stmt) {
			$now      = date("YmdHis");
			$sms_type = 3;
			$stmt->bind_param("ssssss", $message, $now, $email_address, $message_status, $acc_id, $sms_type);
			$stmt->execute();
		}
	}

	function send_message($conn, $phone_number, $message, $acc_id, $send_flag = 0, $template_key = null, $extra = []) {
		$channel = getPreferredChannel($conn);
		if ($channel === 'whatsapp') {
			send_whatsapp($conn, $phone_number, $message, $acc_id, $send_flag, $template_key, $extra);
		} elseif ($channel === 'email') {
			send_email($conn, $message, $acc_id, $send_flag, $template_key, $extra);
		} else {
			send_sms($conn, $phone_number, $message, $acc_id, $send_flag);
		}
	}
?>