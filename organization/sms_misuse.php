<?php

    
	// mikrotik disable all active users who`s dates are due

use PHPMailer\PHPMailer\PHPMailer;

	date_default_timezone_set('Africa/Nairobi');

	// allowed ip address
	include __DIR__ ."/../allowed_ip.php";

	// connect database
	include __DIR__ ."/../db_connect.php";
	
	// shared functions
	// include __DIR__ ."../shared_functions.php";

    // number of sms a day
    $no_of_times = 5;

    // email
    $email = ["gathure@gmail.com", "hilaryme45@gmail.com"];

    // phone_number
    $phone_number = "0720268519,0743551250";

    if ($conn1){
        $select = "SELECT * FROM `organizations`";
        $stmt = $conn1->prepare($select);
        $stmt->execute();
        $main_result = $stmt->get_result();
        $sms_data = [];
        if ($main_result) {
            while ($rowed = $main_result->fetch_assoc()) {
                // database name
				$database_name = $rowed['organization_database'];
                $conn2 = new mysqli($hostname, $dbusername, $dbpassword, $database_name);
				if (mysqli_connect_errno()) {
					continue;
				}
                $today = date("Ymd");
                $select = "SELECT sms_tables.account_id, COUNT(sms_tables.account_id) AS 'total_sent', client_tables.client_name, client_tables.client_account FROM sms_tables LEFT JOIN client_tables ON client_tables.client_id = sms_tables.account_id WHERE sms_tables.account_id != 0 AND sms_tables.date_sent LIKE '".$today."%' GROUP BY sms_tables.account_id HAVING COUNT(*) >= ".$no_of_times.";";
                $statement = $conn2->prepare($select);
                $statement->execute();
                $result = $statement->get_result();
                $clients = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        array_push($clients, $row);
                    }
                }
                if (count($clients) > 0) {
                    $rowed['organization_client'] = $clients;
                }else{
                    $rowed['organization_client'] = [];
                }
                array_push($sms_data, $rowed);
            }
        }
        $organization_data = [];
        $organization_data['sms_data'] = $sms_data;
        $organization_data['date'] = date("Ymd");


        // read data stored in the cookie<?php
        $filename = __DIR__ .'/../cookie';
        $defaultContent = json_encode($organization_data, JSON_PRETTY_PRINT);

        $file = fopen($filename, 'a+'); // 'a+' opens for reading and writing, creates if doesn't exist
        $read_organization_data = "[]";
        if ($file) {
            // Check if file is empty (newly created)
            if (filesize($filename) === 0) {
                // Write default content
                fwrite($file, $defaultContent);
            } else {
                // Read existing content
                $content = fread($file, filesize($filename));
                $read_organization_data = isJson($content) ? json_decode($content, true) : [];
            }
            fclose($file);
        }


        // compare what Ive read to what I have found
        $information_to_share = [];
        if(isset($read_organization_data['date']) && $read_organization_data['date'] == $organization_data['date']){
            // compare the sms data
            $read_sms_data = $read_organization_data['sms_data'];
            $found_sms_data = $organization_data['sms_data'];

            // go through each found organization
            foreach ($found_sms_data as $key => $value) {
                // get their clients
                $found_clients = $value['organization_client'];
                $changed_organizations = $value;
                $changed_organizations['organization_client'] = [];
                // go through each client and see how many messages they have received
                foreach ($found_clients as $key2 => $value2) {
                    // go through the read organization data
                    $present = false;
                    foreach ($read_sms_data as $key3 => $value3) {
                        if($value3['organization_id'] != $value['organization_id']){
                            // skip this organization
                            continue;
                        }

                        // if not, go through the read clients and see whose number has increased
                        $read_clients = $value3['organization_client'];
                        foreach($read_clients as $key4 => $value4){
                            if($value4['account_id'] == $value2['account_id']){
                                $present = true;
                                if($value2['total_sent'] > $value4['total_sent']){
                                    // send sms and email
                                    array_push($changed_organizations['organization_client'], $value2);
                                }
                            }
                        }
                    }

                    // if a new client has reached the limit add them and notify the system admin
                    if(!$present){
                        // send sms and email
                        array_push($changed_organizations['organization_client'], $value2);
                    }
                }
                if(count($changed_organizations['organization_client']) > 0){
                    array_push($information_to_share, $changed_organizations);
                }
            }

            // update the cookie file with the new data
            $file = fopen($filename, 'w'); // 'w' opens for writing, truncating the file to zero length
            if ($file) {
                // Write the updated content
                fwrite($file, json_encode($organization_data, JSON_PRETTY_PRINT));
                fclose($file);
            }
        }else{
            // update the cookie file with the new data
            $file = fopen($filename, 'w'); // 'w' opens for writing, truncating the file to zero length
            if ($file) {
                // Write the updated content
                fwrite($file, json_encode($organization_data, JSON_PRETTY_PRINT));
                fclose($file);
            }
            $found_sms_data = $organization_data['sms_data'];
            // go through each found organization
            foreach ($found_sms_data as $key => $value) {
                // count organization clients
                if(count($value['organization_client']) > 0){
                    array_push($information_to_share, $value);
                }
            }
        }

        // echo json_encode($information_to_share);
        // exit();

        // proceed and create an sms and an email
        $email_data = null;
        $sms_data = null;
        if(count($information_to_share) > 0){
            $sms_data = "ALERT! SMS LIMIT REACHED!!\r";
            $email_data = "<h2>SMS Sent Report</h2>";
            $email_data .= "<p>Dear Admin,</p>";
            $email_data .= "<p>Here is the report of SMS sent to clients who have exceeded the daily limit:</p>";
            $email_data .= "<p>Date: ".date("dS M Y")."</p>";
            $email_data .= "<p>Time: ".date("h:i A")."</p>";
            $email_data .= "<p>Details:</p>";
            foreach ($information_to_share as $key => $value) {
                // get the organization name
                $sms_data .= "Organization: \"".ucwords(strtolower($value['organization_name']))."\" has ".count($value['organization_client'])." client(s),\r";
                $organization_name = ucwords(strtolower($value['organization_name']));
                $email_data .= "<h3>Organization: ".$organization_name."</h3>";
                $email_data .= "<table border='1' cellpadding='5' cellspacing='0'>";
                $email_data .= "<tr><th>No.</th><th>Client Name</th><th>Client Account</th><th>Total Sent</th></tr>";
                foreach ($value['organization_client'] as $key2 => $value2) {
                    $email_data .= "<tr>";
                    $email_data .= "<td>".($key2+1)."</td>";
                    $email_data .= "<td>".ucwords(strtolower($value2['client_name']))."</td>";
                    $email_data .= "<td>".$value2['client_account']."</td>";
                    $email_data .= "<td>".$value2['total_sent']."</td>";
                    $email_data .= "</tr>";
                }
                $email_data .= "</table><br>";
            }
            $email_data .= "<p>Note: The above clients have exceeded the ".$no_of_times." SMS limit for today.</p>";
            $email_data .= "<p>Please take necessary action.</p>";
            $email_data .= "<p>Thank you!</p>";
            $email_data .= "<p>Best Regards,<br>HBS Monitoring system</p>";
            $sms_data .= " who have exceeded the ".$no_of_times." SMS limit for today.\r";
            $sms_data .= "Check hypbits@gmail.com inbox for more information.\r";
        }else{
            echo "No changes found";
        }
        echo $email_data."<br><hr>".$sms_data;
        if ($email_data && $sms_data) {
            // proceed and also send sms
            send_sms($conn1,$phone_number,$sms_data,"0");

            echo "sending!<br>";
            require(__DIR__.'/../phpmailer/src/Exception.php');
            require(__DIR__.'/../phpmailer/src/PHPMailer.php');
            require(__DIR__.'/../phpmailer/src/SMTP.php');
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $email_username = "hilaryme8@gmail.com";
            $mail->Username = $email_username;
            $mail->Password = "lqzgdosbgovhezjw";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587;
            $sender_name = "Hypbits Monitoring System";
            
            $mail->setFrom($email_username,$sender_name);

            $message = $email_data;

            // SEND TO PRIMARY PARENT
            for ($index=0; $index < count($email); $index++) { 
                $mail->addAddress($email[$index]);
            }

            // allow HTML
            $mail->isHTML(true);

            // SET SUBJECT
            $mail->Subject = "ALERT!! SMS LIMIT REACHED!!";

            // SET BODY
            $mail->Body = $message;
            try{
                $mail->send();
                echo "sent!";
            }catch(Exception $e){
                echo "Email could not be sent to $e.";
            }
        }
    }



    function send_sms($conn,$phone_number,$message,$acc_id){
        if ($message == "" || $message == null || is_array($message)) {
            // do not send.
            return;
        }
        // get the sms api keys
        $sms_api_keys = getSMSKeys($conn);
        $apikey = $sms_api_keys[0];
        $partnerID = $sms_api_keys[1];
        $shortcode = $sms_api_keys[2];
        $sms_sender = "celcom";

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
        $message_status = 1;

        // save the message details in the database
        $insert = "INSERT INTO `sms_tables` (`sms_content`,`date_sent`,`recipient_phone`,`sms_status`,`account_id`,`sms_type`) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($insert);
        $now = date("YmdHis");
        $sms_type = 2;
        $stmt->bind_param("ssssss",$message,$now,$phone_number,$message_status,$acc_id,$sms_type);
        $stmt->execute();
    }
    
    function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
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
        // $select = "SELECT * FROM `settings` WHERE `keyword` = 'sms_sender';";
        // $stmt = $conn->prepare($select);
        // $stmt->execute();
        // $result = $stmt->get_result();
        // if ($result) {
        //     if ($row = $result->fetch_assoc()) {
        //         // get the api key
        //         array_push($sms_api_keys,$row['value']);
        //     }
        // }
        array_push($sms_api_keys,"celcom");
        return $sms_api_keys;
    }
?>