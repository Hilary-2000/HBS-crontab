<?php
	// mikrotik disable all active users who`s dates are due
	date_default_timezone_set('Africa/Nairobi');

	// allowed ip address
	include "allowed_ip.php";

	// connect database
	include "db_connect.php";
	
	// shared functions
	include "shared_functions.php";
	
	if ($conn1) {
		require_once "./routeros_api2.php";
		$select = "SELECT * FROM `organizations` WHERE `organization_status` = '1'";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$main_result = $stmt->get_result();
		if ($main_result) {
			while ($rowed = $main_result->fetch_assoc()) {
				// database name
				$database_name = $rowed['organization_database'];
				echo $database_name."<br>";
                if (
                    $database_name != "mikrotik_cloud" && 
                    $database_name != "HBS106") {
                    continue;
                }
				
				// get connection to the database and get the values of the users that are due that minute
				// Connect REMOTE
				$dbname = $database_name;

				$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
				// Check connection
				if (mysqli_connect_errno()) {
					// die("Failed to connect to MySQL: " . mysqli_connect_error());
					// exit();
					continue;
				}
			
				// if connected
				if ($conn) {
					echo "We are connected!<br>";
                    
                    // get the routers
                    $select = "SELECT * FROM remote_routers";
                    $statement = $conn->prepare($select);
                    $statement->execute();
                    $result = $statement->get_result();
                    if ($result) {
                        while ($row_router = $result->fetch_assoc()) {
                            echo $row_router['router_name']." -- ";
                            // connect to the router and import the config
                            $sstp_username = $row_router['sstp_username'];
                            $sstp_password = $row_router['sstp_password'];
                            $api_port = $row_router['api_port'];
                            $interfaces = [];
                            
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
                                    $API_2 = new routeros_api2();
                                    $API_2->debug = false;
                                    if ($API_2->connect($client_router_ip,$sstp_username,$sstp_password,$api_port)){
                                        $file_url = "https://test_billing.hypbits.com/scripts/import_config.rsc";
                                        // Step 1: Delete old scripts if it exists
                                        $scripts = $API_2->comm("/system/script/print", [
                                            ".proplist" => ".id,name"
                                        ]);

                                        foreach ($scripts as $script) {
                                            if ($script['name'] === "run_import_script") {
                                                $API_2->comm("/system/script/remove", [
                                                    ".id" => $script['.id']
                                                ]);
                                                break;
                                            }
                                        }

                                        // Step 2: Delete old import file if it exists
                                        $files = $API_2->comm("/file/print", [
                                            ".proplist" => ".id,name"
                                        ]);

                                        foreach ($files as $file) {
                                            if ($file['name'] === "import_config.rsc") {
                                                $API_2->comm("/file/remove", [
                                                    ".id" => $file['.id']
                                                ]);
                                                break;
                                            }
                                        }

                                        // Use the RouterOS fetch command to download the file
                                        $interfaces = $API_2->comm("/tool/fetch", array(
                                            "url" => $file_url,
                                            "mode" => "https",
                                            "dst-path" => "import_config.rsc",
                                            "keep-result" => "yes"
                                        ));

                                        $API_2->write("/system/script/add", false);
                                        $API_2->write("=name=run_import_script", false);
                                        $API_2->write("=source=/import file-name=import_config.rsc", true);
                                        $API_2->read(false); // don't wait forever

                                        // // Step 2: Run the script
                                        $API_2->write("/system/script/run", false);
                                        $API_2->write("=number=run_import_script", true);
                                        $API_2->read(false);
                                        
                                        $API_2->disconnect();

                                        // Step 2: Import the configuration file into RouterOS
                                        // return $interfaces;
                                    }
                                }
                            }
                            echo json_encode($interfaces)."<br>";
                            // break;
                        }
                    }
				}
                // break;
			}
		}
	}else{
		echo "Cannot connect to main database";
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
?>