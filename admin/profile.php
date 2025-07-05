<?php 
session_start();
require("config.php");

if(!isset($_SESSION['auser'])) {
    header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LM HOMES | Admin Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background: linear-gradient(to right, #f8f8f8, #fff);
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-wrapper {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .profile-pic {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-pic img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #a8a432;
        }

        .profile-name {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        .profile-email {
            text-align: center;
            color: #888;
            margin-bottom: 25px;
        }

        .info-box {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            color: #333;
        }

        .info-item label {
            font-weight: 600;
            color: #666;
        }

        .title-bar {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            color: #a8a432;
            margin-bottom: 20px;
        }

        @media (max-width: 576px) {
            .profile-wrapper {
                margin: 40px 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="container">
    <?php
    $id = $_SESSION['auser'];
    $sql = "SELECT * FROM admin WHERE auser='$id'";
    $result = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($result)) {
    ?>
    <div class="profile-wrapper">
        <div class="title-bar">Admin Profile</div>

        <div class="profile-pic">
            <img src="assets/img/profiles/avatar-01.png" alt="Admin Photo">
        </div>

        <div class="profile-name"><?php echo strtoupper($row['1']); ?></div>
        <div class="profile-email"><?php echo $row['2']; ?></div>

        <div class="info-box">
            <div class="info-item">
                <label>Full Name:</label>
                <div><?php echo $row['1']; ?></div>
            </div>
            <div class="info-item">
                <label>Date of Birth:</label>
                <div><?php echo $row['4']; ?></div>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <div><?php echo $row['2']; ?></div>
            </div>
            <div class="info-item">
                <label>Mobile:</label>
                <div><?php echo $row['5']; ?></div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<!-- Scripts -->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
