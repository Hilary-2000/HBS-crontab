<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	// get connection to the database and get the values of the users that are due that minute
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

	// check if the connection is valid
	if ($conn) {
		// get router ip address
		if (isset($_GET['r_id']) && isset($_GET['r_ip'])) {
			$ip_address = router_ip($_GET['r_id'],$conn);
			$data = "[";
			foreach ($ip_address as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['r_queues']) && isset($_GET['r_id'])) {
			$queues = router_queue($_GET['r_id'],$conn);
			$data = "[";
			foreach ($queues as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['r_id']) && isset($_GET['r_interfaces'])) {
			$interfaces = router_interfaces($_GET['r_id'],$conn);
			$data = "[";
			foreach ($interfaces as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['r_id']) && isset($_GET['r_ppoe_profiles'])) {
			$router_id = $_GET['r_id'];
			$router_profile = router_ppoe_profile($router_id,$conn);
			$data = "[";
			foreach ($router_profile as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['r_id']) && isset($_GET['r_ppoe_secrets'])) {
			$router_id = $_GET['r_id'];
			$router_secrets = router_ppoe_secrets($router_id,$conn);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['r_id']) && isset($_GET['r_active_secrets'])) {
			$router_id = $_GET['r_id'];
			$router_secrets = router_active_conn($router_id,$conn);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['pppoe_server_list']) && isset($_GET['router_api_port'])  && isset($_GET['router_ip_address'])  && isset($_GET['router_username'])  && isset($_GET['router_password'])) {
			$router_api_port = $_GET['router_api_port'];
			$router_ip_address = $_GET['router_ip_address'];
			$router_username = $_GET['router_username'];
			$router_password = $_GET['router_password'];
			$router_secrets = pppoe_server_id_no($router_api_port,$router_ip_address,$router_username,$router_password);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['pppoe_server_bridge']) && isset($_GET['router_api_port'])  && isset($_GET['router_ip_address'])  && isset($_GET['router_username'])  && isset($_GET['router_password'])) {
			$router_api_port = $_GET['router_api_port'];
			$router_ip_address = $_GET['router_ip_address'];
			$router_username = $_GET['router_username'];
			$router_password = $_GET['router_password'];
			$router_secrets = pppoe_server_bridges($router_api_port,$router_ip_address,$router_username,$router_password);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['address_lists']) && isset($_GET['router_api_port'])  && isset($_GET['router_ip_address'])  && isset($_GET['router_username'])  && isset($_GET['router_password'])) {
			$router_api_port = $_GET['router_api_port'];
			$router_ip_address = $_GET['router_ip_address'];
			$router_username = $_GET['router_username'];
			$router_password = $_GET['router_password'];
			$router_secrets = get_router_ips($router_api_port,$router_ip_address,$router_username,$router_password);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['routes_list']) && isset($_GET['router_api_port'])  && isset($_GET['router_ip_address'])  && isset($_GET['router_username'])  && isset($_GET['router_password'])) {
			$router_api_port = $_GET['router_api_port'];
			$router_ip_address = $_GET['router_ip_address'];
			$router_username = $_GET['router_username'];
			$router_password = $_GET['router_password'];
			$router_secrets = get_route_list($router_api_port,$router_ip_address,$router_username,$router_password);
			$data = "[";
			foreach ($router_secrets as $value) {
				// create as a string
				$data.="{";
				foreach ($value as $key => $values) {
					$data.="\"$key\"".":"."\"$values\"".",";
				}
				$data = substr($data,0,(strlen($data)-1));
				$data.="},";
			}
			$data = strlen($data) > 1 ? substr($data,0,(strlen($data)-1)) : $data;
			$data.="]";
			echo $data;
		}elseif (isset($_GET['router_logs']) && isset($_GET['router_ids'])) {
			$router_id = $_GET['router_ids'];
			// $router_logs = router_active_conn($router_id,$conn);
			$router_logs = router_logs($router_id,$conn);
			echo json_encode($router_logs);
		}
		else {
			echo "[]";
		}
	}
	// get NAT settings
	function pppoe_server_bridges($router_api_port,$router_ip_address,$router_username,$router_password){
		$router_infor = [];
		array_push($router_infor,$router_ip_address,$router_username,$router_password,$router_api_port);
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$active_conns = $API->comm("/interface/bridge/print");
			// var_dump($active_conns);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $active_conns;
		}
		return [];
	}
	function get_route_list($router_api_port,$router_ip_address,$router_username,$router_password){
		// get the router information
		$router_infor = [];
		array_push($router_infor,$router_ip_address,$router_username,$router_password,$router_api_port);

		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$ip_addresses = $API->comm("/ip/route/print");
			// var_dump($ip_addresses);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $ip_addresses;
		}
		return [];
	}
	function get_router_ips($router_api_port,$router_ip_address,$router_username,$router_password){
		// get the router information
		$router_infor = [];
		array_push($router_infor,$router_ip_address,$router_username,$router_password,$router_api_port);

		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$ip_addresses = $API->comm("/ip/address/print");
			// var_dump($ip_addresses);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $ip_addresses;
		}
		return [];
	}
	function pppoe_server_id_no($router_api_port,$router_ip_address,$router_username,$router_password){
		$router_infor = [];
		array_push($router_infor,$router_ip_address,$router_username,$router_password,$router_api_port);
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$active_conns = $API->comm("/ppp/profile/print");
			// var_dump($active_conns);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $active_conns;
		}
		return [];
	}
	function router_active_conn($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$active_conns = $API->comm("/ppp/active/print");
			// var_dump($active_conns);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $active_conns;
		}
		return [];
	}
	function router_logs($router_id,$conn){
		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		// connect to router
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$active_conns = $API->comm("/log/print");
			// var_dump($active_conns);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $active_conns;
		}
		return [];
	}
	function router_ppoe_secrets($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$interfaces = $API->comm("/ppp/secret/print");
			// var_dump($interfaces);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $interfaces;
		}
		return [];
	}

	function router_ppoe_profile($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];
		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$interfaces = $API->comm("/ppp/profile/print");
			// var_dump($interfaces);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $interfaces;
		}
		return [];
	}

	function router_interfaces($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT * FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$interfaces = $API->comm("/interface/print");
			// var_dump($interfaces);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $interfaces;
		}
		return [];
	}

	function router_queue($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$ip_addresses = $API->comm("/queue/simple/print");
			// var_dump($ip_addresses);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $ip_addresses;
		}
		return [];
	}

	function router_ip($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$ip_addresses = $API->comm("/ip/address/print");
			// var_dump($ip_addresses);
			// get if the ip address appears and get its id
			$API->disconnect();
			return $ip_addresses;
		}
		return [];
	}



	function de_activate($router_id,$network,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		// get router information
		$select = "SELECT `router_ipaddr`,`router_api_username`,`router_api_password`,`router_api_port` FROM `router_tables` WHERE `router_id` = ?";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$router_infor = [];
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				array_push($router_infor,$row['router_ipaddr'],$row['router_api_username'],$row['router_api_password'],$row['router_api_port']);
			}
		}
		$ip_address = $router_infor[0];
		$username = $router_infor[1];
		$password = $router_infor[2];
		$port = $router_infor[3];

		// echo $ip_address." ".$username." ".$password." ".$port;

		require_once "./routeros_api.php";
		$API = new routeros_api();
        $API->debug = false;
		// echo $API->connect($ip_address,$username,$password,$port);
        if ($API->connect($ip_address,$username,$password,$port)) {

			$id = "";
			$ip_addresses = $API->comm("/ip/address/print");
			// var_dump($ip_addresses);
			// get if the ip address appears and get its id
			foreach ($ip_addresses as $key => $value) {
				// get the ip address id
				if ($value['network'] == $network) {
					$id = $value['.id'];
					// set the address disabled
					$API->comm("/ip/address/set",array(
						"disabled"=>"yes",
						".id"=> $id
					));
				}
			}
			$API->disconnect();
		}
		
	}
	function send_sms($conn,$phone_number,$message,$acc_id){
		// get the sms api keys
		$sms_api_keys = getSMSKeys($conn);
		$apikey = $sms_api_keys[0];
		$partnerID = $sms_api_keys[1];
		$shortcode = $sms_api_keys[2];

		// send the sms
		$mobile = $phone_number; // Bulk messages can be comma separated

		$finalURL = "https://mysms.celcomafrica.com/api/services/sendsms/?apikey=" . urlencode($apikey) . "&partnerID=" . urlencode($partnerID) . "&message=" . urlencode($message) . "&shortcode=$shortcode&mobile=$mobile";
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
		// return $values;
		foreach ($values as  $key => $value) {
			// echo $key;
			if ($key == "response-code") {
				if ($value == "200") {
					// if its 200 the message is sent delete the
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
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_api_key';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_partner_id';";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result) {
			if ($row = $result->fetch_assoc()) {
				// get the api key
				array_push($sms_api_keys,$row['value']);
			}
		}
		$select = "SELECT `value` FROM `settings` WHERE `keyword` = 'sms_shortcode';";
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
