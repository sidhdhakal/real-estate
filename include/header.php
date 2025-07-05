<?php
// Start session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection
include("config.php"); // Adjust this path if needed

// Check user type for Submit Property button
$showSubmitButton = false;
if (isset($_SESSION['uemail'])) {
    $userEmail = $_SESSION['uemail'];
    $query = mysqli_query($con, "SELECT utype FROM user WHERE uemail = '$userEmail' LIMIT 1");
    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if ($user['utype'] === 'agent') {
            $showSubmitButton = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Real Estate Navbar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .navbar {
            background-color: rgb(235, 73, 52) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .navbar .nav-link {
            color: white !important;
            font-weight: 500;
            margin-right: 15px;
            transition: all 0.3s ease-in-out;
        }

        .navbar .nav-link:hover {
            color: #ffdab9 !important;
            text-decoration: underline;
        }

        .navbar-brand img {
            height: 50px;
        }

        .top-header {
            background-color: #f4f4f4;
            font-size: 14px;
            padding: 5px 0;
        }

        .top-contact a {
            color: #333;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .top-contact a:hover {
            color: rgb(235, 73, 52);
        }

        .btn-success {
            background-color: white !important;
            color: rgb(235, 73, 52) !important;
            border: 2px solid white;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 30px;
            transition: 0.3s ease-in-out;
        }

        .btn-success:hover {
            background-color: rgba(255, 255, 255, 0.9) !important;
            color: rgb(235, 73, 52);
        }

        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu .nav-link {
            color: rgb(235, 73, 52) !important;
            padding: 8px 15px;
        }

        .dropdown-menu .nav-link:hover {
            background-color: rgba(235, 73, 52, 0.1);
            border-radius: 5px;
        }

        .full-container {
            width: 100%;
            padding: 20px;
            background-color: #eb4934;
            color: white;
        }

        .top-contact .btn-top-header {
            color: rgb(235, 73, 52);
            font-weight: 600;
            text-decoration: none;
            padding: 5px 12px;
            border: 2px solid rgb(235, 73, 52);
            border-radius: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .top-contact .btn-top-header:hover {
            background-color: rgb(235, 73, 52);
            color: white;
            text-decoration: none;
        }

        .btn-submit {
            background-color: rgb(235, 73, 52) !important;
            border-color: rgb(235, 73, 52) !important;
            color: white !important;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: rgb(200, 60, 40) !important;
            border-color: rgb(200, 60, 40) !important;
            color: white !important;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link:focus,
        .navbar .nav-link:active {
            text-decoration: none !important;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header id="header" class="w-100">

    <!-- Top header -->
    <div class="top-header">
        <div class="container">
            <div class="row">
                <div class="col-md-4 ml-auto text-right">
                    <div class="top-contact">
                        <ul class="list-inline m-0">
                            <li class="list-inline-item">
                                <?php if (isset($_SESSION['uemail'])) { ?>
                                    <a href="logout.php" class="btn-top-header">Logout</a>
                                <?php } else { ?>
                                    <a href="login.php" class="btn-top-header">Login</a>
                                    <span style="color: #eb4934; margin: 0 8px;">|</span>
                                    <a href="register.php" class="btn-top-header">Register</a>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main navbar -->
    <div class="main-nav py-2">
        <div class="full-container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.php"></a>

                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        <li class="nav-item"><a class="nav-link" href="property.php">Properties</a></li>
                        <li class="nav-item"><a class="nav-link" href="agent.php">Agent</a></li>

                        <?php if (isset($_SESSION['uemail'])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">My Account</a>
                                <div class="dropdown-menu">
                                    <a class="nav-link" href="profile.php">Profile</a>
                                    <a class="nav-link" href="userproperty.php">Your Property</a>
                                    <a class="nav-link" href="logout.php">Logout</a>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <?php } ?>
                    </ul>

                    <!-- ✅ Submit Property Button (Only for agents) -->
                    <?php if ($showSubmitButton): ?>
                        <a class="btn btn-submit d-none d-xl-block" href="submitproperty.php">Submit Property</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</header>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
