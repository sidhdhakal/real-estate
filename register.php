<?php
// WARNING: This version uses the insecure sha1() hashing method for demo purposes.

include('verify_signature.php');
include("config.php"); 

$error = "";
$msg = "";

if (isset($_POST['reg'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = $_POST['pass']; // Get the plain-text password
    $utype = $_POST['utype'];

    if (empty($name) || empty($email) || empty($phone) || empty($pass) || empty($_FILES['uimage']['name'])) {
        $error = "<p class='alert alert-warning'>Please fill all the required fields.</p>";
    } else {
        $stmt = $con->prepare("SELECT uemail FROM user WHERE uemail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "<p class='alert alert-warning'>This Email ID is already registered.</p>";
        } else {
            $keyPair = generatePublicPrivateKey();

            if (isset($keyPair['error']) && $keyPair['error']) {
                $error = "<p class='alert alert-danger'>" . htmlspecialchars($keyPair['message']) . "</p>";
            } else {
                $public_key = $keyPair['public_key'];
                $private_key = $keyPair['private_key'];

                $uimage = $_FILES['uimage']['name'];
                $temp_name1 = $_FILES['uimage']['tmp_name'];
                $image_path = "admin/user/" . basename($uimage);

                // ===============================================
                // --- INSECURE CHANGE: Using sha1() for password ---
                $sha1_pass = sha1($pass); 
                // ===============================================

                $sql = "INSERT INTO user (uname, uemail, uphone, upass, utype, uimage, public_key) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $con->prepare($sql);
                // We use the new $sha1_pass variable here
                $insert_stmt->bind_param("sssssss", $name, $email, $phone, $sha1_pass, $utype, $uimage, $public_key);
                
                $result = $insert_stmt->execute();

                if ($result) {
                    move_uploaded_file($temp_name1, $image_path);

                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . htmlspecialchars($email) . '_private_key.pem"');
                    header('Content-Length: ' . strlen($private_key));
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    
                    echo $private_key;
                    exit();

                } else {
                    $error = "<p class='alert alert-warning'>Registration Failed. Please try again. Error: " . $insert_stmt->error . "</p>";
                }
                $insert_stmt->close();
            }
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Register | Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />

    <style>
        /* Your CSS is perfectly fine */
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
            animation: slideIn 1s ease forwards;
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
        .btn-register {
            background-color: white;
            color: rgb(235, 73, 52);
            border: 2px solid rgb(235, 73, 52);
            padding: 10px;
            font-weight: 600;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s ease;
            width: 100%;
        }
        .btn-register:hover {
            background-color: rgb(235, 73, 52);
            color: white;
        }
        .alert {
            font-size: 0.9rem;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        .form-group {
            margin-bottom: 1rem; /* Added margin for better spacing */
        }
        .form-check-inline {
            margin-right: 10px;
        }
        label.form-check-label {
            cursor: pointer;
            font-weight: 500;
        }
        .dont-have {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
        .dont-have a {
            color: rgb(235, 73, 52);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .dont-have a:hover {
            color: rgb(200, 50, 40);
            text-decoration: underline;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #444;
        }
    </style>
</head>
<body>

<?php include("include/header.php"); ?>

<div class="login-wrapper">
    <h1>Register</h1>
    <p class="account-subtitle">Access to our dashboard</p>

    <?php echo $error; ?>
    <?php echo $msg; ?>

    <form method="post" action="register.php" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Your Name*" required />
        </div>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Your Email*" required />
        </div>
        <div class="form-group">
            <input type="number" name="phone" class="form-control" placeholder="Your Phone*" maxlength="10" required />
        </div>
        <div class="form-group">
            <input type="password"  name="pass" min="0"  class="form-control" placeholder="Your Password*" required />
        </div>

        <div class="form-group">
            <label>Register As:</label>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="utype" value="user" id="userTypeUser" checked />
                <label class="form-check-label" for="userTypeUser">User</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="utype" value="agent" id="userTypeAgent" />
                <label class="form-check-label" for="userTypeAgent">Agent</label>
            </div>
        </div>

        <div class="form-group">
            <label for="uimage">User Image*</label>
            <input type="file" name="uimage" class="form-control-file" id="uimage" required />
        </div>

        <button type="submit" name="reg" class="btn-register">Register</button>
    </form>

    <div class="dont-have">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

<?php include("include/footer.php"); ?>

<!-- Bootstrap JS -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>