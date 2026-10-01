<?php
	// SMS sending agent: sends the bulk SMS that mikrotik_cloud's compose page has
	// queued in an org's `sms_tables`. Not scheduled; mikrotik_cloud calls it as a link
	// right after queueing, passing the org database:
	//   http://localhost/crontab/send_queued_sms.php?db=<organization_database>
	//   php send_queued_sms.php <organization_database>
	//
	// Queued rows have `sent_status = 0` and `processing_id = 0`. Every other row
	// (including those written by send_sms() here) gets the column default
	// `sent_status = 1`, so the agent never touches them.
	date_default_timezone_set('Africa/Nairobi');

	// keep sending after mikrotik_cloud stops waiting for the response
	ignore_user_abort(true);
	set_time_limit(0);

	// allowed ip address
	include "allowed_ip.php";

	// connect database
	include "db_connect.php";

	include "shared_functions.php";

	// rows claimed per batch
	$batch_size = 50;
	// a claimed row still unsent after this long is marked failed, never re-sent
	$stale_minutes = 10;

	header('Content-Type: application/json');

	$database_name = isset($_GET['db']) ? trim($_GET['db']) : (isset($argv[1]) ? trim($argv[1]) : "");
	if ($database_name == "") {
		echo json_encode(["success" => false, "message" => "Organization database not provided."]);
		exit();
	}

	// only run for a database that belongs to a registered organization
	$select = "SELECT `organization_database`, `send_sms` FROM `organizations` WHERE `organization_database` = ? LIMIT 1";
	$stmt = $conn1->prepare($select);
	$stmt->bind_param("s", $database_name);
	$stmt->execute();
	$organization = $stmt->get_result()->fetch_assoc();
	if (!$organization) {
		echo json_encode(["success" => false, "message" => "Unknown organization database."]);
		exit();
	}

	$conn = new mysqli($hostname, $dbusername, $dbpassword, $organization['organization_database']);
	if (mysqli_connect_errno()) {
		echo json_encode(["success" => false, "message" => "Failed to connect to the organization database."]);
		exit();
	}

	$summary = ["success" => true, "sent" => 0, "failed" => 0, "stale_marked_failed" => 0, "unsendable_marked_failed" => 0];

	// 1. rows a crashed/killed run claimed but never sent: mark failed so the operator
	// can resend them by hand from the SMS list
	$stale_before = date("YmdHis", strtotime("-" . $stale_minutes . " minutes"));
	$update = "UPDATE `sms_tables` SET `sms_status` = 0, `sent_status` = 1 WHERE `processing_id` > 0 AND `sent_status` = 0 AND `date_changed` < ?";
	$stmt = $conn->prepare($update);
	$stmt->bind_param("s", $stale_before);
	$stmt->execute();
	$summary['stale_marked_failed'] = $stmt->affected_rows;

	// 2. org can't send right now: fail the queue instead of leaving it to go out
	// unexpectedly whenever sending is re-enabled
	$sms_api_keys = getSMSKeys($conn);
	$sms_sender = isset($sms_api_keys[3]) ? $sms_api_keys[3] : "";
	if ($organization['send_sms'] == 0 || $sms_sender == "") {
		$update = "UPDATE `sms_tables` SET `sms_status` = 0, `sent_status` = 1 WHERE `processing_id` = 0 AND `sent_status` = 0";
		$stmt = $conn->prepare($update);
		$stmt->execute();
		$summary['unsendable_marked_failed'] = $stmt->affected_rows;
		echo json_encode($summary);
		exit();
	}

	// 3. claim and send batches until the queue is empty
	$lock_name = "sms_agent_claim_" . $organization['organization_database'];
	while (true) {
		// The claim itself is one atomic UPDATE, so two concurrent runs can never claim
		// the same row. The lock only keeps two runs from picking the same new
		// processing_id, which would make each one also read back the other's rows.
		$stmt = $conn->prepare("SELECT GET_LOCK(?, 30) AS got_lock");
		$stmt->bind_param("s", $lock_name);
		$stmt->execute();
		$lock = $stmt->get_result()->fetch_assoc();
		if (!$lock || $lock['got_lock'] != 1) {
			break;
		}

		$result = $conn->query("SELECT COALESCE(MAX(`processing_id`), 0) + 1 AS new_id FROM `sms_tables`");
		$processing_id = (int) $result->fetch_assoc()['new_id'];
		$claimed_at = date("YmdHis");

		$update = "UPDATE `sms_tables` SET `processing_id` = ?, `date_changed` = ? WHERE `processing_id` = 0 AND `sent_status` = 0 AND `deleted` = 0 ORDER BY `sms_id` ASC LIMIT ?";
		$stmt = $conn->prepare($update);
		$stmt->bind_param("isi", $processing_id, $claimed_at, $batch_size);
		$stmt->execute();
		$claimed = $stmt->affected_rows;

		$stmt = $conn->prepare("SELECT RELEASE_LOCK(?)");
		$stmt->bind_param("s", $lock_name);
		$stmt->execute();
		$stmt->get_result();

		if ($claimed <= 0) {
			break;
		}

		$select = "SELECT `sms_id`, `recipient_phone`, `sms_content` FROM `sms_tables` WHERE `processing_id` = ? AND `sent_status` = 0";
		$stmt = $conn->prepare($select);
		$stmt->bind_param("i", $processing_id);
		$stmt->execute();
		$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

		foreach ($rows as $row) {
			$provider_status = send_sms_via_provider($conn, $row['recipient_phone'], $row['sms_content']);
			// null means the number is invalid for the provider
			$message_status = ($provider_status !== null && $provider_status == 1) ? 1 : 0;

			$update = "UPDATE `sms_tables` SET `sms_status` = ?, `sent_status` = 1, `date_sent` = ? WHERE `sms_id` = ?";
			$stmt = $conn->prepare($update);
			$now = date("YmdHis");
			$stmt->bind_param("isi", $message_status, $now, $row['sms_id']);
			$stmt->execute();

			if ($message_status == 1) {
				$summary['sent']++;
			} else {
				$summary['failed']++;
			}
		}
	}

	echo json_encode($summary);
?>
