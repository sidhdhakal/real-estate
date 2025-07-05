<?php 
session_start();
include("config.php");
$error="";
$msg="";
if(isset($_REQUEST['login']))
{
	$email=$_REQUEST['email'];
	$pass=$_REQUEST['pass'];
	$pass= sha1($pass);
	
	if(!empty($email) && !empty($pass))
	{
		$sql = "SELECT * FROM user where uemail='$email' && upass='$pass'";
		$result=mysqli_query($con, $sql);
		$row=mysqli_fetch_array($result);
		   if($row){
			   
				$_SESSION['uid']=$row['uid'];
				$_SESSION['uemail']=$email;
				header("location:index.php");
				
		   }
		   else{
			   $error = "<p class='alert alert-warning'>Email or Password doesnot match!</p> ";
		   }
	}else{
		$error = "<p class='alert alert-warning'>Please Fill all the fields</p>";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login | Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Your CSS is great, it remains unchanged -->
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
            opacity: 0; /* start hidden for fade-in */
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
        .btn-login:active {
            background-color: rgb(220, 65, 45);
            border-color: rgb(220, 65, 45);
            color: white;
        }
        .forgot-pass {
            display: block;
            margin-top: 15px;
            text-align: right;
            font-size: 0.9rem;
            color: rgb(235, 73, 52);
            text-decoration: none;
            transition: color 0.3s;
        }
        .forgot-pass:hover {
            color: rgb(200, 50, 40);
            text-decoration: underline;
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
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
    </style>
</head>
<body>

<?php include("include/header.php"); ?>

<div class="login-wrapper">
  <h1 style="color: rgb(235, 73, 52);">Login</h1>
    <p class="account-subtitle">Access to your dashboard</p>
    
    <?php echo $error; // This will now display our secure error messages ?>
    
    <form method="post" novalidate>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <input type="password" name="pass" class="form-control" placeholder="Password" required>
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>

    <a href="forgot-password.php" class="forgot-pass">Forgot Password?</a>

    <div class="text-center dont-have">
        Don't have an account? <a href="register.php" class="btn-custom">Register</a>
    </div>
</div>

<?php include("include/footer.php"); ?>

<!-- Your Javascript is also fine and remains unchanged -->
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const emailInput = this.email;
    const email = emailInput.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        e.preventDefault();
        alert("Please enter a valid email address.");
        emailInput.focus();
        return false;
    }
});
</script>

</body>
</html>