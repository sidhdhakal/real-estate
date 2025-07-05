<?php
session_start();
include("config.php");
if (!isset($_SESSION['auser'])) {
    header("location:index.php");
    exit;
}

$error = "";
$msg = "";

// Handle AJAX request
if (isset($_POST['action']) && $_POST['action'] == 'insert_city') {
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';

    if (!empty($state) && !empty($city)) {
        $stmt = $con->prepare("INSERT INTO city (cname, sid) VALUES (?, ?)");
        $stmt->bind_param("si", $city, $state);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'City Inserted Successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'City Not Inserted']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fill all the fields']);
    }
    exit; // Important: stop further output for AJAX
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ventura - Add City</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <!-- Header -->
    <?php include("header.php"); ?>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Add City</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add City</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- City Add Section -->
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">City Details</h4>
                        </div>
                        <div class="card-body">
                            <div id="response-message"></div>
                            <form id="cityForm" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="stateSelect">State Name</label>
                                    <select class="form-control" id="stateSelect" name="state" required>
                                        <option value="">Select State</option>
                                        <?php
                                        $query1 = mysqli_query($con, "SELECT * FROM state");
                                        while ($row1 = mysqli_fetch_row($query1)) {
                                            echo '<option value="' . $row1[0] . '">' . htmlspecialchars($row1[1]) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cityInput">City Name</label>
                                    <input type="text" class="form-control" id="cityInput" name="city" required />
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- City List Section -->
            <div class="row mt-5">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">City List</h4>
                        </div>
                        <div class="card-body" id="cityListContainer">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="cityListBody">
                                    <?php
                                    $query = mysqli_query($con, "SELECT city.*, state.sname FROM city JOIN state ON city.sid = state.sid");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        echo "<tr>
                                        <td>" . $cnt++ . "</td>
                                        <td>" . htmlspecialchars($row['cname']) . "</td>
                                        <td>" . htmlspecialchars($row['sname']) . "</td>
                                        <td>
                                            <a href='cityedit.php?id=" . $row['cid'] . "'><button class='btn btn-info'>Edit</button></a>
                                            <a href='citydelete.php?id=" . $row['cid'] . "'><button class='btn btn-danger'>Delete</button></a>
                                        </td>
                                        </tr>";
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

    <!-- jQuery -->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap Core JS -->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#cityForm').submit(function (e) {
                e.preventDefault();

                let state = $('#stateSelect').val();
                let city = $('#cityInput').val().trim();

                if (!state) {
                    $('#response-message').html('<p class="alert alert-warning">Please select a state.</p>');
                    return;
                }
                if (!city) {
                    $('#response-message').html('<p class="alert alert-warning">Please enter a city name.</p>');
                    return;
                }

                $.ajax({
                    url: '',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'insert_city',
                        state: state,
                        city: city
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#response-message').html('<p class="alert alert-success">' + response.message + '</p>');
                            // Clear form fields
                            $('#cityForm')[0].reset();
                            // Reload city list table
                            reloadCityList();
                        } else {
                            $('#response-message').html('<p class="alert alert-danger">' + response.message + '</p>');
                        }
                    },
                    error: function () {
                        $('#response-message').html('<p class="alert alert-danger">An error occurred. Please try again.</p>');
                    }
                });
            });

            function reloadCityList() {
                $.ajax({
                    url: 'citylist_ajax.php',
                    method: 'GET',
                    success: function (data) {
                        $('#cityListBody').html(data);
                    },
                    error: function () {
                        $('#cityListBody').html('<tr><td colspan="4">Failed to load city list.</td></tr>');
                    }
                });
            }
        });
    </script>
</body>

</html>
