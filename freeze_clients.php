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
		$select = "SELECT * FROM `organizations` WHERE `organization_status` = '1';";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$result_1 = $stmt->get_result();
		if ($result_1){
			while ($rowed = $result_1->fetch_assoc()) {
	
				// get connection local database and get the values of the users that are due that minute
				// LOCALE
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
				// get the users from the remote server that are to be deactivated and activate those that are activated
				if ($conn) {
					echo "Connected! $dbname<br>";
					// first unblock all clients
					// select client whose freezing date is due
					// if there are those that their account are still inactive activate them
					$date_today = date("YmdHis");
					// echo $date_today;
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_untill` < '$date_today' AND `client_freeze_status` = '1' AND `client_freeze_untill` != '00000000000000' AND `client_freeze_untill` != ''";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						echo json_encode($row)."<br>";
						// get the cleint status
						$payments_status = $row['payments_status'];
						$client_status = $row['client_status'];
						$client_id = $row['client_id'];
						$client_phone = $row['clients_contacts'];
						echo "<br>Frozen date due but still inactive $client_id!";
						$unblock = 1;
						
						// echo $unblock;
						if ($unblock == 1) {
							echo "Unblocked <br>";
							// unblock the user then locally update the clients payments status and account status
							// while remotely change the freezing status
							$update = "UPDATE `client_tables` SET `client_status` = '1', `payments_status` = '1',`client_freeze_status` = '0', `client_freeze_untill` = '' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								// SEND THE CLIENT A MESSAGE OF ACTIVATION
								$message_contents = get_sms($conn);
								if (count($message_contents) > 4) {
									$message = $message_contents[5]->messages;
									$send_message = "";
									for ($index=0; $index < count($message); $index++) {
										if ($message[$index]->Name == "account_unfrozen") {
											$send_message = $message[$index]->message;
										}
									}
									if (strlen($send_message) > 0 && $send_message != null) {
										$trans_amount = 0;
										$message = message_content($send_message,$client_id,$conn,$trans_amount);
										echo $message."<br>";
										send_sms($conn,$client_phone,$message,$client_id,$rowed['send_sms']);
									}
								}else {
									echo  "<br>An error occured!";
								}
							}
							// NOW enable the user
							activate_user($row,$rowed['organization_database']);
						}
					}
					
					// then second freeze clients that are to be frozen
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_status` = '1' AND `client_freeze_untill` > '$date_today' AND `client_freeze_untill` != '00000000000000' AND `client_freeze_untill` != ''";
					// echo $select;
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						// echo json_encode($row)."<br>";
						// check clients that are frozen and need to be deactivated
						$payments_status = $row['payments_status'];
						$client_status = $row['client_status'];
						$client_id = $row['client_id'];
						$block = ($payments_status == "1" || $client_status == "1") ? 1 : 0;
						
						// block users
						if ($block == 1) {
							// block the user
							// update the locale database that the user is deactivated and also deactivate them from the mikrotik router
							$update = "UPDATE `client_tables` SET `client_status` = '0', `payments_status` = '0' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								echo "<br>Client deactivated";
								deactivate_client($row,$rowed['organization_database']);
							}else {
								echo "<br>error occured";
							}
						}

						$time = date("Hi");
						// deactivate those that are to be frozen incase they are activated manually after 6 hours!
						if ($time == "1230" || $time == "0030" || $time == "1830" || $time == "0630") {
							// block the user
							// update the locale database that the user is deactivated and also deactivate them from the mikrotik router
							$update = "UPDATE `client_tables` SET `client_status` = '0', `payments_status` = '0' WHERE `client_id` = '$client_id'";
							$stmt = $conn->prepare($update);
							if($stmt->execute()){
								echo "<br>Client deactivated";
								deactivate_client($row,$rowed['organization_database']);
							}else {
								echo "<br>error occured";
							}
						}
					}

					// FREEZE THE INDEFINATE clients
					$today = date("YmdHis");
					$select = "SELECT * FROM `client_tables` WHERE (`client_freeze_untill` > '".$today."' OR `client_freeze_untill` = '00000000000000') AND (`freeze_date` <= '".$today."'  OR `freeze_date` = '') AND `client_freeze_status` = '1'  AND client_status = '1'";
					// echo $select."<br>";
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while($row = $result->fetch_assoc()){
							// echo json_encode($row)."<br>";
							$update = "UPDATE `client_tables` SET `client_freeze_status` = '1',  `date_changed` = '".date("YmdHis")."', `payments_status` = '0', `freeze_date` = '".date("YmdHis")."' WHERE `client_id` = '".$row['client_id']."'";
							$stmt = $conn->prepare($update);
							$stmt->execute();
			
							// deactivate the user
							deactivate_client($row,$rowed['organization_database']);
						}
					}
			
					// get all clients that are to be deactivated in the future
					$today = date("YmdHis");
					$select = "SELECT * FROM `client_tables` WHERE `client_freeze_untill` > '".$today."' AND `freeze_date` <= '".$today."' AND `client_freeze_status` = '0'";
					// echo $select;
					$stmt = $conn->prepare($select);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						while ($row = $result->fetch_assoc()) {
							// echo json_encode($row)."<br>";
							$update = "UPDATE `client_tables` SET `client_freeze_status` = '1',  `date_changed` = '".date("YmdHis")."', `payments_status` = '0', `freeze_date` = '".date("YmdHis")."' WHERE `client_id` = '".$row['client_id']."'";
							$stmt = $conn->prepare($update);
							$stmt->execute();
			
							// deactivate the user
							deactivate_client($row,$rowed['organization_database']);
			
							// SEND THE CLIENT A MESSAGE OF freezing
							$message_contents = get_sms($conn);
							if (count($message_contents) > 4) {
								$message = $message_contents[5]->messages;
								$send_message = "";
								for ($index=0; $index < count($message); $index++) {
									if ($message[$index]->Name == "account_frozen") {
										$send_message = $message[$index]->message;
									}
								}
								if (strlen($send_message) > 0 && $send_message != null) {
									$trans_amount = 0;
									$freeze_start = date_create($row['freeze_date']);
									$freeze_end = date_create($row['client_freeze_untill']);
									$diff=date_diff($freeze_start,$freeze_end);
									// $days = $diff->format("%R %a days");
									$day_frozen = $diff->format("%a");
									$freeze_dates = $row['client_freeze_untill'] == "00000000000000" ? "" : $row['client_freeze_untill'];
									$message = message_content($send_message,$row['client_id'],$conn,$trans_amount,$day_frozen,$row['freeze_date'],$freeze_dates);
									// echo "<br>".$message.json_encode($row);
									send_sms($conn,$row['clients_contacts'],$message,$row['client_id'],$rowed['send_sms']);
								}
							}else {
								echo  "<br>An error occured!";
							}
						}
					}
				}
			}
		}
	}else{
		echo "Cannot connect to the main database";
	}
?>