<?php 
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit;
}

$error = "";
$msg = "";

if (isset($_POST['addabout'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $aimage = $_FILES['aimage']['name'];
    $temp_name1 = $_FILES['aimage']['tmp_name'];

    move_uploaded_file($temp_name1, "upload/$aimage");

    $sql = "INSERT INTO about (title, content, image) VALUES ('$title', '$content', '$aimage')";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $msg = "<div class='alert alert-success'>✅ About Us section added successfully!</div>";
    } else {
        $error = "<div class='alert alert-danger'>❌ Error! Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LM HOMES | About Us</title>

    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        :root {
            --primary-color: #a8a432;
        }

        body {
            background-color: #f8f9fa;
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 700;
        }

        .form-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
        }

        .form-wrapper h4 {
            font-weight: 600;
            margin-bottom: 25px;
            color: var(--primary-color);
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 30px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #94902b;
        }

        .alert-success,
        .alert-danger {
            border-radius: 6px;
            font-weight: 500;
            padding: 10px 20px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .breadcrumb {
            background-color: transparent;
            padding-left: 0;
            margin-top: 10px;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
        }
    </style>
</head>

<body>

    <div class="main-wrapper">
        <?php include("header.php"); ?>
       
        <div class="page-wrapper">
            <div class="content container-fluid">

                <!-- Page Header -->
                <div class="page-header text-center">
					<br>
	   <br>
	   <br>
                    <h3 class="page-title">About Us ✍️</h3>
                    <p class="text-muted">Add content for your About section</p>
                </div>

                <!-- Feedback Messages -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <?php echo $error; ?>
                        <?php echo $msg; ?>
                    </div>
                </div>

                <!-- About Form -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-wrapper">
                            <form method="post" enctype="multipart/form-data">
                                <h4>Add About Us Content</h4>

                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>

                                <div class="form-group">
                                    <label>Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" name="aimage" required>
                                </div>

                                <div class="form-group">
                                    <label>Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="content" required></textarea>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-submit" name="addabout">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="assets/plugins/tinymce/tinymce.min.js"></script>
    <script src="assets/plugins/tinymce/init-tinymce.min.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>

</body>
</html>
