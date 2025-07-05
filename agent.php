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
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- Internal CSS -->
    <style>
        /* Reset & box sizing */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Muli', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        #page-wrapper {
            width: 100%;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .container {
            width: 90%;
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .col-lg-12, .col-md-6, .col-lg-4 {
            padding: 15px;
        }

        .col-lg-12 {
            width: 100%;
        }

        .col-md-6 {
            width: 100%;
        }

        .col-lg-4 {
            width: 100%;
        }

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

        h2.text-center {
            font-weight: 700;
            font-size: 2.5rem;
            color: rgb(235, 73, 52);
            margin-bottom: 3rem;
        }

        .hover-zoomer {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            cursor: default;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .hover-zoomer:hover {
            transform: scale(1.05);
        }

        .overflow-hidden {
            overflow: hidden;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            flex-shrink: 0;
        }

        .overflow-hidden img {
            display: block;
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .hover-zoomer:hover .overflow-hidden img {
            transform: scale(1.1);
        }

        .py-3 {
            padding-top: 1rem;
            padding-bottom: 1rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h5.text.hover-text-success {
            color: rgb(235, 73, 52);
            margin-bottom: 0.25rem;
            font-weight: 600;
            font-size: 1.25rem;
        }

        p.text {
            margin: 0.2rem 0;
            color: #555;
            font-size: 1rem;
        }

        span.text {
            color: rgb(235, 73, 52);
            font-weight: 600;
            font-size: 1rem;
            margin-top: 0.5rem;
            display: inline-block;
        }
    </style>

    <title>Real Estate PHP - Agents</title>
</head>
<body>

<div id="page-wrapper">
    <div class="row"> 
        <!-- Header -->
        <?php include("include/header.php"); ?>

        <div class="full-row" style="padding: 40px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="text-center mb-5">Agents</h2>
                    </div>
                </div>
                <div class="row">
                    <?php 
                        $query = mysqli_query($con, "SELECT * FROM user WHERE utype='agent'");
                        while($row = mysqli_fetch_array($query)) {
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="hover-zoomer">
                            <div class="overflow-hidden">
                                <img src="admin/user/<?php echo htmlspecialchars($row['uimage']); ?>" alt="agent-image" class="img-fluid">
                            </div>
                            <div class="py-3 text-center">
                                <h5 class="text hover-text-success mb-1">
                                    <?php echo htmlspecialchars($row['uname']); ?>
                                </h5>
                                <p class="text mb-1"><?php echo htmlspecialchars($row['uphone']); ?></p>
                                <p class="text mb-1"><?php echo htmlspecialchars($row['uemail']); ?></p>
                                <span class="text">Agent</span>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include("include/footer.php"); ?>
    </div>
</div>

</body>
</html>
