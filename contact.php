<?php 
include("config.php");
$error = "";
$msg = "";

if (isset($_POST['send'])) {
    $name    = htmlspecialchars($_POST['name']);
    $email   = htmlspecialchars($_POST['email']);
    $phone   = htmlspecialchars($_POST['phone']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contact (name, email, phone, subject, message) 
                VALUES ('$name', '$email', '$phone', '$subject', '$message')";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $msg = "<p class='alert alert-success'>Message Sent Successfully</p>";
        } else {
            $error = "<p class='alert alert-danger'>Message Failed to Send</p>";
        }
    } else {
        $error = "<p class='alert alert-warning'>Please fill in all fields</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 

    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .contact-wrapper {
            max-width: 600px;
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
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 1.5s ease forwards;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: rgb(235, 73, 52);
            box-shadow: 0 0 8px rgba(235, 73, 52, 0.5);
            outline: none;
        }

        .btn-send {
            background-color: white;
            color: rgb(235, 73, 52);
            border: 2px solid rgb(235, 73, 52);
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 30px;
            transition: 0.3s ease;
            cursor: pointer;
        }

        .btn-send:hover {
            background-color: rgb(235, 73, 52);
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

        .alert {
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>

<?php include("include/header.php"); ?>

<div class="contact-wrapper">
<h1 style="color: rgb(235, 73, 52);">Contact Us</h1>


    <?php echo $msg; ?>
    <?php echo $error; ?>

    <form method="post" action="">
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
        </div>
        <div class="form-group">
            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <input type="text" name="subject" class="form-control" placeholder="Subject" required>
        </div>
        <div class="form-group">
            <textarea name="message" rows="5" class="form-control" placeholder="Write your message here..." required></textarea>
        </div>
        <div class="text-center">
            <button type="submit" name="send" class="btn-send">Send Message</button>
        </div>
    </form>
</div>

<?php include("include/footer.php"); ?>

</body>
</html>
