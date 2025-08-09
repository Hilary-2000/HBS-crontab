<?php
	// allow only certain ip addresses
	// $allowed_ip_address = "172.71.178.94";
	// $server_ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
	// if (php_sapi_name() === 'cli') {
	// 	// Running from CLI (Terminal)
	// 	$server_ip_address = '172.71.178.94'; // Assume local execution
	// } else {
	// 	// Running from Web
	// 	$server_ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
	// }
	// if ($allowed_ip_address !== $server_ip_address) {
	// 	echo "Server ip address not allowed \"".$server_ip_address."\"";
	// 	exit();
	// 	return 0;
	// }



    
    function formatKenyanPhone($number) {
        // Remove spaces, dashes, and plus sign
        $number = preg_replace('/[\s\-\+]/', '', $number);

        // If it starts with "07", replace with "2547"
        if (preg_match('/^07\d{8}$/', $number)) {
            return '254' . substr($number, 1);
        }

        // If it starts with "+2547" (after plus removal)
        if (preg_match('/^2547\d{8}$/', $number)) {
            return $number;
        }

        // If it starts with "7" only, add "254"
        if (preg_match('/^7\d{8}$/', $number)) {
            return '254' . $number;
        }

        // Invalid number
        return false;
    }
?>