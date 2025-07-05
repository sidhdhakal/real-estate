<?php
session_start();
require("config.php");

// --- HARD-CODED AES SETTINGS (NOT RECOMMENDED FOR PRODUCTION) ---
// These constants must be defined here to be used by openssl_decrypt below.
define('AES_CIPHER', 'aes-256-cbc');
define('AES_KEY', 'b4a8e7f1c9d2g5h3j6k8m0n2p5r7t9v1'); // Must be 32 characters
define('AES_IV', 'q3s6u9x2z5c8f1g4');               // Must be 16 characters
// --- END OF AES SETTINGS ---


// --- AUTHENTICATION CHECK ---
if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>LM Homes | Admin Feedback</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/plugins/datatables/dataTables.bootstrap4.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include("header.php"); ?>

<div class="page-wrapper">
    <div class="content container-fluid">
        <br><br><br>
        <div class="page-header text-center" style="margin-bottom: 30px;">
            <h3 class="page-title" style="color: #a8a432; font-weight: 700;">Feedback</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div class="card-header" style="background-color: #a8a432; color: white; border-radius: 12px 12px 0 0; font-weight: 600;">
                        <h4 class="card-title" style="margin: 0;">Feedback List</h4>
                    </div>
                    <div class="card-body" style="background: #fff; border-radius: 0 0 12px 12px; padding: 20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead style="background-color: #a8a432; color: white; font-weight: 600;">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Feedback</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT feedback.fid, feedback.fdescription, feedback.status, feedback.date, user.uname, user.uemail 
                                            FROM feedback 
                                            JOIN user ON feedback.uid = user.uid";
                                    $query = mysqli_query($con, $sql);

                                    if (!$query) {
                                        echo "<tr><td colspan='7'>Database Query Failed: " . htmlspecialchars(mysqli_error($con)) . "</td></tr>";
                                    } else {
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            
                                            // --- DIRECT, HARD-CODED DECRYPTION ---
                                            // The openssl_decrypt call is placed directly here, using the constants from the top of the file.
                                            // This is the implementation without any functions or external files.
                                            $decrypted_feedback = openssl_decrypt($row['fdescription'], AES_CIPHER, AES_KEY, 0, AES_IV);
                                            
                                            // Prepare the feedback for display, handling cases where decryption fails
                                            $display_feedback = ($decrypted_feedback !== false)
                                                ? nl2br(htmlspecialchars($decrypted_feedback))
                                                : '<i>[Unable to decrypt message]</i>';
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt++; ?></td>
                                                <td><?php echo htmlspecialchars($row['uname']); ?></td>
                                                <td><?php echo htmlspecialchars($row['uemail']); ?></td>
                                                <td><?php echo $display_feedback; ?></td>
                                                <td><?php echo $row['status'] == 1 ? 'Published' : 'Pending'; ?></td>
                                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                                <td>
                                                    <a href="feedbackedit.php?id=<?php echo $row['fid']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="feedbackdelete.php?id=<?php echo $row['fid']; ?>" onclick="return confirm('Are you sure you want to delete this?');" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>