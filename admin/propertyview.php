<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
    <title>Ventura - Property Details</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png" />

    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="assets/css/feathericon.min.css" />
    <link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/datatables/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/datatables/select.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/plugins/datatables/buttons.bootstrap4.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <?php include("header.php"); ?>

    <div class="page-wrapper">
        <div class="content container-fluid">

            <br /><br /><br />

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                        <div class="card-body" style="padding: 25px;">
                            <h4 class="header-title mt-0 mb-4" style="font-weight: 700; color: #a8a432; text-align: center;">
                                Property Details
                            </h4>

                            <?php if (isset($_GET['msg'])) : ?>
                                <p style="color: green; font-weight: 600;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px;">
                                    <thead style="background-color: #a8a432; color: white; font-weight: 600;">
                                        <tr>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Seller Name</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Type</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">BHK</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Sub Type</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Price</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Location</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Status</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Added Date</th>
                                            <th style="padding: 12px; border: 1px solid #ddd;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($con, "
                                            SELECT 
                                                property.pid,
                                                property.type,
                                                property.bhk,
                                                property.stype,
                                                property.price,
                                                property.location,
                                                property.status,
                                                property.date,
                                                user.uname
                                            FROM property
                                            LEFT JOIN user ON property.uid = user.uid
                                        ");

                                        while ($row = mysqli_fetch_assoc($query)) {
                                            echo "<tr style='border: 1px solid #ddd; text-align: center; vertical-align: middle;'>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd; text-align: left; font-weight: 600; color: #333;'>" . htmlspecialchars($row['uname']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['type']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['bhk']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['stype']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['price']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['location']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['status']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>" . htmlspecialchars($row['date']) . "</td>";
                                            echo "<td style='padding: 12px; border: 1px solid #ddd;'>
                                                
                                                <a href='propertydelete.php?id=" . urlencode($row['pid']) . "' onclick=\"return confirm('Are you sure to delete this property?');\" style='text-decoration:none;'>
                                                    <button style='background-color: #d9534f; border: none; color: white; padding: 7px 14px; border-radius: 6px; cursor: pointer; font-weight: 600;'>Delete</button>
                                                </a>
                                            </td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div><!-- end col -->
            </div>
            <!-- end row -->

        </div>
    </div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.select.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
    <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
    <script src="assets/plugins/datatables/buttons.flash.min.js"></script>
    <script src="assets/plugins/datatables/buttons.print.min.js"></script>
    <script src="assets/js/script.js"></script>

</body>

</html>
