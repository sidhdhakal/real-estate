<?php
session_start();
include("config.php");

if(!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appid = intval($_POST['appid']);
    $status = $_POST['status'];

    // Allowed status values
    $valid_status = ['pending', 'confirmed', 'cancelled', 'completed'];
    if (!in_array($status, $valid_status)) {
        die("Invalid status selected.");
    }

    $update = mysqli_query($con, "UPDATE appointment SET status='$status' WHERE appid='$appid'");

    if ($update) {
        header("Location: userproperty.php?msg=Appointment status updated successfully");
        exit;
    } else {
        die("Error updating appointment status: " . mysqli_error($con));
    }
} else {
    header("Location: userproperty.php");
    exit;
}
