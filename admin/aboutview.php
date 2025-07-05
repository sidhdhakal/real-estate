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
        <title>LM Homes | About</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Feathericon CSS -->
        <link rel="stylesheet" href="assets/css/feathericon.min.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
    <body>
	
		<!-- Main Wrapper -->
	
		
			<!-- Header -->
				<?php include("header.php"); ?>
			<!-- /Sidebar -->
			
			<div class="page-wrapper">
    <div class="content container-fluid">
  <br>
  <br>
  <br>
        <!-- Page Header -->
        <div class="page-header text-center" style="margin-bottom: 30px;">
            <h3 class="page-title">View About</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div class="card-header" style="background-color: #a8a432; color: white; border-radius: 12px 12px 0 0; font-weight: 600;">
                        <h4 class="card-title" style="margin: 0;">List Of About</h4>
                        <?php 
                            if(isset($_GET['msg'])) echo "<div style='margin-top:10px; font-weight: 500;'>" . htmlspecialchars($_GET['msg']) . "</div>";
                        ?>
                    </div>
                    <div class="card-body" style="background: #fff; border-radius: 0 0 12px 12px; padding: 20px;">
                        <div class="table-responsive">
                            <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                <thead style="background-color: #a8a432; color: white; font-weight: 600;">
                                    <tr>
                                        <th style="padding: 12px; border: 1px solid #ddd;">#</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Title</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Content</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Image</th>
                                        <th style="padding: 12px; border: 1px solid #ddd;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $query = mysqli_query($con, "SELECT * FROM about");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr style="border: 1px solid #ddd; text-align: center; vertical-align: middle;">
                                        <td style="padding: 12px; border: 1px solid #ddd;"><?php echo $cnt; ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: 600; color: #333;"><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: left;"><?php echo nl2br(htmlspecialchars($row['content'])); ?></td>
                                        <td style="padding: 12px; border: 1px solid #ddd;">
                                            <img src="upload/<?php echo htmlspecialchars($row['image']); ?>" height="100" width="100" style="border-radius: 8px; object-fit: cover;">
                                        </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;">
                                            <a href="aboutedit.php?id=<?php echo $row['id']; ?>" style="text-decoration: none;">
                                                <button style="background-color: #a8a432; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-right: 6px;">
                                                    Edit
                                                </button>
                                            </a>
                                            <a href="aboutdelete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure to delete this?');" style="text-decoration: none;">
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

		
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
        <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
		
		<!-- Custom JS -->
		<script  src="assets/js/script.js"></script>
		
    </body>
</html>
