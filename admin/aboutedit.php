<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit;
}

$error = "";
$msg = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM about WHERE id='$id'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
} else {
    header("location:about-view.php");
    exit;
}

if (isset($_POST['updateabout'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($_FILES['aimage']['name'] != "") {
        $aimage = $_FILES['aimage']['name'];
        $temp_name1 = $_FILES['aimage']['tmp_name'];
        move_uploaded_file($temp_name1, "upload/$aimage");

        $sql = "UPDATE about SET title='$title', content='$content', image='$aimage' WHERE id='$id'";
    } else {
        $sql = "UPDATE about SET title='$title', content='$content' WHERE id='$id'";
    }

    $result = mysqli_query($con, $sql);
    if ($result) {
        $msg = "<div class='alert alert-success'>✅ About Us section updated successfully!</div>";
    } else {
        $error = "<div class='alert alert-danger'>❌ Error! Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit About Us | LM HOMES</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    </style>
</head>

<body>

    <div class="main-wrapper">
        <?php include("header.php"); ?>

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="page-header text-center">
                    <h3 class="page-title">Edit About Us 📝</h3>
                    <p class="text-muted">Update the content for your About section</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <?php echo $error; ?>
                        <?php echo $msg; ?>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-wrapper">
                            <form method="post" enctype="multipart/form-data">
                                <h4>Edit About Us Content</h4>

                                <div class="form-group">
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Current Image:</label><br>
                                    <img src="upload/<?php echo $row['image']; ?>" width="100" height="auto" alt="Current Image">
                                </div>

                                <div class="form-group">
                                    <label>Change Image</label>
                                    <input type="file" class="form-control" name="aimage">
                                </div>

                                <div class="form-group">
                                    <label>Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="content" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-submit" name="updateabout">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>

</body>
</html>
