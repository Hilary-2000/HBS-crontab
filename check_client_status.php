<?php
	// mikrotik disable all active users who`s dates are due
	date_default_timezone_set('Africa/Nairobi');

	// loop through every organization database to activate and deactivate clients
	$dbname = "mikrotik_cloud_manager";
	$hostname = "localhost";
	// $dbusername = 'hilla';
	// $dbpassword = "Francis=Son123";
	$dbusername = 'root';
	$dbpassword = "";
	$conn1 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// Check connection
	if (mysqli_connect_errno()) {
		die("Failed to connect to MySQL: " . mysqli_connect_error());
		exit();
	}
	
	if ($conn1) {
		$select = "SELECT * FROM `organizations` WHERE `organization_status` = '1'";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$main_result = $stmt->get_result();
		if ($main_result) {
			while ($rowed = $main_result->fetch_assoc()) {
				// database name
				$database_name = $rowed['organization_database'];
				// echo $database_name."<br>";
				
				// get connection to the database and get the values of the users that are due that minute
				// Connect REMOTE
				$dbname = $database_name;
				$hostname = 'localhost';
				// $dbusername = 'hilla';
				// $dbpassword = "Francis=Son123";
				$dbusername = 'root';
				$dbpassword = "";
				if(!isset($_SESSION)) {
					session_start(); 
				}
				$conn = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
				// Check connection
				if (mysqli_connect_errno()) {
					continue;
				}
			
				// if connected
				if ($conn) {
					// echo "We are connected!<br>";

                    // deactivate client if they have been deactivated in the last 24 hours
					$date = date("YmdHis",strtotime("-1 day"));
                    $select = "SELECT * FROM `client_tables` WHERE `next_expiration_date` > '".$date."'";
                    $stmt = $conn->prepare($select);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo $row['client_name']." deactivate<br>";
                            // function to deactivate client
                            // deactivate_client($row,$rowed['organization_database']);
                        }
                    }

                    // activate clients if status is active
                    // $select = "SELECT * FROM `client_tables` WHERE `client_status` = '1'";
                    // $stmt = $conn->prepare($select);
                    // $stmt->execute();
                    // $result = $stmt->get_result();
                    // if ($result) {
                    //     while ($row = $result->fetch_assoc()) {
                    //         echo $row['client_name']." activate<br>";
                    //         // activate_user($row, $rowed['organization_database']);
                    //     }
                    // }
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
	}

    function activate_user($client_data,$database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/activate/".$client_data['client_id']."/".$database_name."/".$database_name;
		// $url = "http://192.168.88.240:8000/activate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		// echo $curl_data;
    }

    function deactivate_client($client_data, $database_name){
		$curl_handle = curl_init();

		$url = "https://billing.hypbits.com/deactivate/".$client_data['client_id']."/".$database_name."/".$database_name;
		// $url = "http://192.168.88.240:8000/deactivate/".$client_data['client_id']."/".$database_name;
	
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	
		$curl_data = curl_exec($curl_handle);
	
		if(curl_errno($curl_handle)){
			echo 'Curl error: ' . curl_error($curl_handle);
		}
	
		curl_close($curl_handle);
	
		// echo $curl_data;
    }
?>