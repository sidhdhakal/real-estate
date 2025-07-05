<?php 
include("config.php");
$error = "";
$msg = "";

if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];

    if (!empty($name) && !empty($email) && !empty($pass) && !empty($dob) && !empty($phone)) {
        $sql = "INSERT INTO admin (auser, aemail, apass, adob, aphone) VALUES ('$name', '$email', '$pass', '$dob', '$phone')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $msg = "<p class='alert alert-success'>Admin registered successfully.</p>";
        } else {
            $error = "<p class='alert alert-danger'>Registration failed. Please try again.</p>";
        }
    } else {
        $error = "<p class='alert alert-warning'>Please fill in all the fields.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register | Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
        }

        .login-wrapper {
            max-width: 500px;
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
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 1rem;
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
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-login:hover {
            background-color: rgb(235, 73, 52);
            color: white;
        }

        .text-center.dont-have {
            margin-top: 20px;
            font-size: 0.95rem;
        }

        .btn-custom {
            background-color: white;
            color: rgb(235, 73, 52);
            border: 2px solid rgb(235, 73, 52);
            padding: 8px 20px;
            font-weight: 600;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: rgb(235, 73, 52);
            color: white;
            text-decoration: none;
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

        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
    </style>
</head>
<body>

<?php include("include/header.php"); ?>

<div class="login-wrapper">
    <h1>Register</h1>
    <p class="account-subtitle">Create a new admin account</p>

    <?php echo $error; ?>
    <?php echo $msg; ?>

    <form method="post" novalidate>
        <input class="form-control" type="text" placeholder="Name" name="name" required>
        <input class="form-control" type="email" placeholder="Email" name="email" required>
        <input class="form-control" type="password" placeholder="Password" name="pass" required>
        <input class="form-control" type="date" placeholder="Date of Birth" name="dob" required>
        <input class="form-control" type="text" placeholder="Phone" name="phone" maxlength="10" required>

        <button type="submit" name="insert" class="btn-login">Register</button>
    </form>

    <div class="text-center dont-have">
        Already have an account? <a href="index.php" class="btn-custom">Login</a>
    </div>
</div>

<?php include("include/footer.php"); ?>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const email = this.email.value.trim();
    const phone = this.phone.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
        e.preventDefault();
        alert("Please enter a valid email.");
        return false;
    }

    if (!/^\d{10}$/.test(phone)) {
        e.preventDefault();
        alert("Please enter a valid 10-digit phone number.");
        return false;
    }
});
</script>

</body>
</html>
