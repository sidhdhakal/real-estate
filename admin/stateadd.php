<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
}

$error = "";
$msg = "";

if (isset($_POST['insert'])) {
    $state = $_POST['state'];
    if (!empty($state)) {
        $sql = "INSERT INTO state (sname) VALUES ('$state')";
        $result = mysqli_query($con, $sql);
        $msg = $result ? "<p class='text-success'>State Inserted Successfully</p>" : "<p class='text-danger'>* State Not Inserted</p>";
    } else {
        $error = "<p class='text-danger'>* Fill all the Fields</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LM Homes | State Management</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Feathericons -->
    <link rel="stylesheet" href="assets/css/feathericon.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .card-header {
            background-color: #a8a432;
            color: white;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
        }
        .btn-submit {
            background-color: #EB4934;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
        }
        .btn-edit {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .btn-delete {
            background-color: #F44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="page-wrapper">
    <div class="content container-fluid">
        <br><br><br>
        <div class="page-header text-center">
            <h3 class="page-title" style="color: #a8a432; font-weight: 700;">State</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div class="card-header">
                        <h4 class="card-title">Add State</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $error; ?>
                        <?php echo $msg; ?>
                        <?php if (isset($_GET['msg'])) echo $_GET['msg']; ?>

                        <form method="post">
                            <div class="form-group">
                                <label>State Name</label>
                                <input type="text" class="form-control" name="state" required>
                            </div>
                            <div class="form-group text-right">
                                <input type="submit" class="btn btn-submit" value="Submit" name="insert">
                            </div>
                        </form>
                    </div>
                </div>

                <br>

                <div class="card" style="border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.08);">
                    <div class="card-header">
                        <h4 class="card-title">State List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead style="background-color: #a8a432; color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>State Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM state");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt; ?></td>
                                        <td style="text-align: left;"><?php echo htmlspecialchars($row['sname']); ?></td>
                                        <td>
                                            <a href="stateedit.php?id=<?php echo $row['sid']; ?>">
                                                <button class="btn-edit">Edit</button>
                                            </a>
                                            <a href="statedelete.php?id=<?php echo $row['sid']; ?>" onclick="return confirm('Are you sure to delete this?');">
                                                <button class="btn-delete">Delete</button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $cnt++; } ?>
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

<!-- Custom Script -->
<script src="assets/js/script.js"></script>

</body>
</html>
