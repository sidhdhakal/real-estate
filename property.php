<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");						
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Real Estate PHP</title>

    <!-- Internal CSS -->
    <style>
    /* Global box-sizing */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        background-color: #f9f9f9;
    }

    .text-center {
        text-align: center;
    }

    .mb-4 {
        margin-bottom: 1.5rem;
    }

    .container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px; /* Adjusted for gutters */
    }

    .col-md-6, .col-lg-4 {
        padding: 15px;
        width: 100%;
    }

    /* Responsive widths */
    @media (min-width: 768px) {
        .col-md-6 {
            width: 50%;
        }
    }

    @media (min-width: 992px) {
        .col-lg-4 {
            width: 33.3333%;
        }
    }

    .featured-thumb {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .featured-thumb:hover {
        transform: translateY(-5px);
    }

    .overlay-black {
        position: relative;
        flex-shrink: 0;
    }

    .overlay-black img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .sale {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgb(235, 73, 52);
        padding: 5px 10px;
        font-weight: bold;
        font-size: 14px;
        border-radius: 4px;
        color: white;
    }

    .price {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background-color: rgba(255,255,255,0.9);
        padding: 5px 10px;
        font-weight: bold;
        font-size: 16px;
        border-radius: 4px;
        color: rgb(235, 73, 52);
    }

    .featured-thumb-data {
        background-color: #fff;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 15px;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .featured-thumb-data h5 {
        margin: 0 0 10px;
        font-size: 18px;
        color: rgb(235, 73, 52);
    }

    .featured-thumb-data span {
        font-size: 14px;
        color: #555;
    }

    .bg-gray {
        background-color: #f1f1f1;
        padding: 10px 15px;
        margin-top: 10px;
        border-radius: 6px;
    }

    .quantity ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: nowrap;  /* No wrapping */
        justify-content: flex-start;
        gap: 20px;
    }

    .quantity ul li {
        font-size: 14px;
        color: rgb(235, 73, 52);
        white-space: nowrap;
    }

    .p-3 {
        padding: 15px;
    }

    .p-4 {
        padding: 15px;
        border-top: 1px solid #eee;
        font-size: 14px;
    }

    .float-left {
        float: left;
        font-weight: 600;
        color: #555;
    }

    .float-right {
        float: right;
        color: #999;
    }

    /* Clear floats */
    .p-4::after {
        content: "";
        display: table;
        clear: both;
    }

    .hover-text-success a {
        color: rgb(235, 73, 52);
        text-decoration: none;
    }

    .hover-text-success a:hover {
        color: green;
        text-decoration: underline;
    }

    .full-row {
        padding: 40px 0;
    }

    .d-inline-block {
        display: inline-block;
    }

    .w-100 {
        width: 100%;
    }

    .shadow-one {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .hover-zoomer {
        transition: transform 0.3s ease;
    }

    .hover-zoomer:hover {
        transform: scale(1.03);
    }
    </style>
</head>
<body>

<div id="page-wrapper">
    <div class="row"> 
        <!-- Header -->
        <?php include("include/header.php");?>
        <br><br><br>
        <div class="full-row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center mb-4" style="color: rgb(235, 73, 52);">Recent Property</h2>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <?php 
                            $query = mysqli_query($con, "SELECT property.*, user.uname FROM `property` JOIN `user` ON property.uid = user.uid ORDER BY date DESC");
                            while ($row = mysqli_fetch_array($query)) {
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="featured-thumb hover-zoomer mb-4">
                                    <div class="overlay-black">
                                        <img src="admin/property/<?php echo htmlspecialchars($row['18']); ?>" alt="Property Image">
                                        <div class="sale">For <?php echo htmlspecialchars($row['5']); ?></div>
                                        <div class="price"><b>Rs.<?php echo htmlspecialchars($row['13']); ?></b></div>
                                    </div>
                                    <div class="featured-thumb-data shadow-one">
                                        <div class="p-3">
                                            <h5 class="hover-text-success mb-2 text-capitalize">
                                                <a href="propertydetail.php?pid=<?php echo htmlspecialchars($row['0']); ?>">
                                                    <?php echo htmlspecialchars($row['1']); ?>
                                                </a>
                                            </h5>
                                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['14']); ?></span>
                                        </div>
                                        <div class="bg-gray quantity">
                                            <ul>
                                                <li><span><?php echo htmlspecialchars($row['12']); ?></span> Sqft</li>
                                                <li><span><?php echo htmlspecialchars($row['6']); ?></span> Beds</li>
                                                <li><span><?php echo htmlspecialchars($row['7']); ?></span> Baths</li>
                                                <li><span><?php echo htmlspecialchars($row['9']); ?></span> Kitchen</li>
                                                <li><span><?php echo htmlspecialchars($row['8']); ?></span> Balcony</li>
                                            </ul>
                                        </div>
                                        <div class="p-4 d-inline-block w-100">
                                            <div class="float-left text-capitalize">By: <?php echo htmlspecialchars($row['uname']); ?></div>
                                            <div class="float-right"><?php echo date('d-m-Y', strtotime($row['date'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include("include/footer.php");?>
    </div>
</div>

</body>
</html>
