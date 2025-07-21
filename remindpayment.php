<?php
	// mikrotik disable all active users who`s dates are due
	// $filename = dirname(__FILE__)."/output.txt";
	// $data = "Hello it ".date("Y-m-d H:i:s")." now"."\n";
	// file_put_contents($filename,$data,FILE_APPEND);
	date_default_timezone_set('Africa/Nairobi');
	
	// allowed ip address
	include "allowed_ip.php";

	// connect database
	include "db_connect.php";
	
	include "shared_functions.php";

	if ($conn1) {
		$select = "SELECT `organization_database` FROM `organizations` GROUP BY `organization_database`;";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$result_1 = $stmt->get_result();
		if ($result_1){
			while ($rowed = $result_1->fetch_assoc()) {
				// get connection to the database and get the values of the users that are due that minute
				$dbname = $rowed['organization_database'];
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
					// echo "Connected";
					// date tommorow
					$tommorow = date("Ymd",strtotime("1 day"));
					// date today
					$today = date("Ymd");
					// date one day after
					$yesterday = date("Ymd",strtotime("-1 day"));
					// echo $yesterday." ".$today." ".$tommorow;
			
					// get the users that are to pay yesterday
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$yesterday%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[2]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
			
					// get the users that are to pay today
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$today%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[1]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
					
					// get the users that are to pay tommorow
					$select = "SELECT `client_id`,`client_name`,`router_name`,`client_status`,`client_network`,`monthly_payment`,`client_account`,`wallet_amount`,`clients_contacts` FROM `client_tables` WHERE `next_expiration_date` LIKE '$tommorow%' AND `payments_status` = '1';";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							$message_contents = get_sms($conn);
							$message = $message_contents[0]->messages[0]->message;
							if ($message) {
								$trans_amount = 0;
								$message = message_content($message,$row['client_id'],$conn,$trans_amount);
								send_sms($conn,$row['clients_contacts'],$message,$row['client_id']);
							}
						}
					}
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
	}
?>
