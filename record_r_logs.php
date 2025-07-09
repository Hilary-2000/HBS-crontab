<?php
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

// get connection local database and get the values of the users that are due that minute
// LOCAL
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
    // go through all the routers and record their logs over time
    $select = "SELECT * FROM `router_tables`";
    $stmt = $conn->prepare($select);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            recordLogs($row['router_id']);
        }
    }
    echo "Done recording logs for the router";
}else{
    echo "Couldn`t connect to the router!";
}

function recordLogs($router_id){

    $curl_handle = curl_init();

    $url = "http://localhost:8000/Router/writeLogs/".$router_id;
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
?>