<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");
require_once 'verify_signature.php';

if(!isset($_SESSION['uemail']))
{
	header("location:login.php");
}

$error="";
$msg="";
if (isset($_POST['add'])) {
    // require 'db.php'; // make sure this connects $con to MySQL

	 // Sanitize inputs
	 $title ="New Jadibutti";
	 $content = "Fine";
	 $ptype ="1";
	 $bhk ="bkh";
	 $bed = 1;
	 $balc = 1;
	 $hall = 1;
	 $stype = "stype";
	 $bath = 1;
	 $kitc = 1;
	 $floor = "10";
	 $price = 10;
	 $city = "KTM";
	 $asize = 1;
	 $loc = "ktm";
	 $state = "lth";
	 $status = "pending";
	 $uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	 $feature = 1;
	 $totalfloor = "10";
	 $isFeatured = 1;
	 $aimage ="asdf";
	 $aimage1 = "asdf";
	 $aimage2 ="asdf";
	 $aimage3 = "asdf";
	 $aimage4 = "asdf";
	 $fimage ="asdf";
	 $fimage1 = "asdf";
	 $fimage2 = "asdf";

    // Handle private key upload
    $privateKeyFile = $_FILES['private_key']['tmp_name'];
    $privateKey = file_get_contents($privateKeyFile);


    // Sign data
    $signature = '';
	$data = $title;

    $res = createDigitalSignature($privateKey,$data);
    if (!$res["error"]) {
        $error = $res["message"];
    }
    $base64Signature = $res["signature"];
    // // Insert into DB
    $sql = "INSERT INTO property 
            (title, pcontent, type, bhk, stype, bedroom, bathroom, balcony, kitchen, hall, floor, size, price, location, city, state, feature, 
            pimage, pimage1, pimage2, pimage3, pimage4, uid, status, mapimage, topmapimage, groundmapimage, totalfloor, isFeatured, digital_signature)
            VALUES (
            '$title', '$content', '$ptype', '$bhk', '$stype', '$bed', '$bath', '$balc', '$kitc', '$hall', '$floor', '$asize', '$price', '$loc', 
            '$city', '$state', '$feature', '$aimage', '$aimage1', '$aimage2', '$aimage3', '$aimage4', '$uid', '$status', '$fimage', '$fimage1', 
            '$fimage2', '$totalfloor', '$isFeatured', '$base64Signature')";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $msg = "<p class='alert alert-success'>✅ Property Inserted Successfully</p>";
    } else {
        $error = "<p class='alert alert-danger'>❌ Insert Failed: " . mysqli_error($con) . "</p>";
    }
}						
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta Tags -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="images/favicon.ico">

    <!--	Fonts
	========================================================-->
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">

    <!--	Css Link
	========================================================-->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/layerslider.css">
    <link rel="stylesheet" type="text/css" href="css/color.css">
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/flaticon/flaticon.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <!-- FOR MORE PROJECTS visit: codeastro.com -->
    <!--	Title
	=========================================================-->
    <title>Real Estate PHP</title>
</head>

<body>
    <div id="page-wrapper">
        <div class="row">
            <!--	Header start  -->
            <?php include("include/header.php");?>
            <!--	Submit property   -->
            <div class="full-row">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2 class="text-secondary double-down-line text-center">Submit Property</h2>
                        </div>
                    </div>
                    <div class="row p-5 bg-white">
                        <form method="post" enctype="multipart/form-data">
                            <div class="description">
                                <h5 class="text-secondary">Basic Information</h5>
                                <hr>
                                <?php echo $error; ?>
                                <?php echo $msg; ?>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Title</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="title" required
                                                    placeholder="Enter Title">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Private Key</label>
                                            <div class="col-lg-9">
                                                <input type="file" class="form-control" name="private_key" required>
                                            </div>
                                        </div>
                                        <!-- FOR MORE PROJECTS visit: codeastro.com -->
                                        <div class="form-group row">
                                            <label class="col-lg-2 col-form-label">Content</label>
                                            <div class="col-lg-9">
                                                <textarea class="tinymce form-control" name="content" rows="10"
                                                    cols="30"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <input type="submit" value="Submit Property" class="btn btn-info" name="add"
                                    style="margin-left:200px;">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--	Submit property   -->


            <!--	Footer   start-->
            <?php include("include/footer.php");?>
            <!--	Footer   start-->

            <!-- Scroll to top -->
            <a href="#" class="bg-secondary text-white hover-text-secondary" id="scroll"><i
                    class="fas fa-angle-up"></i></a>
            <!-- End Scroll To top -->
        </div>
    </div>
    <!-- Wrapper End -->
    <!-- FOR MORE PROJECTS visit: codeastro.com -->
    <!--	Js Link
============================================================-->
    <script src="js/jquery.min.js"></script>
    <script src="js/tinymce/tinymce.min.js"></script>
    <script src="js/tinymce/init-tinymce.min.js"></script>
    <!--jQuery Layer Slider -->
    <script src="js/greensock.js"></script>
    <script src="js/layerslider.transitions.js"></script>
    <script src="js/layerslider.kreaturamedia.jquery.js"></script>
    <!--jQuery Layer Slider -->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/tmpl.js"></script>
    <script src="js/jquery.dependClass-0.1.js"></script>
    <script src="js/draggable-0.1.js"></script>
    <script src="js/jquery.slider.js"></script>
    <script src="js/wow.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>