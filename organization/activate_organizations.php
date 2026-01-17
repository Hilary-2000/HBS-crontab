<?php
/**
 * THIS SCRIPT IS USED TO ACTIVATE AND
 * DEACTIVATE ORGANIZATIONS WHEN THEIR 
 * EXPIRY DATES IS REACHED AND ACTIVATED
 * WHEN THEY MAKE PAYMENTS.
 */

include __DIR__."/../allowed_ip.php";

date_default_timezone_set('Africa/Nairobi');

#CONSTANTS
$months_last_active = "-3 months";  //months to check for last active clients

$batch_of_client = 50; //number of clients to charge

$per_head_cost = 20; // cost per head

$free_clients = 0; // number of clients to charge free


// DB CREDENTIALS
$dbname = 'mikrotik_cloud_manager';
include __DIR__."/../db_credential.php";
if(!isset($_SESSION)) {
    session_start(); 
}


$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
    exit();
}

// PROCEED AND GET THE ORGANIZATIONS TO ACTIVATE AND DEACTIVATED
$sql = "SELECT * FROM organizations WHERE payment_status = '1'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while($row = $result->fetch_object()){
        // check if the organization is deactivated and needs activation
        $database_name = $row->organization_database;
        $monthly_payment = $row->monthly_payment;
        $wallet = $row->wallet*1;

        // if the organization expiry date is reached
        if (date("YmdHis")*1 > $row->expiry_date*1) {
            $total_cost = getMonthlyPayment($row, $hostname, $dbusername, $dbpassword, $months_last_active, $free_clients, $per_head_cost);
            if($wallet >= $total_cost){
                // extend the client
                $next_expiry_date = date("YmdHis", strtotime("1 month"));
                $wallet -= $total_cost;
                $update = "UPDATE organizations SET `wallet` = ?, `expiry_date` = ?, `organization_status` = '1' WHERE organization_id = ?";
                $stmt = $conn->prepare($update);
                $stmt->bind_param("sss", $wallet, $next_expiry_date, $row->organization_id);
                $stmt->execute();
                
                if ($row->organization_status == "1"){
                    // send the message
                    $message = get_sms($conn, "renew_account", "account_extended");
                    $message = message_content($message,$row->organization_id,$conn,0,$total_cost);
                    
                    // send_sms
                    send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
                }else{
                    // send the message
                    $message = get_sms($conn, "renew_account", "account_blocked");
                    $message = message_content($message,$row->organization_id,$conn,0,$total_cost);
                    
                    // send_sms
                    send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
                }
            }else{
                if ($row->organization_status == "1") {
                    // first time deactivation send a message
                    // extend the client
                    $update = "UPDATE organizations SET `organization_status` = '0' WHERE organization_id = ?";
                    $stmt = $conn->prepare($update);
                    $stmt->bind_param("s", $row->organization_id);
                    $stmt->execute();
                    
                    $message = get_sms($conn, "renew_account", "account_deactivated");
                    $message = message_content($message,$row->organization_id,$conn,0,$total_cost);

                    // send_sms
                    send_sms($conn, $row->organization_main_contact, $message, $row->organization_id);
                }else{
                    // extend the client
                    $update = "UPDATE organizations SET `organization_status` = '0' WHERE organization_id = ?";
                    $stmt = $conn->prepare($update);
                    $stmt->bind_param("s", $row->organization_id);
                    $stmt->execute();
                }
            }
        }else{
            if($row->organization_status == "1"){
                // organization is active
                continue;
            }
            // KEEP THE ORGANIZATION STATUS AS ON WHEN THE EXPIRATION DATE IS NOT REACHED!
            $update = "UPDATE organizations SET `organization_status` = '1' WHERE organization_id = ?";
            $stmt = $conn->prepare($update);
            $stmt->bind_param("s", $row->organization_id);
            $stmt->execute();
        }
    }
}

function getMonthlyPayment($organization_data, $hostname, $dbusername, $dbpassword, $months_last_active, $free_clients, $per_head_cost){
        // GET THAT MONTHLY PAYMENT AMOUNT
        $dbname = $organization_data->organization_database;
        $conn2 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
        if (mysqli_connect_errno()) {
            return 0;
        }

        // check the number of clients they have active in the last three months
        $sql = "SELECT COUNT(*) AS 'total' FROM client_tables WHERE next_expiration_date >= ? AND clients_reg_date <= ?";
        $stmt = $conn2->prepare($sql);
        $last_active_month = date("Ym", strtotime($months_last_active))."01235959";
        $five_days_before_expiry = modifyDate($organization_data->expiry_date,-5);
        $stmt->bind_param("ss", $last_active_month, $five_days_before_expiry);
        $stmt->execute();
        $result_2 = $stmt->get_result();
        $total_clients = 0;
        if ($result_2) {
            if ($rowed = $result_2->fetch_assoc()) {
                $total_clients = $rowed['total']*1;
            }
        }
        
        $total_cost = 1000;
        if ($total_clients > $free_clients) {
            $total_clients -= $free_clients;
            $total_cost = $total_clients > 100 ? $total_clients * $per_head_cost : 1000;
            $total_cost = $total_cost != 0 ? $total_cost : 1000;
        }
        return $total_cost;
}

function modifyDate($date, $period, $unit = 'days', $format = "YmdHis") {
    // Normalize the unit
    $unit = strtolower(trim($unit));

    // Determine if we are adding or subtracting
    $sign = ($period >= 0) ? '+' : '';

    // Build the interval string
    switch ($unit) {
        case 'day':
        case 'days':
            $intervalSpec = "{$sign}{$period} days";
            break;
        case 'week':
        case 'weeks':
            $intervalSpec = "{$sign}{$period} weeks";
            break;
        case 'month':
        case 'months':
            $intervalSpec = "{$sign}{$period} months";
            break;
        case 'year':
        case 'years':
            $intervalSpec = "{$sign}{$period} years";
            break;
        default:
            $intervalSpec = "{$sign}{$period} days";
    }

    // Create the DateTime object
    $dateTime = new DateTime($date);

    // Modify the date
    $dateTime->modify($intervalSpec);

    // Return the new date in Y-m-d format
    return $dateTime->format($format);
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
        $data = str_replace("[monthly_fees]", number_format($organization_data[0]->monthly_payment),$data);
        $data = str_replace("[this_month_payment]", number_format($this_month_payment),$data);
        $data = str_replace("[org_contact]", $contacts, $data);
        $data = str_replace("[acc_no]", $account_no, $data);
        $data = str_replace("[org_wallet]", number_format($wallet), $data);
        $data = str_replace("[trans_amnt]", number_format($trans_amount), $data);
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

function addMonths($date,$months){
    $date = date_create($date);
    date_add($date,date_interval_create_from_date_string($months." Month"));
    return date_format($date,"YmdHis");
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
        $message_status = 0;
        $values = $res->responses[0];
        foreach ($values as  $key => $value) {
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