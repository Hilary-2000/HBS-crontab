<?php
/**
 * THIS SCRIPT IS USED TO ACTIVATE AND
 * DEACTIVATE ORGANIZATIONS WHEN THEIR 
 * EXPIRY DATES IS REACHED AND ACTIVATED
 * WHEN THEY MAKE PAYMENTS.
 */

include "../allowed_ip.php";

date_default_timezone_set('Africa/Nairobi');

#CONSTANTS
$months_last_active = "-3 months";

$batch_of_client = 50; //number of clients to charge

$free_clients = 0; // number of clients to charge free


// connect database
include "../db_connect.php";

// PROCEED AND GET THE ORGANIZATIONS TO ACTIVATE AND DEACTIVATED

$conn = $conn1;
$sql = "SELECT * FROM organizations";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while($row = $result->fetch_object()){
        // send reminder to those their expiration date is tommorrow.
        $tommorow_start = (date("Ymd", strtotime("1 day"))."000000")*1;
        $tommorow_end = (date("Ymd", strtotime("1 day"))."235959")*1;
        if ($row->expiry_date >= $tommorow_start && $row->expiry_date <= $tommorow_end) {
              $message = get_sms($conn, "Remind_payment", "day_before");
              $message = message_content($message,$row->organization_id,$conn);
              
              // send_sms
              send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
            // echo $message;
        }


        // send reminder to those their expiration date is today.
        $today_start = date("Ymd")."000000"*1;
        $today_end = date("Ymd")."235959"*1;
        if ($row->expiry_date >= $today_start && $row->expiry_date <= $today_end) {
              $message = get_sms($conn, "Remind_payment", "de_day");
              $message = message_content($message,$row->organization_id,$conn);
              
              // send_sms
              send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
            // echo $message;
        }

        // send reminder to those their expiration date is yesterday.
        $day_after_start = (date("Ymd", strtotime("-1 day"))."000000")*1;
        $day_after_end = (date("Ymd", strtotime("-1 day"))."235959")*1;
        if ($row->expiry_date >= $day_after_start && $row->expiry_date <= $day_after_end) {
              $message = get_sms($conn, "Remind_payment", "day_after");
              $message = message_content($message,$row->organization_id,$conn);
              
              // send_sms
              send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
            // echo $message;
        }
    }
}


function message_content($data,$organization_id, $conn, $trans_amount = 0, $this_month_payment = 0) {
    $organization_data = [];
    $sql = "SELECT * FROM organizations WHERE `organization_id` = '".$organization_id."'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        if ($row = $result->fetch_object()) {
            array_push($organization_data, $row);
        }
    }
    if (count($organization_data) > 0) {
        // expiry date
        $exp_date = $organization_data[0]->expiry_date;
        $reg_date = $organization_data[0]->date_joined;
        $full_name = $organization_data[0]->organization_name;
        $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
        $address = $organization_data[0]->organization_address;
        $contacts = $organization_data[0]->organization_main_contact;
        $account_no = $organization_data[0]->account_no;
        $wallet = $organization_data[0]->wallet;
        $trans_amount = isset($trans_amount)?$trans_amount:"0";
        
        // edited
        $today = date("dS-M-Y");
        $now = date("H:i:s");
        $time = $exp_date;
        $exp_date = date("dS-M-Y",strtotime($exp_date));
        $exp_time = date("H:i:s",strtotime($time));
        $reg_date = date("dS-M-Y",strtotime($reg_date));
        $data = str_replace("[org_name]", $full_name, $data);
        $data = str_replace("[org_first_name]", $f_name, $data);
        $data = str_replace("[org_address]", $address, $data);
        $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
        $data = str_replace("[reg_date]", $reg_date, $data);
        $data = str_replace("[monthly_fees]", $organization_data[0]->monthly_payment,$data);
        $data = str_replace("[this_month_payment]", $this_month_payment,$data);
        $data = str_replace("[org_contact]", $contacts, $data);
        $data = str_replace("[acc_no]", $account_no, $data);
        $data = str_replace("[org_wallet]", "Ksh ".$wallet, $data);
        $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
        $data = str_replace("[today]", $today, $data);
        $data = str_replace("[now]", $now,$data);
        return $data;
    }else {
        // null data
        $exp_date = "Null";
        $reg_date = "Null";
        $monthly_payment = "Null";
        $full_name = "Null";
        $f_name = ucfirst(strtolower((explode(" ",$full_name)[0])));
        $address = "Null";
        $contacts = "Null";
        $account_no = "Null";
        $wallet = "Null";
        $trans_amount = isset($trans_amount)?$trans_amount:"Null";

        // edited
        $today = date("dS-M-Y");
        $now = date("H:i:s");
        $time = $exp_date;
        $exp_date = date("dS-M-Y",strtotime($exp_date));
        $exp_time = date("H:i:s",strtotime($time));
        $reg_date = date("dS-M-Y",strtotime($reg_date));

        // replace the string data
        $data = str_replace("[org_name]", $full_name, $data);
        $data = str_replace("[org_first_name]", $f_name, $data);
        $data = str_replace("[org_address]", $address, $data);
        $data = str_replace("[exp_date]", $exp_date." at ".$exp_time, $data);
        $data = str_replace("[reg_date]", $reg_date, $data);
        $data = str_replace("[monthly_fees]", 0,$data);
        $data = str_replace("[this_month_payment]", $this_month_payment,$data);
        $data = str_replace("[org_contact]", $contacts, $data);
        $data = str_replace("[acc_no]", $account_no, $data);
        $data = str_replace("[org_wallet]", "Ksh ".$wallet, $data);
        $data = str_replace("[trans_amnt]", "Ksh ".$trans_amount, $data);
        $data = str_replace("[today]", $today, $data);
        $data = str_replace("[now]", $now,$data);
        return $data;
    }
}

function get_sms($conn, $category = null, $sub_category = null){
    $select = "SELECT * FROM `settings` WHERE `keyword` = 'Messages';";
    $stmt = $conn->prepare($select);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        if ($rowed = $res->fetch_assoc()){
            $sms_contents = json_decode($rowed['value'], true);
            if ($category == null && $sub_category == null) {
                return $sms_contents;
            }

            if ($category != null && $sub_category == null) {
                foreach ($sms_contents as $key => $value) {
                    if ($category == $value['Name']) {
                        return $value['messages'];
                    }
                }
            }

            if ($category != null && $sub_category != null) {
                foreach ($sms_contents as $key => $value) {
                    if ($category == $value['Name']) {
                        foreach ($value['messages'] as $value_2) {
                            if($value_2['Name'] == $sub_category){
                                return $value_2['message'];
                            }
                        }
                    }
                }
            }

            return $sms_contents;
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
function formatKenyanPhone($number) {
    // Remove spaces, dashes, and plus sign
    $number = preg_replace('/[\s\-\+]/', '', $number);

    // If it starts with "07", replace with "2547"
    if (preg_match('/^07\d{8}$/', $number)) {
        return '254' . substr($number, 1);
    }

    // If it starts with "+2547" (after plus removal)
    if (preg_match('/^2547\d{8}$/', $number)) {
        return $number;
    }

    // If it starts with "7" only, add "254"
    if (preg_match('/^7\d{8}$/', $number)) {
        return '254' . $number;
    }

    // Invalid number
    return false;
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