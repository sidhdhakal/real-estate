<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");
require_once 'verify_signature.php';

if(!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit;
}

$error = "";
$msg = "";

if(isset($_POST['add'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $ptype = $_POST['ptype'];
    $bhk = $_POST['bhk'];
    $bed = $_POST['bed'];
    $balc = $_POST['balc'];
    $hall = $_POST['hall'];
    $stype = $_POST['stype'];
    $bath = $_POST['bath'];
    $kitc = $_POST['kitc'];
    $floor = $_POST['floor'];
    $price = $_POST['price'];
    $city = $_POST['city'];
    $asize = $_POST['asize'];
    $loc = $_POST['loc'];
    $state = $_POST['state'];
    $status = $_POST['status'];
    $uid = $_SESSION['uid'];
    $totalfloor = $_POST['totalfl'];
    $isFeatured = $_POST['isFeatured'];

    // Upload private key
    $privateKeyFile = $_FILES['private_key']['tmp_name'];
    $privateKey = file_get_contents($privateKeyFile);

    // Images
    $aimage = $_FILES['aimage']['name'];
    $aimage1 = $_FILES['aimage1']['name'];
    $aimage2 = $_FILES['aimage2']['name'];
    $aimage3 = $_FILES['aimage3']['name'];
    $aimage4 = $_FILES['aimage4']['name'];

    $temp_name  = $_FILES['aimage']['tmp_name'];
    $temp_name1 = $_FILES['aimage1']['tmp_name'];
    $temp_name2 = $_FILES['aimage2']['tmp_name'];
    $temp_name3 = $_FILES['aimage3']['tmp_name'];
    $temp_name4 = $_FILES['aimage4']['tmp_name'];

    // Move uploaded images
    move_uploaded_file($temp_name,"admin/property/$aimage");
    move_uploaded_file($temp_name1,"admin/property/$aimage1");
    move_uploaded_file($temp_name2,"admin/property/$aimage2");
    move_uploaded_file($temp_name3,"admin/property/$aimage3");
    move_uploaded_file($temp_name4,"admin/property/$aimage4");

    // Sign data using digital signature
   $signature = '';
$data = $price;

$res = createDigitalSignature($privateKey, $data);

if (is_array($res)) {
    if (!empty($res["error"])) {
        $error = "<p class='alert alert-danger'>" . $res["message"] . "</p>";
        $base64Signature = "";
    } else {
        $base64Signature = isset($res["signature"]) ? $res["signature"] : "";
    }
} else {
    $error = "<p class='alert alert-danger'>Digital signature function failed.</p>";
    $base64Signature = "";
}


    // Insert into DB
    $sql = "INSERT INTO property 
    (title, pcontent, type, bhk, stype, bedroom, bathroom, balcony, kitchen, hall, floor, size, price, location, city, state, 
    pimage, pimage1, pimage2, pimage3, pimage4, uid, status, totalfloor, isFeatured, digital_signature) 
    VALUES (
        '$title','$content','$ptype','$bhk','$stype','$bed','$bath','$balc','$kitc','$hall','$floor','$asize','$price',
        '$loc','$city','$state','$aimage','$aimage1','$aimage2','$aimage3','$aimage4','$uid','$status','$totalfloor',
        '$isFeatured','$base64Signature')";

    $result = mysqli_query($con, $sql);
    
    if($result) {
        $msg = "<p class='alert alert-success'>Property Inserted Successfully</p>";
    } else {
        $error = "<p class='alert alert-warning'>Property Not Inserted. Some Error Occurred.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="shortcut icon" href="images/favicon.ico" />
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet" />

    <title>Real Estate PHP</title>

    <style>
        :root {
            --primary-color: rgb(235, 73, 52);
            --primary-color-dark: #d63e2a;
            --background-color: #f5f5f5;
            --form-bg: #fff;
            --border-radius: 10px;
            --box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            --font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-family);
            background-color: var(--background-color);
        }

        #page-wrapper {
            padding: 30px 15px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .property-form-section {
            padding: 50px 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .property-form-header {
            font-weight: 700;
            font-size: 28px;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 15px;
            position: relative;
        }

        .property-form-header::after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background-color: var(--primary-color);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .form-card {
            background: var(--form-bg);
            padding: 40px;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
        }

        .section-title {
            font-size: 20px;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 5px;
        }

        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin-bottom: 25px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 calc(50% - 10px);
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            flex: 1 1 100%;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="file"],
        select,
        textarea {
            padding: 10px;
            font-size: 15px;
            border: 2px solid var(--primary-color);
            border-radius: 6px;
            outline: none;
            background-color: #fafafa;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="file"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color-dark);
            box-shadow: 0 0 5px var(--primary-color-dark);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-button {
            margin-top: 30px;
            text-align: center;
        }

        .submit-button input {
            background-color: var(--primary-color);
            color: #fff;
            padding: 12px 40px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-button input:hover {
            background-color: var(--primary-color-dark);
            transform: scale(1.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-group {
                flex: 1 1 100%;
            }
        }

        /* Alert styles */
        .alert {
            font-size: 0.9rem;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>

    <div id="page-wrapper">
        <div class="row">
            <!-- Header -->
            <?php include("include/header.php");?>

            <!-- Submit property -->
            <div class="property-form-section">
                <h2 class="property-form-header">Submit Property</h2>
                <div class="form-card">
                    <?php
                    if ($error !== "") {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                    if ($msg !== "") {
                        echo "<div class='alert alert-success'>$msg</div>";
                    }
                    ?>
                    <form method="post" enctype="multipart/form-data">
                        <div class="section-title">Basic Information</div>
                        <div class="divider"></div>

                        <div class="form-row">
                            <div class="form-group full">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" required placeholder="Enter Title" />
                            </div>
                            <div class="form-group full">
                                <label for="private_key">Private Key</label>
                                <input type="file" id="private_key" name="private_key" required accept=".pem" />
                            </div>
                            <div class="form-group full">
                                <label for="content">Content</label>
                                <textarea id="content" class="tinymce" name="content" rows="10" cols="30" placeholder="Enter Description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="ptype">Property Type</label>
                                <select id="ptype" name="ptype" required>
                                    <option value="">Select Type</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="flat">Flat</option>
                                    <option value="building">Building</option>
                                    <option value="house">House</option>
                                    <option value="villa">Villa</option>
                                    <option value="office">Office</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stype">Selling Type</label>
                                <select id="stype" name="stype" required>
                                    <option value="">Select Status</option>
                                    <option value="rent">Rent</option>
                                    <option value="sale">Sale</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bath">Bathroom</label>
                                <input type="text" id="bath" name="bath" required placeholder="Enter Bathroom (1–10)" />
                            </div>
                            <div class="form-group">
                                <label for="kitc">Kitchen</label>
                                <input type="text" id="kitc" name="kitc" required placeholder="Enter Kitchen (1–10)" />
                            </div>
                            <div class="form-group">
                                <label for="bhk">BHK</label>
                                <select id="bhk" name="bhk" required>
                                    <option value="">Select BHK</option>
                                    <option value="1 BHK">1 BHK</option>
                                    <option value="2 BHK">2 BHK</option>
                                    <option value="3 BHK">3 BHK</option>
                                    <option value="4 BHK">4 BHK</option>
                                    <option value="5 BHK">5 BHK</option>
                                    <option value="1,2 BHK">1,2 BHK</option>
                                    <option value="2,3 BHK">2,3 BHK</option>
                                    <option value="2,3,4 BHK">2,3,4 BHK</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bed">Bedroom</label>
                                <input type="text" id="bed" name="bed" required placeholder="Enter Bedroom (1–10)" />
                            </div>
                            <div class="form-group">
                                <label for="balc">Balcony</label>
                                <input type="text" id="balc" name="balc" required placeholder="Enter Balcony (1–10)" />
                            </div>
                            <div class="form-group">
                                <label for="hall">Hall</label>
                                <input type="text" id="hall" name="hall" required placeholder="Enter Hall (1–10)" />
                            </div>
                        </div>

                        <div class="section-title">Price & Location</div>
                        <div class="divider"></div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="floor">Floor</label>
                                <select id="floor" name="floor" required>
                                    <option value="">Select Floor</option>
                                    <option value="1st Floor">1st Floor</option>
                                    <option value="2nd Floor">2nd Floor</option>
                                    <option value="3rd Floor">3rd Floor</option>
                                    <option value="4th Floor">4th Floor</option>
                                    <option value="5th Floor">5th Floor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" id="price" name="price" required placeholder="Enter Price" />
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required placeholder="Enter City" />
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" required placeholder="Enter State" />
                            </div>
                            <div class="form-group">
                                <label for="totalfl">Total Floor</label>
                                <select id="totalfl" name="totalfl" required>
                                    <option value="">Select Floor</option>
                                    <option value="1 Floor">1 Floor</option>
                                    <option value="2 Floor">2 Floor</option>
                                    <option value="3 Floor">3 Floor</option>
                                    <option value="4 Floor">4 Floor</option>
                                    <option value="5 Floor">5 Floor</option>
                                    <option value="6 Floor">6 Floor</option>
                                    <option value="7 Floor">7 Floor</option>
                                    <option value="8 Floor">8 Floor</option>
                                    <option value="9 Floor">9 Floor</option>
                                    <option value="10 Floor">10 Floor</option>
                                    <option value="11 Floor">11 Floor</option>
                                    <option value="12 Floor">12 Floor</option>
                                    <option value="13 Floor">13 Floor</option>
                                    <option value="14 Floor">14 Floor</option>
                                    <option value="15 Floor">15 Floor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="asize">Area Size</label>
                                <input type="text" id="asize" name="asize" required placeholder="Enter Area Size (in sqft)" />
                            </div>
                            <div class="form-group full">
                                <label for="loc">Address</label>
                                <input type="text" id="loc" name="loc" required placeholder="Enter Address" />
                            </div>
                        </div>

                        <div class="section-title">Image & Status</div>
                        <div class="divider"></div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="aimage">Image 1</label>
                                <input type="file" id="aimage" name="aimage" required accept="image/*" />
                            </div>
                            <div class="form-group">
                                <label for="aimage1">Image 2</label>
                                <input type="file" id="aimage1" name="aimage1" required accept="image/*" />
                            </div>
                            <div class="form-group">
                                <label for="aimage2">Image 3</label>
                                <input type="file" id="aimage2" name="aimage2" required accept="image/*" />
                            </div>
                            <div class="form-group">
                                <label for="aimage3">Image 4</label>
                                <input type="file" id="aimage3" name="aimage3" required accept="image/*" />
                            </div>
                            <div class="form-group">
                                <label for="aimage4">Image 5</label>
                                <input type="file" id="aimage4" name="aimage4" required accept="image/*" />
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="available">Available</option>
                                    <option value="sold out">Sold Out</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="isFeatured"><b>Is Featured?</b></label>
                                <select id="isFeatured" name="isFeatured" required>
                                    <option value="">Select...</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="submit-button">
                            <input type="submit" value="Submit Property" name="add" />
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <?php include("include/footer.php");?>
        </div>
    </div>
</body>

</html>
