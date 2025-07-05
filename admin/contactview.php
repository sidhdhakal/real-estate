<?php
session_start();
require("config.php");
////code
 
if(!isset($_SESSION['auser']))
{
	header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <title>LM Homes | Admin</title>
		
	
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="assets/css/feathericon.min.css">
		

		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
	 <style>
		.table{
         table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

table thead {
    background-color: #2F80ED;
    color: #ffffff;
}

table th, table td {
    padding: 14px 16px;
    text-align: left;
    font-size: 15px;
    border-bottom: 1px solid #eaeaea;
}

table tbody tr:hover {
    background-color: #f2f6ff;
    transition: 0.2s ease-in-out;
}

table tbody tr:last-child td {
    border-bottom: none;
}

		}
	 </style>
    </head>
    <body>
	
		<!-- Main Wrapper -->
		
		
			<!-- Header -->
				<?php include("header.php"); ?>
			<!-- /Sidebar -->
			
			<!-- Page Wrapper -->
           <div class="page-wrapper">
    <div class="content container-fluid">
        <br><br><br>

        <!-- Page Header -->
        <div class="page-header text-center" style="margin-bottom: 30px;">
            <h3 class="page-title" style="color: #a8a432; font-weight: 700;">Contact</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div class="card-header" style="background-color: #a8a432; color: white; border-radius: 12px 12px 0 0; font-weight: 600;">
                        <h4 class="card-title" style="margin: 0;">Contact List</h4>
                    </div>
                    <div class="card-body" style="background: #fff; border-radius: 0 0 12px 12px; padding: 20px;">
                        <div class="table-responsive">
                            <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                <thead style="background-color: #a8a432; color: white; font-weight: 600;">
                                    <tr>
                                        <th style="padding: 12px; border: 1px solid #ddd;">#</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Name</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Email</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Phone</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Subject</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Message</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $query = mysqli_query($con, "SELECT * FROM contact");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr style="border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                                        <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $cnt; ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: 600; color: #333; text-align: left;"><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: left;"><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: left;"><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: left;"><?php echo htmlspecialchars($row['subject']); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: left;"><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd;">
                                            <a href="contactdelete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure to delete this?');" style="text-decoration: none;">
                                                <button style="background-color: #d9534f; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                                    Delete
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                            $cnt++;
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

			<!-- /Main Wrapper -->

		
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
        <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		
		<!-- Datatables JS -->
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
		
		<!-- Custom JS -->
		<script  src="assets/js/script.js"></script>
		
    </body>
</html>
