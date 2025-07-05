<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");
require_once 'verify_signature.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Property Detail - Real Estate PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Base Style Reset */
        body {
            font-family: 'Muli', sans-serif;
            background-color: #f4f6f8;
            color: #333;
            line-height: 1.6;
        }

        .text {
            color: #e74c3c; /* Red accent color */
        }

        .signature-message {
            font-weight: bold;
            font-size: 1.05em;
            padding: 12px 20px;
            background-color: #eafaf1;
            color: #27ae60;
            border-left: 6px solid #2ecc71;
            margin: 20px 0;
            border-radius: 6px;
        }

        /* Slider */
        #slider-container {
            width: 100%;
            max-width: 960px;
            margin: 40px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        #slider-container img {
            width: 100%;
            max-height: 550px;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        #slider-container img:hover {
            transform: scale(1.02);
        }

        /* Property Details */
        .property-details {
            padding: 20px 0;
        }

        .property-quantity {
            background-color: #e74c3c;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            color: #fff;
        }

        .property-quantity ul {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .property-quantity li {
            padding: 10px 16px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            font-size: 14px;
        }

        /* Tables */
        table.table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .table td, .table th {
            vertical-align: middle;
            padding: 12px;
            border-top: 1px solid #dee2e6;
        }

        /* Agent Section */
        .agent-contact {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .agent-contact img {
            width: 100%;
            max-width: 100px;
            height: auto;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .agent-contact h6 {
            font-weight: 700;
            margin-bottom: 10px;
            color: #e74c3c;
            text-transform: capitalize;
        }

        .agent-contact ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
            font-size: 14px;
            color: #555;
        }

        .agent-contact ul li {
            margin-bottom: 6px;
        }

        /* Appointment Form */
        .appointment-form {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .appointment-form label {
            font-weight: 600;
            color: #333;
        }

        .appointment-form input,
        .appointment-form textarea {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            width: 100%;
            margin-top: 6px;
            margin-bottom: 16px;
            transition: border-color 0.3s;
        }

        .appointment-form input:focus,
        .appointment-form textarea:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 5px rgba(231, 76, 60, 0.4);
        }

        .appointment-form button {
            background-color: #e74c3c;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .appointment-form button:hover {
            background-color: #c0392b;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .property-quantity ul {
                flex-direction: column;
                gap: 8px;
            }

            .appointment-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div id="page-wrapper">
    <?php include("include/header.php"); ?>

    <div class="container">
        <?php
        $id = isset($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;
        if ($id <= 0) {
            echo "<p>Invalid property ID.</p>";
        } else {
            $query = mysqli_query($con, "SELECT property.*, user.* FROM `property`, `user` WHERE property.uid=user.uid AND pid='$id'");
            if (mysqli_num_rows($query) == 0) {
                echo "<p>Property not found.</p>";
            } else {
                while ($row = mysqli_fetch_assoc($query)) {
                    $digitalSignature = $row['digital_signature'];
                    $publicKey        = $row['public_key'];
                    $price            = $row['price'];
                    $result = verifyDigitalSignature($price, $digitalSignature, $publicKey);
        ?>

        <!-- === Property Image Slider === -->
       <div class="row justify-content-center text-center">
    <div class="col-12">
        <div id="slider-container">
            <div class="slider-wrapper">
                <?php
$imageFields = ['pimage', 'pimage1', 'pimage2', 'pimage3', 'pimage4'];
foreach ($imageFields as $field) {
    if (!empty($row[$field])) {
        $imagePath = "admin/property/" . htmlspecialchars($row[$field]);
        echo '<img class="slider-slide" src="' . $imagePath . '" alt="Property Image">';
    }
}
?>

            </div>
            <button class="slider-btn prev-btn" aria-label="Previous">&#10094;</button>
            <button class="slider-btn next-btn" aria-label="Next">&#10095;</button>
        </div>
    </div>
</div>

<style>
    #slider-container {
        position: relative;
        max-width: 960px;
        margin: 40px auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        background: #fff;
    }
    .slider-wrapper {
        display: flex;
        overflow-x: scroll;
        scroll-behavior: smooth;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none;  /* IE 10+ */
    }
    .slider-wrapper::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    .slider-slide {
        flex: 0 0 auto;
        width: 300px;
        height: 200px;
        object-fit: cover;
        border-radius: 12px;
        margin: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        user-select: none;
    }
    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.4);
        color: white;
        border: none;
        padding: 12px 18px;
        font-size: 28px;
        cursor: pointer;
        border-radius: 50%;
        user-select: none;
        transition: background 0.3s ease;
        z-index: 10;
    }
    .slider-btn:hover {
        background: rgba(0,0,0,0.7);
    }
    .prev-btn {
        left: 16px;
    }
    .next-btn {
        right: 16px;
    }
</style>

<script>
    (function(){
        const wrapper = document.querySelector('#slider-container .slider-wrapper');
        const prevBtn = document.querySelector('#slider-container .prev-btn');
        const nextBtn = document.querySelector('#slider-container .next-btn');
        const slideWidth = 320; // image width + margin (300 + 2*10 margin left and right)

        prevBtn.addEventListener('click', () => {
            wrapper.scrollBy({ left: -slideWidth, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            wrapper.scrollBy({ left: slideWidth, behavior: 'smooth' });
        });
    })();
</script>


        <div class="signature-message"><?= htmlspecialchars($result['message']); ?></div>

        <!-- === Property Title & Price === -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-table px-3 py-2 rounded text-white text-capitalize" style="background-color: #e74c3c;">For <?= htmlspecialchars($row['stype']); ?></div>
                <h5 class="mt-2 text-secondary text-capitalize"><?= htmlspecialchars($row['title']); ?></h5>
                <span class="d-block text-capitalize"><i class="fas fa-map-marker-alt text-success"></i> <?= htmlspecialchars($row['location']); ?></span>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="text mt-2">Rs. <?= htmlspecialchars($row['price']); ?></h5>
            </div>
        </div>

        <!-- === Property Details === -->
        <div class="property-details">
            <div class="property-quantity px-4 pt-4 w-100">
                <ul>
                    <li><?= htmlspecialchars($row['size']); ?> Sqft</li>
                    <li><?= htmlspecialchars($row['bedroom']); ?> Bedroom</li>
                    <li><?= htmlspecialchars($row['bathroom']); ?> Bathroom</li>
                    <li><?= htmlspecialchars($row['balcony']); ?> Balcony</li>
                    <li><?= htmlspecialchars($row['hall']); ?> Hall</li>
                    <li><?= htmlspecialchars($row['kitchen']); ?> Kitchen</li>
                </ul>
            </div>

            <h4 class="text my-4">Description</h4>
            <p><?= htmlspecialchars($row['pcontent']); ?></p>

            <h5 class="mt-5 mb-4 text">Property Summary</h5>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td><strong>BHK</strong></td><td><?= htmlspecialchars($row['bhk']); ?></td>
                        <td><strong>Property Type</strong></td><td><?= htmlspecialchars($row['type']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Floor</strong></td><td><?= htmlspecialchars($row['floor']); ?></td>
                        <td><strong>Total Floor</strong></td><td><?= htmlspecialchars($row['totalfloor']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>City</strong></td><td><?= htmlspecialchars($row['city']); ?></td>
                        <td><strong>State</strong></td><td><?= htmlspecialchars($row['state']); ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- === Agent Details === -->
            <h5 class="mt-5 mb-4 text">Contact Agent</h5>
            <div class="agent-contact pt-3 pb-5">
                <div class="row align-items-center">
                    <div class="col-sm-3 text-center">
                        <img src="admin/user/<?= htmlspecialchars($row['uimage']); ?>" alt="Agent Image" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-sm-9">
                        <h6 class="text text-capitalize"><?= htmlspecialchars($row['uname']); ?></h6>
                        <ul class="list-unstyled">
                            <li><strong>Phone:</strong> <?= htmlspecialchars($row['uphone']); ?></li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($row['uemail']); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Appointment Form === -->
        <div class="appointment-form">
            <?php if (isset($_SESSION['error'])) { echo '<p style="color:red;">'.htmlspecialchars($_SESSION['error']).'</p>'; unset($_SESSION['error']); } ?>
            <?php if (isset($_SESSION['success'])) { echo '<p style="color:green;">'.htmlspecialchars($_SESSION['success']).'</p>'; unset($_SESSION['success']); } ?>
            <h5 class="text mb-4">Book Appointment</h5>
            <?php if (isset($_SESSION['uid'])) { ?>
            <form action="add_appointment.php" method="POST">
                <input type="hidden" name="pid" value="<?= $id; ?>">
                <input type="hidden" name="uid" value="<?= $_SESSION['uid']; ?>">
                <input type="hidden" name="agent_uid" value="<?= htmlspecialchars($row['uid']); ?>">
                <div class="form-group"><label>Property Title:</label><input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']); ?>" readonly></div>
                <div class="form-group"><label>Date:</label><input type="date" name="date" class="form-control" required></div>
                <div class="form-group"><label>Time:</label><input type="time" name="time" class="form-control" required></div>
                <div class="form-group"><label>Message (optional):</label><textarea name="message" class="form-control" rows="3"></textarea></div>
                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </form>
            <?php } else { echo '<p>Please <a href="login.php">login</a> to book an appointment.</p>'; } ?>
        </div>

        <?php } } } ?>
    </div>

    <?php include("include/footer.php"); ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const slideWidth = 320; // 300px width + 2*10px margin

    prevBtn.addEventListener('click', () => {
        sliderWrapper.scrollBy({ left: -slideWidth, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', () => {
        sliderWrapper.scrollBy({ left: slideWidth, behavior: 'smooth' });
    });

    // Optional: swipe support for touch devices
    let isDown = false;
    let startX;
    let scrollLeft;

    sliderWrapper.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - sliderWrapper.offsetLeft;
        scrollLeft = sliderWrapper.scrollLeft;
    });
    sliderWrapper.addEventListener('mouseleave', () => {
        isDown = false;
    });
    sliderWrapper.addEventListener('mouseup', () => {
        isDown = false;
    }); 
    sliderWrapper.addEventListener('mousemove', (e) => {
        if(!isDown) return;
        e.preventDefault();
        const x = e.pageX - sliderWrapper.offsetLeft;
        const walk = (x - startX) * 2; //scroll-fast
        sliderWrapper.scrollLeft = scrollLeft - walk;
    });
});
</script>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
