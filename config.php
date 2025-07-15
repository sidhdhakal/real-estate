<?php
$con = mysqli_connect("db", "user", "pass", "realestatephp");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>
