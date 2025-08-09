<?php
    // loop through every organization database to activate and deactivate clients
	// $dbname = "mikrotik_cloud_manager";
	// $hostname = "localhost";
	// $dbusername = 'hillary';
	// $dbpassword = "Francis=Son123";
	// // $dbusername = 'root';
	// // $dbpassword = "";
	// $conn1 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
	// // Check connection
	// if (mysqli_connect_errno()) {
	// 	die("Failed to connect to MySQL: " . mysqli_connect_error());
	// 	exit();
	// }

    // // DB CREDENTIALS
    $dbname = 'mikrotik_cloud_manager';
    include "db_credential.php";
    if(!isset($_SESSION)) {
        session_start(); 
    }
    $conn1 = new mysqli($hostname, $dbusername, $dbpassword, $dbname);
    // Check connection
    if (mysqli_connect_errno()) {
        die("Failed to connect to MySQL: " . mysqli_connect_error());
        exit();
    }
?>