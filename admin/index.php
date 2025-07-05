<?php 
session_start();
include("config.php");
$error = "";

if (isset($_POST['login'])) {
    $user = trim($_POST['user']);
    $pass = trim($_POST['pass']);
    
    if (!empty($user) && !empty($pass)) {
        // Prepare statement to prevent SQL Injection
        $stmt = $con->prepare("SELECT auser, apass FROM admin WHERE auser = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Compare plain text password
            if ($pass === $row['apass']) {
                $_SESSION['auser'] = $user;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = '<p class="alert alert-danger">Invalid User Name or Password</p>';
            }
        } else {
            $error = '<p class="alert alert-danger">Invalid User Name or Password</p>';
        }
        $stmt->close();
    } else {
        $error = '<p class="alert alert-warning">Please fill all the fields!</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login | Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
        }
        .login-wrapper {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(235, 73, 52, 0.4);
            border: 2px solid rgb(235, 73, 52);
            animation: slideIn 1s ease;
        }
        h1 {
            color: rgb(235, 73, 52);
            font-weight: 700;
            margin-bottom: 15px;
            animation: fadeIn 1.5s ease forwards;
        }
        .account-subtitle {
            margin-bottom: 25px;
            color: #555;
            animation: fadeIn 2s ease forwards;
        }
        .form-control {
            border: 1.5px solid #ccc;
            transition: 0.3s;
        }
        .form-control:focus {
            border-color: rgb(235, 73, 52);
            box-shadow: 0 0 8px rgba(235, 73, 52, 0.5);
            outline: none;
        }
        .btn-login {
            background-color: white;
            color: rgb(235, 73, 52);
            border: 2px solid rgb(235, 73, 52);
            padding: 8px 20px;
            font-weight: 600;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .btn-login:hover {
            background-color: rgb(235, 73, 52);
            color: white;
        }
        .btn-login:active {
            background-color: rgb(220, 65, 45);
            border-color: rgb(220, 65, 45);
            color: white;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <h1>Admin Login</h1>
    <p class="account-subtitle">Access to your dashboard</p>

    <?php echo $error; ?>

    <form method="post" autocomplete="off">
        <div class="form-group">
            <input type="text" name="user" class="form-control" placeholder="User Name" required>
        </div>
        <div class="form-group">
            <input type="password" name="pass" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>
</div>

<!-- JS -->
<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>  
