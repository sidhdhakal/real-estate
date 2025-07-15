<?php
ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);
session_start();
include("config.php"); // Establishes $con

// Fetch states for the dropdown
$states_query = mysqli_query($con, "SELECT sid, sname FROM state ORDER BY sname ASC");
$states = [];
while ($state_row = mysqli_fetch_assoc($states_query)) {
    $states[] = $state_row;
}

// Fetch cities for the dropdown
// For a dependent dropdown (cities change based on selected state),
// you'd typically use JavaScript and an AJAX call.
// For now, we'll load all cities.
$cities_query = mysqli_query($con, "SELECT cid, cname, sid FROM city ORDER BY cname ASC");
$cities = [];
while ($city_row = mysqli_fetch_assoc($cities_query)) {
    $cities[] = $city_row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Real Estate PHP</title>
    <style>
        /* Typography and General Layout */
        body {
            font-family: 'Muli', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        h1, h2, h5 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: rgb(235, 73, 52);
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        /* Header Animation */
        .animated-heading {
            font-weight: 700;
            overflow: hidden;
            position: relative;
        }

        .glow-text {
            display: inline-block;
            white-space: nowrap;
            animation: slide-glow 15s linear infinite;
            font-size: 32px; /* Adjusted for Nepali font */
            color: rgb(235, 73, 52);
            text-shadow: 0 0 5px #eb4934, 0 0 10px #ff6f61, 0 0 15px #ff6f61;
            padding-left: 100%; /* Start off-screen */
        }

        @keyframes slide-glow {
            0% { transform: translateX(0%); opacity: 0; } /* Start off-screen right, fade in */
            10% { transform: translateX(-20%); opacity: 1; }
            40% { transform: translateX(-80%); }
            50% { transform: translateX(-100%); opacity: 1; } /* Fully visible, centered */
            60% { transform: translateX(-120%); }
            90% { transform: translateX(-180%); opacity: 1; }
            100% { transform: translateX(-200%); opacity: 0; } /* Slide off-screen left, fade out */
        }


        /* Button Styling */
        .btn {
            color: rgb(235, 73, 52);
            font-weight: 600;
            text-decoration: none;
            padding: 10px 30px;
            border: 2px solid rgb(235, 73, 52);
            border-radius: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            cursor: pointer;
        }

        .btn:hover {
            background-color: rgb(235, 73, 52);
            color: white;
        }

        /* Banner Area */
        .slider-banner1 {
            /* background: linear-gradient(rgba(30,30,30,0.7), rgba(30,30,30,0.7)), url('images/banner/cartoon.png') center/cover no-repeat; */
            /* height: 520px; */
            position: relative;
        }

        .login-wrapper {
            max-width: 900px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid rgb(235, 73, 52);
            border-radius: 20px;
        }

        /* Property Grid */
        .text-center.mb-4 {
            font-weight: bold;
            font-size: 2.2rem;
        }

        .featured-thumb {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .featured-thumb:hover {
            transform: translateY(-5px);
        }

        .overlay-black {
            position: relative;
            width: 100%;
            padding-top: 65%; /* Aspect ratio for the image container */
            background-color: #eee; /* Placeholder background */
            overflow: hidden;
        }

        .overlay-black img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .featured-thumb:hover .overlay-black img {
            transform: scale(1.05);
        }

        .sale {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgb(235, 73, 52);
            color: white; /* Ensure text is visible */
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
            z-index: 1;
        }

        .price {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-size: 18px;
            font-weight: bold;
            color: rgb(235, 73, 52); /* Price color */
            background-color: rgba(255,255,255,0.8); /* Slight background for readability */
            padding: 3px 7px;
            border-radius: 3px;
            z-index: 1;
        }

        .featured-thumb-data {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .featured-thumb-data h5 a {
            color: rgb(235, 73, 52);
            font-weight: 600;
            text-decoration: none;
        }
         .featured-thumb-data h5 a:hover {
            text-decoration: underline;
        }

        .featured-thumb-data span {
            font-size: 14px;
            color: #555;
        }
        .featured-thumb-data span i {
            margin-right: 5px;
        }

        .quantity {
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
            padding: 10px 15px;
        }

        .quantity ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .quantity ul li {
            font-size: 13px;
            color: rgb(235, 73, 52);
            flex: 1 1 48%; /* Adjust for spacing */
            margin-bottom: 5px;
        }
         .quantity ul li span {
            font-weight: bold;
            color: #333;
         }

        .p-4.d-inline-block { /* Renamed from original for clarity */
            background: #fff;
            border-top: 1px solid #eee;
            padding: 10px 15px; /* Adjusted padding */
            font-size: 13px;
            color: #444;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .glow-text {
                font-size: 20px; /* Further adjust for smaller screens */
                 animation: slide-glow 10s linear infinite; /* Faster animation on mobile */
            }

            .btn {
                padding: 8px 20px;
                font-size: 14px;
            }

            .login-wrapper {
                padding: 20px;
            }
            .featured-thumb {
                margin-bottom: 20px;
            }
        }
        @media (max-width: 576px) {
            .quantity ul li {
                 flex: 1 1 100%; /* Stack items on very small screens */
            }
        }

    </style>
     <!-- Font Awesome for icons (if you use them, like fa-map-marker-alt) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div id="page-wrapper">
        <div class="row">
            <?php include("include/header.php"); ?>
            <div class="slider-banner1 position-relative" style="background: linear-gradient(rgba(30,30,30,0.7), rgba(30,30,30,0.7)), url('images/banner/cartoon.png') center/cover no-repeat; height: 520px;">
                <div class="container h-100 d-flex justify-content-center align-items-center position-relative" style="z-index: 2;">
                    <div class="bg-white rounded-4 shadow-lg p-5 login-wrapper">
                        <h1 class="animated-heading text-dark mb-4 text-center">
                            <span class="glow-text">घर किनबेच चाहिएमा हामीलाई सम्झनुहोस्।</span>
                        </h1>
                      <!-- You can place this form wherever you need it, for example, on index.php -->

<form method="post" action="propertygrid.php">
    <div class="row g-3">
        <!-- Property Type -->
        <div class="col-md-4 col-sm-6">
            <label for="propertyType" class="form-label">Select Type</label>
            <select id="propertyType" name="type" class="form-select form-control" style="border-radius: 12px;">
                <option value="" selected>All Types</option>
                <option value="apartment">Apartment</option>
                <option value="flat">Flat</option>
                <option value="building">Building</option>
                <option value="house">House</option>
                <option value="villa">Villa</option>
                <option value="office">Office</option>
            </select>
        </div>

        <!-- Status Type -->
        <div class="col-md-4 col-sm-6">
            <label for="statusType" class="form-label">Select Status</label>
            <select id="statusType" name="stype" class="form-select form-control" style="border-radius: 12px;">
                <option value="" selected>All Status</option>
                <option value="rent">Rent</option>
                <option value="sale">Sale</option>
            </select>
        </div>

        <!-- State -->
        <div class="col-md-4 col-sm-6">
            <label for="stateSelect" class="form-label">Select State</label>
            <select id="stateSelect" name="state_id" class="form-select form-control" style="border-radius: 12px;">
                <option value="" selected>All States</option>
                <?php 
                // This PHP code must be on the page where the form is.
                // It fetches states to populate the dropdown.
                $states_query = mysqli_query($con, "SELECT sid, sname FROM state ORDER BY sname ASC");
                while ($state_row = mysqli_fetch_assoc($states_query)) {
                    echo '<option value="' . htmlspecialchars($state_row['sid']) . '">' . htmlspecialchars($state_row['sname']) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- City -->
        <div class="col-md-4 col-sm-6">
            <label for="citySelect" class="form-label">Select City</label>
            <select id="citySelect" name="city_id" class="form-select form-control" style="border-radius: 12px;">
                <option value="" selected>All Cities</option>
                 <?php 
                // This PHP code must be on the page where the form is.
                // It fetches cities to populate the dropdown.
                $cities_query = mysqli_query($con, "SELECT cid, cname FROM city ORDER BY cname ASC");
                while ($city_row = mysqli_fetch_assoc($cities_query)) {
                    echo '<option value="' . htmlspecialchars($city_row['cid']) . '">' . htmlspecialchars($city_row['cname']) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- Minimum Price -->
        <div class="col-md-4 col-sm-6">
            <label for="minPrice" class="form-label">Min Price (NPR)</label>
            <input id="minPrice" type="number" name="min_price" class="form-control" placeholder="e.g., 500000" style="border-radius: 12px;" min="0">
        </div>

        <!-- Maximum Price -->
        <div class="col-md-4 col-sm-6">
            <label for="maxPrice" class="form-label">Max Price (NPR)</label>
            <input id="maxPrice" type="number" name="max_price" class="form-control" placeholder="e.g., 8000000" style="border-radius: 12px;" min="0">
        </div>

        <!-- Search Button -->
        <div class="col-12 text-center mt-4"> 
            <button type="submit" name="filter" class="btn" style="width: auto; padding: 10px 30px; font-weight: 600; background-color: rgb(235, 73, 52); color: white;">
                <i class="fas fa-search me-2"></i>Search Property
            </button>
        </div>
    </div>
</form>

                    </div>
                </div>
            </div>

            <div class="full-row py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-center mb-4" style="color: rgb(235, 73, 52);">Recent Property</h2>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <?php
                                // Assumed column names: pid, title, pimage, stype, price, location, size, bedroom, bathroom, kitchen, balcony, property.date, user.uname
                                // Adjust the SELECT statement if your column names are different.
                                $query_string = "SELECT p.pid, p.title, p.pimage, p.stype, p.price, p.location, p.size, p.bedroom, p.bathroom, p.kitchen, p.balcony, p.date AS property_date, u.uname 
                                                 FROM property p
                                                 JOIN user u ON p.uid = u.uid 
                                                 ORDER BY p.date DESC LIMIT 9";
                                $query = mysqli_query($con, $query_string);
                                if (mysqli_num_rows($query) > 0) {
                                    while ($row = mysqli_fetch_assoc($query)) { ?>
                                        <div class="col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
                                            <div class="featured-thumb">
                                                <div class="overlay-black">
                                                    <img src="admin/property/<?php echo htmlspecialchars($row['pimage']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                                    <div class="sale">For <?php echo htmlspecialchars($row['stype']); ?></div>
                                                    <div class="price">Rs. <?php echo number_format($row['price']); ?></div>
                                                </div>
                                                <div class="featured-thumb-data">
                                                    <div>
                                                        <h5><a href="propertydetail.php?pid=<?php echo htmlspecialchars($row['pid']); ?>"><?php echo htmlspecialchars($row['title']); ?></a></h5>
                                                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                                                    </div>
                                                    <div class="quantity">
                                                        <ul>
                                                            <li><span><?php echo htmlspecialchars($row['size']); ?></span> Sqft</li>
                                                            <li><span><?php echo htmlspecialchars($row['bedroom']); ?></span> Beds</li>
                                                            <li><span><?php echo htmlspecialchars($row['bathroom']); ?></span> Baths</li>
                                                            <li><span><?php echo htmlspecialchars($row['kitchen']); ?></span> Kitchen</li>
                                                            <li><span><?php echo htmlspecialchars($row['balcony']); ?></span> Balcony</li>
                                                        </ul>
                                                    </div>
                                                    <div class="p-4 d-inline-block w-100"> 
                                                        <div class="float-start">By : <?php echo htmlspecialchars($row['uname']); ?></div>
                                                        <div class="float-end"> <?php echo date('d-m-Y', strtotime($row['property_date'])); ?></div> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php }
                                } else {
                                    echo "<p class='text-center col-12'>No recent properties found.</p>";
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("include/footer.php"); ?>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/greensock.js"></script>
    <script src="js/bootstrap-slider.js"></script>
    <script src="js/jquery-ui.js"></script>
    
    <script>
        // Basic JavaScript for dependent City dropdown (optional improvement)
        document.addEventListener('DOMContentLoaded', function () {
            const stateSelect = document.getElementById('stateSelect');
            const citySelect = document.getElementById('citySelect');
            const cityOptions = Array.from(citySelect.options); // Store original city options

            if (stateSelect && citySelect) {
                stateSelect.addEventListener('change', function () {
                    const selectedStateId = this.value;
                    citySelect.innerHTML = ''; // Clear current city options

                    // Add the default "Select City" option
                    const defaultCityOption = document.createElement('option');
                    defaultCityOption.value = "";
                    defaultCityOption.textContent = "Select City";
                    citySelect.appendChild(defaultCityOption);
                    
                    cityOptions.forEach(option => {
                        if (option.value === "" || option.dataset.stateId === selectedStateId) {
                             // Clone the option to avoid issues if it's re-selected
                            citySelect.appendChild(option.cloneNode(true));
                        }
                    });
                    if(citySelect.options.length <=1 && selectedStateId !==""){
                        // If no cities for selected state (excluding "Select City" option)
                         const noCityOption = document.createElement('option');
                         noCityOption.value = "";
                         noCityOption.textContent = "No cities for this state";
                         noCityOption.disabled = true;
                         citySelect.appendChild(noCityOption);
                    }
                });

                 // Trigger change on page load if a state is pre-selected (e.g., from form resubmission)
                if (stateSelect.value) {
                    stateSelect.dispatchEvent(new Event('change'));
                } else {
                     // Initially hide all city options except the default one
                    cityOptions.forEach(option => {
                        if (option.value !== "") {
                            // Temporarily remove or hide options not matching the default "Select State"
                            // For this simple version, we'll just ensure the city dropdown is reset if no state is selected.
                        }
                    });
                     let hasSelectedCity = false;
                     for(let i=0; i< citySelect.options.length; i++){
                         if(citySelect.options[i].selected && citySelect.options[i].value !== ""){
                             hasSelectedCity = true;
                             break;
                         }
                     }
                     if(!hasSelectedCity){
                        citySelect.value = ""; // Reset city if no state selected and no city pre-selected
                     }
                }
            }
        });
    </script>

</body>
</html>
