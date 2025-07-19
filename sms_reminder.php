<?php
/**
 * This file is used to send sms notifications to the owner if the sms used is more than the defined figure.
 * The owner will get messages after a defined usage is exceeded and a preceeding three time notification after a number of messages are 
 * used after the limit
 * 
 * Example you are to get notified of used messages when they are more than 100
 * you will get notified again after 10 messages three time at the 110, 120 and the 120 mark to emphasize the message
 */
	
	// allowed ip address
	include "allowed_ip.php";

//  connect to the database
	// Connect REMOTE
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
        /**
         * Modify your variabled from here
         */
        $date = date("Ymd");
        $used_limit = 1;
        $used_limit_1 = 2;
        $used_limit_2 = 3;
        $used_limit_3 = 4;
        $phones_to_notify = ["0720268519","0717748569","0743551250"];
        $repeat_number = 0;
        $intervals = 10;

        // get the sms used that day
        $select = "SELECT COUNT(*) AS 'Total' FROM `sms_tables` WHERE `date_sent` LIKE '".$date."%'";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $Total = 0;
        if ($result) {
            if ($row = $result->fetch_assoc()) {
                $Total = $row['Total'];
            }
        }

        // go to settings for the repeat value
        $select = "SELECT * FROM `settings` WHERE `keyword` = 'repeat_value'";
        $stmt = $conn->prepare($select);
        $stmt->execute();
        $result = $stmt->get_result();
        $repeat_holder = "";
        if ($result) {
            if ($row = $result->fetch_assoc()) {
                $repeat_holder = $row['value'];
                $r_holder = json_decode($repeat_holder);

                // if the date is not for today uodate the record
                if (date("Ymd") == $r_holder->date) {
                    $repeat_number = $r_holder->repeat_value;
                }else {
                    // update the table
                    $repeat = new stdClass();
                    $repeat->date = date("Ymd");
                    $repeat->repeat_value = 0;


                    // update the repeat value
                    $update = "UPDATE `settings` SET `value` = ? WHERE `keyword` = 'repeat_value'";
                    $stmt = $conn->prepare($update);
                    $val = json_encode($repeat);
                    $stmt->bind_param("s",$val);
                    $stmt->execute();
                }
            }
        }

        if (strlen($repeat_holder) == 0) {
            $repeat = new stdClass();
            $repeat->date = date("Ymd");
            $repeat->repeat_value = 0;

            $insert = "INSERT INTO `settings` (`keyword`,`value`,`status`,`date_changed`,`deleted`) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($insert);
            $val_1 = "repeat_value";
            $val_2 = json_encode($repeat);
            $val_3 = "1";
            $val_4 = date("YmdHis");
            $val_5 = "0";
            $stmt->bind_param("sssss",$val_1,$val_2,$val_3,$val_4,$val_5);
            $stmt->execute();
        }

        if ($Total >= $used_limit && $repeat_number == 0) {
            $message = "Kind reminder! Your SMS usage limit has been reached. Total used SMSs are ".$Total.".";
            
            // send the sms
            $phones = "";
            for ($index=0; $index < count($phones_to_notify); $index++) {
                $phones.=$phones_to_notify[$index].",";
            }

            // change to MR G`s NO.
            $phones = strlen($phones) > 0 ? substr($phones,0,strlen($phones)-1) : "0720268519";
            send_sms($conn,$phones,$message,0);
            $repeat_number++;

            $repeat = new stdClass();
            $repeat->date = date("Ymd");
            $repeat->repeat_value = $repeat_number;


            // update the repeat value
            $update = "UPDATE `settings` SET `value` = ? WHERE `keyword` = 'repeat_value'";
            $stmt = $conn->prepare($update);
            $val = json_encode($repeat);
            $stmt->bind_param("s",$val);
            $stmt->execute();
        }elseif ($Total >= $used_limit_1 && $repeat_number == 1){
            $message = "Kind reminder! Your SMS usage limit was reached. Total used SMSs are ".$Total.".";
            
            // send the sms
            $phones = "";
            for ($index=0; $index < count($phones_to_notify); $index++) {
                $phones.=$phones_to_notify[$index].",";
            }

            // change to MR G`s NO.
            $phones = strlen($phones) > 0 ? substr($phones,0,strlen($phones)-1) : "0720268519";
            send_sms($conn,$phones,$message,0);
            $repeat_number++;

            $repeat = new stdClass();
            $repeat->date = date("Ymd");
            $repeat->repeat_value = $repeat_number;


            // update the repeat value
            $update = "UPDATE `settings` SET `value` = ? WHERE `keyword` = 'repeat_value'";
            $stmt = $conn->prepare($update);
            $val = json_encode($repeat);
            $stmt->bind_param("s",$val);
            $stmt->execute();
            
        }elseif ($Total >= $used_limit_2 && $repeat_number == 2){
            $message = "Kind reminder! Your SMS usage limit was reached. Total used SMSs are ".$Total.".";
            
            // send the sms
            $phones = "";
            for ($index=0; $index < count($phones_to_notify); $index++) {
                $phones.=$phones_to_notify[$index].",";
            }

            // change to MR G`s NO.
            $phones = strlen($phones) > 0 ? substr($phones,0,strlen($phones)-1) : "0720268519";
            send_sms($conn,$phones,$message,0);
            $repeat_number++;

            $repeat = new stdClass();
            $repeat->date = date("Ymd");
            $repeat->repeat_value = $repeat_number;


            // update the repeat value
            $update = "UPDATE `settings` SET `value` = ? WHERE `keyword` = 'repeat_value'";
            $stmt = $conn->prepare($update);
            $val = json_encode($repeat);
            $stmt->bind_param("s",$val);
            $stmt->execute();
            
        }elseif ($Total >= $used_limit_3 && $repeat_number == 3){
            $message = "Kind reminder! Your SMS usage limit was reached. Total used SMSs are ".$Total.".";
            
            // send the sms
            $phones = "";
            for ($index=0; $index < count($phones_to_notify); $index++) {
                $phones.=$phones_to_notify[$index].",";
            }

            // change to MR G`s NO.
            $phones = strlen($phones) > 0 ? substr($phones,0,strlen($phones)-1) : "0720268519";
            send_sms($conn,$phones,$message,0);
            $repeat_number++;

            $repeat = new stdClass();
            $repeat->date = date("Ymd");
            $repeat->repeat_value = $repeat_number;


            // update the repeat value
            $update = "UPDATE `settings` SET `value` = ? WHERE `keyword` = 'repeat_value'";
            $stmt = $conn->prepare($update);
            $val = json_encode($repeat);
            $stmt->bind_param("s",$val);
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
?>