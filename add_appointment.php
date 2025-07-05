<?php
session_start();
include("config.php");

// Validate user is logged in
if (!isset($_SESSION['uid'])) {
    $_SESSION['error'] = "Please login to book an appointment.";
    header("Location: propertydetail.php?pid=" . urlencode($_POST['pid'] ?? ''));
    exit();
}

$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
$uid = $_SESSION['uid']; // logged-in user id
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basic validation
if ($pid <= 0 || empty($title) || empty($date) || empty($time)) {
    $_SESSION['error'] = "Please fill all required fields.";
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}

// Verify ownership: fetch property owner id
$sqlOwner = "SELECT uid as agent_uid FROM property WHERE pid = ?";
$stmtOwner = $con->prepare($sqlOwner);
if (!$stmtOwner) {
    $_SESSION['error'] = "Database error: " . $con->error;
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}
$stmtOwner->bind_param("i", $pid);
$stmtOwner->execute();
$resultOwner = $stmtOwner->get_result();

if ($resultOwner->num_rows == 0) {
    $_SESSION['error'] = "Property not found.";
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}

$rowOwner = $resultOwner->fetch_assoc();
$propertyAgentUid = $rowOwner['agent_uid'];
$stmtOwner->close();

// Prevent property owner (agent) from booking appointment on their own property
if ($uid == $propertyAgentUid) {
    $_SESSION['error'] = "Property owners cannot book appointments on their own property.";
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}

// Validate date and time formats (basic)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $_SESSION['error'] = "Invalid date format.";
    header("Location: property-detail.php?pid=" . urlencode($pid));
    exit();
}

if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    $_SESSION['error'] = "Invalid time format.";
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}

// Prevent duplicate appointment for same user & property at same date/time
$sqlCheck = "SELECT * FROM appointment WHERE pid=? AND uid=? AND date=? AND time=?";
$stmtCheck = $con->prepare($sqlCheck);
if (!$stmtCheck) {
    $_SESSION['error'] = "Database error: " . $con->error;
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}
$stmtCheck->bind_param("iiss", $pid, $uid, $date, $time);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    $_SESSION['error'] = "You already have an appointment for this property at the selected date and time.";
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}
$stmtCheck->close();

// Insert appointment
$sqlInsert = "INSERT INTO appointment (pid, uid, agent_uid, title, date, time, message) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $con->prepare($sqlInsert);
if (!$stmt) {
    $_SESSION['error'] = "Database error: " . $con->error;
    header("Location: propertydetail.php?pid=" . urlencode($pid));
    exit();
}
$stmt->bind_param("iiissss", $pid, $uid, $propertyAgentUid, $title, $date, $time, $message);

if ($stmt->execute()) {
    $_SESSION['success'] = "Appointment booked successfully!";
} else {
    $_SESSION['error'] = "Failed to book appointment. Please try again.";
}

$stmt->close();
$con->close();

header("Location: propertydetail.php?pid=" . urlencode($pid));
exit();
?>
