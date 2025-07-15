<?php

// Read database configuration from environment variables
// Use the variable NAMES from your app.yaml file
$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USERNAME');
$db_pass = getenv('DB_PASSWORD');
$db_name = getenv('DB_DATABASE');

// Establish the connection
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (mysqli_connect_errno()) {
    // This provides a helpful error in the pod's logs
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    die("Database connection failed. Please check server logs for more info.");
}

?>

