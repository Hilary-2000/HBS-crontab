<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');

	if (!isset($_GET['db_name'])) {
		echo "Invalid access";
		return "Invalid access";
		exit();
	}

	// get connection to the database and get the values of the users that are due that minute
	$dbname = $_GET['db_name'];
    include "db_credential.php";
	if(!isset($_SESSION)) {
		session_start(); 
	}
	// echo $dbname;
	// exit();
	
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
			// get router ip addresses
			$ip_address = router_ip($_GET['r_id'],$conn);
			echo json_encode($ip_address);

		}elseif (isset($_GET['r_queues']) && isset($_GET['r_id'])) {
			// get router queues
			$queues = router_queue($_GET['r_id'],$conn);
			echo json_encode($queues);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_interfaces'])) {
			// get interfaces
			$interfaces = router_interfaces($_GET['r_id'],$conn);
			echo json_encode($interfaces);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_ip_pool'])) {
			// get interfaces
			$pool = router_ip_pool($_GET['r_id'],$conn);
			echo json_encode($pool);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_ppoe_profiles'])) {
			// get the pppoe profile
			$router_id = $_GET['r_id'];
			$router_profile = router_ppoe_profile($router_id,$conn);
			echo json_encode($router_profile);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_ppoe_secrets'])) {
			// get the pppoe secret
			$router_id = $_GET['r_id'];
			$router_secrets = router_ppoe_secrets($router_id,$conn);
			echo json_encode($router_secrets);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_bridge_ports'])) {
			// get the pppoe secret
			$router_id = $_GET['r_id'];
			$router_secrets = router_bridge_ports($router_id,$conn);
			echo json_encode($router_secrets);

		}elseif (isset($_GET['r_id']) && isset($_GET['r_active_secrets'])) {
			$router_id = $_GET['r_id'];
			$router_secrets = router_active_conn($router_id,$conn);
			echo json_encode($router_secrets);
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

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;

					require_once "./routeros_api.php";
					$API = new routeros_api();
					$API->debug = false;
					if ($API->connect($ip_address,$user,$pass,$port)){
						$API->disconnect();
						$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
						if ($client_router_ip != false) {
							// connect to the router and get the ip addresses
							$API_2 = new routeros_api();
							$API_2->debug = false;
							if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
								$active_connection = $API_2->comm("/ppp/active/print");
								$API_2->disconnect();
								return $active_connection;
							}
						}
					}

				}
			}
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

	function router_bridge_ports($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;

					require_once "./routeros_api.php";
					$API = new routeros_api();
					$API->debug = false;
					if ($API->connect($ip_address,$user,$pass,$port)){
						$API->disconnect();
						$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
						if ($client_router_ip != false) {
							// connect to the router and get the ip addresses
							$API_2 = new routeros_api();
							$API_2->debug = false;
							if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
								$interfaces = $API_2->comm("/interface/bridge/port/print");
								$API_2->disconnect();
								return $interfaces;
							}
						}
					}

				}
			}
		}
		return [];
	}

	function router_ppoe_secrets($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;

					require_once "./routeros_api.php";
					$API = new routeros_api();
					$API->debug = false;
					if ($API->connect($ip_address,$user,$pass,$port)){
						$API->disconnect();
						$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
						if ($client_router_ip != false) {
							// connect to the router and get the ip addresses
							$API_2 = new routeros_api();
							$API_2->debug = false;
							if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
								$interfaces = $API_2->comm("/ppp/secret/print");
								$API_2->disconnect();
								return $interfaces;
							}
						}
					}

				}
			}
		}
		return [];
	}

	function router_ppoe_profile($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;
					
					$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
					if ($client_router_ip != false) {
						// connect to the router and get the ip addresses
						$API_2 = new routeros_api();
						$API_2->debug = false;
						if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
							$interfaces = $API_2->comm("/ppp/profile/print");
							$API_2->disconnect();
							return $interfaces;
						}
					}

				}
			}
		}
		return [];
	}

	function router_ip_pool($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;
					
					$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
					if ($client_router_ip != false) {
						// connect to the router and get the ip addresses
						$API_2 = new routeros_api();
						$API_2->debug = false;
						if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
							$pool = $API_2->comm("/ip/pool/print");
							$API_2->disconnect();
							return $pool;
						}
					}

				}
			}
		}
		return [];
	}

	function router_interfaces($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;
					
					$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
					if ($client_router_ip != false) {
						// connect to the router and get the ip addresses
						$API_2 = new routeros_api();
						$API_2->debug = false;
						if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
							$interfaces = $API_2->comm("/interface/print");
							$API_2->disconnect();
							return $interfaces;
						}
					}

				}
			}
		}
		return [];
	}

	function router_queue($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;
					
					// ROUTER IP
					$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
					if ($client_router_ip != false) {
						// connect to the router and get the ip addresses
						$API_2 = new routeros_api();
						$API_2->debug = false;
						if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
							$simple_queues = $API_2->comm("/queue/simple/print");
							$API_2->disconnect();
							return $simple_queues;
						}
					}

				}
			}
		}
		return [];
	}

	function getSSTPAddress($conn){
		$select = "SELECT * FROM `settings` WHERE `keyword` = 'sstp_server'";
		$stmt = $conn->prepare($select);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				$value = $row['value'];
				return $value;
			}
		}
		return null;
	}
    function isJson($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

	function router_ip($router_id,$conn){
		// get the router information
		// connect to the router
		// print the ip addresses
		// then get the id of the ip address of the user

		$select = "SELECT * FROM `remote_routers` WHERE `router_id` = ? AND `deleted` = '0'";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("s",$router_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result){
			if($row = $result->fetch_assoc()){
				// hey yje sstp server details
				$sstp_username = $row['sstp_username'];
				$sstp_password = $row['sstp_password'];
				$api_port = $row['api_port'];
				
				// connect to the router and set the sstp client
				$sstp_value = getSSTPAddress($conn);
				if (isJson($sstp_value)) {
					// sstp value
					$sstp_value = json_decode($sstp_value);

					// server settings
					$ip_address = $sstp_value->ip_address;
					$user = $sstp_value->username;
					$pass = $sstp_value->password;
					$port = $sstp_value->port;

					$client_router_ip = checkActive($ip_address,$user,$pass,$port,$sstp_username);
					if ($client_router_ip != false) {
						// connect to the router and get the ip addresses
						$API_2 = new routeros_api();
						$API_2->debug = false;
						if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
							$ip_addresses = $API_2->comm("/ip/address/print");
							$API_2->disconnect();
							return $ip_addresses;
						}
					}

				}
			}
		}
		return [];
	}

	

    function checkActive($ip_address,$user,$pass,$port,$sstp_username){
		require_once "./routeros_api.php";
        $API_3 = new routeros_api();
        $API_3->debug = false;

        if ($API_3->connect($ip_address, $user, $pass, $port)){
            // connect and get the 
            $active = $API_3->comm("/ppp/active/print");

            // loop through the active routers to get if the router is active or not so that we connect
            $found = 0;
            $ip_address_remote_client = null;
            for ($index=0; $index < count($active); $index++) { 
                if ($active[$index]['name'] == $sstp_username && $active[$index]['service'] == "sstp") {
                    $found = 1;
                    $ip_address_remote_client = $active[$index]['address'];
                    break;
                }
            }

            // if found the router is actively connected
            if ($found == 1) {
                $API_3->disconnect();
                return $ip_address_remote_client;
            }
            $API_3->disconnect();
        }
        return false;
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
?>
