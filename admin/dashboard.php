<?php
session_start();
require("config.php"); // Ensure this file establishes $con (your MySQLi connection)

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit; // Important to exit after a header redirect
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Ventura - Dashboard</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Feathericon CSS -->
    <link rel="stylesheet" href="assets/css/feathericon.min.css">

    <!-- Morris CSS -->
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .dashboard-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 6px solid rgb(168, 164, 50);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .dashboard-icon {
            font-size: 2.5rem;
            color: rgb(168, 164, 50);
            margin-bottom: 10px;
            display: block;
        }

        .dashboard-title {
            font-size: 14px;
            color: #777;
        }

        .dashboard-number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="main-wrapper">
        <?php include("header.php"); ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <br><br><br>
                            <h3 class="page-title">Welcome Admin!</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php
                    $dashboard_items = [
                        ['icon_class' => 'fa fa-users', 'query' => "SELECT COUNT(*) as total FROM user WHERE utype = 'user'", 'title' => '👤 Registered Users'],
                        ['icon_class' => 'fa fa-user-tie', 'query' => "SELECT COUNT(*) as total FROM user WHERE utype = 'agent'", 'title' => '🕴️ Agents'],
                        ['icon_class' => 'fa fa-home', 'query' => "SELECT COUNT(*) as total FROM property", 'title' => '🏠 Total Properties'],
                        ['icon_class' => 'fa fa-building', 'query' => "SELECT COUNT(*) as total FROM property WHERE type = 'apartment'", 'title' => '🏢 Apartments'],
                        ['icon_class' => 'fa fa-home', 'query' => "SELECT COUNT(*) as total FROM property WHERE type = 'house'", 'title' => '🏘️ Houses'],
                        ['icon_class' => 'fa fa-city', 'query' => "SELECT COUNT(*) as total FROM property WHERE type = 'building'", 'title' => '🏬 Buildings'],
                        ['icon_class' => 'fa fa-door-open', 'query' => "SELECT COUNT(*) as total FROM property WHERE type = 'flat'", 'title' => '🛏️ Flats'],
                        ['icon_class' => 'fa fa-dollar-sign', 'query' => "SELECT COUNT(*) as total FROM property WHERE stype = 'sale'", 'title' => '💰 Properties For Sale'],
                        ['icon_class' => 'fa fa-key', 'query' => "SELECT COUNT(*) as total FROM property WHERE stype = 'rent'", 'title' => '🔑 Properties For Rent'],
                    ];

                    foreach ($dashboard_items as $item) {
                        $count = 0;
                        $result = $con->query($item['query']);

                        if ($result && $result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $count = $row['total'];
                        }

                        echo '
                        <div class="col-xl-3 col-sm-6 col-12">
                            <div class="dashboard-card">
                                <div class="dashboard-icon"><i class="' . htmlspecialchars($item['icon_class']) . '"></i></div>
                                <div class="dashboard-number">' . htmlspecialchars($count) . '</div>
                                <div class="dashboard-title">' . htmlspecialchars($item['title']) . '</div>
                            </div>
                        </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Slimscroll JS -->
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Morris JS -->
    <script src="assets/plugins/raphael/raphael.min.js"></script>
    <script src="assets/plugins/morris/morris.min.js"></script>
    <script src="assets/js/chart.morris.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>

</body>

</html>
