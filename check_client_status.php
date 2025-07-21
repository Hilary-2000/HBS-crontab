<?php
	// mikrotik disable all active users who`s dates are due
	date_default_timezone_set('Africa/Nairobi');
	
	// allowed ip address
	include "allowed_ip.php";

	// connect database
	include "db_connect.php";
	
	include "shared_functions.php";
	
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
					$yesterday = date("YmdHis",strtotime("-1 day"));
					$today = date("YmdHis");
                    $select = "SELECT * FROM `client_tables` WHERE `payments_status` = '1' AND `deleted` = '0' AND `next_expiration_date` BETWEEN '".$yesterday."' AND '".$today."'";
                    $stmt = $conn->prepare($select);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo $row['client_name']." deactivate<br>";
                            // function to deactivate client
                            deactivate_client($row,$rowed['organization_database']);
                        }
                    }
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
	}
?>