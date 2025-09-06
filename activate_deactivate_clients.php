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
		$select = "SELECT * FROM `organizations` WHERE `organization_status` = '1'";
		$stmt = $conn1->prepare($select);
		$stmt->execute();
		$main_result = $stmt->get_result();
		if ($main_result) {
			while ($rowed = $main_result->fetch_assoc()) {
				// database name
				$database_name = $rowed['organization_database'];
				echo $database_name."<br>";
				
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
			
					// get the clients who have their account expired
					$select = "SELECT * FROM `client_tables` WHERE `next_expiration_date` < ? AND `payments_status` = '1' AND `deleted` = '0'";
					$stmt = $conn->prepare($select);
					$today_date = date("YmdHis");
					$stmt->bind_param("s",$today_date);
					$stmt->execute();
					$result = $stmt->get_result();
					if ($result) {
						$in = 0;
						while ($row = $result->fetch_assoc()) {
							$in++;
							$wallet_amount = $row['wallet_amount'];
							$monthly_payment = $row['monthly_payment'];
							$min_amount = $row['min_amount'];
							$client_id = $row['client_id'];
							$client_contacts = $row['clients_contacts'];
			
							$next_expiry_date = null;
							$wallet_balance = 0;
							if ($wallet_amount >= $monthly_payment) {
								// add a month to the expiry date
								$next_expiry_date = date("YmdHis",strtotime("1 Month"));
								$wallet_balance = $wallet_amount - $monthly_payment;
			
								// update the client
								$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ? WHERE `client_id` = ?";
								$stmt = $conn->prepare($update);
								$client_status = 1;
								$stmt->bind_param("sss",$next_expiry_date,$wallet_balance,$row['client_id']);
								$stmt->execute();
			
								// activate the client
								activate_user($row,$rowed['organization_database']);
			
								// activate the user
								if ($row['client_status'] == 0) {
									$message_contents = get_sms($conn);
									$message = $message_contents[2]->messages[0]->message;
									if ($message) {
										$trans_amount = 0;
										$message = message_content($message,$client_id,$conn,$trans_amount);
										echo $message."<br>";
										// activate the user in the router
										// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been renewed. your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
										send_sms($conn,$client_contacts,$message,$client_id,$rowed['send_sms']);
									}
			
								}else {
									$message_contents = get_sms($conn);
									$message = $message_contents[2]->messages[1]->message;
									if ($message) {
										$trans_amount = 0;
										$message = message_content($message,$client_id,$conn,$trans_amount);
										echo $message."<br>";
										// send the user an SMS
										// $message = "Dear ".ucfirst(strtolower(explode(" ",$client_name)[0])).", Your Acc ".$client_account." has been extended for the next 30 days. Your wallet Bal:".$new_wallet." KSH.Check acc status on billing.hypbits.com/login. For enquires call 0717748569";
										send_sms($conn,$client_contacts,$message,$client_id,$rowed['send_sms']);
									}
								}
							}else {
								$minimum_pay = ceil($monthly_payment * ($min_amount / 100));
								
								// the minimum pay should not be less than 0
								if ($minimum_pay != 0) {
									// $minimum_pay = ceil($monthly_payment/4);
									if ($wallet_amount < $minimum_pay) {
										if ($row['client_status'] == 1) {
											// send them a message that they are deactivated
											$message_contents = get_sms($conn);
											$message = $message_contents[2]->messages[2]->message;
											if ($message) {
												$trans_amount = 0;
												$message = message_content($message,$client_id,$conn,$trans_amount);
												// send the user an SMS
												echo $message."<br>";
												send_sms($conn,$client_contacts,$message,$client_id,$rowed['send_sms']);
											}
			
											// function to deactivate client
											deactivate_client($row,$rowed['organization_database']);
										}
									}else {
										// if the amount in the wallet is greater than the minimum
										// get the percentage of the amount and know till when 
										// will the amount take them for a 30 day period
										$percentage = ($wallet_amount/$monthly_payment) * 100;
										$dayed = round(($percentage/100) * 30,1);
										// check if it gives hours and days so that they days and hours are added
				
										// split to get days and hours
										$days = explode(".",$dayed);
										$time_period = "0 hours";
										if (count($days) > 0) {
											// means it has both days and hours
											$day = $days[0];
											$hours = isset($days[1]) ? round(($days[1]/10) * 24) : 0;
											$time_period = $day." days ".$hours." hours";
										}else {
											$time_period = $days[0]." days";
										}
										$NextExpDate1 = date("dS M Y H:i:s",strtotime($time_period));
										$NextExpDate = date("YmdHis",strtotime($time_period));
										$wallet_amount = 0;
										$account_status = 1;
										// the next date of expiry is already found
										// update the user next date of expiration and the user active status and the new wallet amount
										$update = "UPDATE `client_tables` SET `next_expiration_date` = ?, `wallet_amount` = ? WHERE `client_id` = ?";
										$stmt = $conn->prepare($update);
										$stmt->bind_param("sss",$NextExpDate,$wallet_amount,$client_id);
										$stmt->execute();
				
										// send sms
										$message_contents = get_sms($conn);
										$message = $message_contents[2]->messages[1]->message;
										if ($message) {
											$trans_amount = 0;
											$message = message_content($message,$client_id,$conn,$trans_amount);
											echo $message."<br>";
											// send the user an SMS
											send_sms($conn,$client_contacts,$message,$client_id,$rowed['send_sms']);
										}
										// activate the router
										activate_user($row,$rowed['organization_database']);
									}
								}else {
									// deactivate the client
									deactivate_client($row,$rowed['organization_database']);
								}
							}
						}
					}
			
					// select all client whose expiration date is not due and activate them if they are inactive
					$select = "SELECT * FROM `client_tables` WHERE `client_status` = '0' AND `payments_status` = '1' AND `next_expiration_date` > ?  AND `deleted` = '0'";
					$stmt = $conn->prepare($select);
					$stmt->bind_param("s",$today_date);
					$stmt->execute();
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						// loop through the clients and activate them
						activate_user($row,$rowed['organization_database']);
					}
				}
			}
		}
	}else{
		echo "Cannot connect to main database";
	}
?>