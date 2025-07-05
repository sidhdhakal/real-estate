<?php
session_start();
include("config.php");

// Enable error reporting for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Property Results - Real Estate</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        /* Your existing CSS styles go here... */
        body { background-color: #f8f9fa; }
        .page-header { background-image: url('img/carousel-1.jpg'); background-size: cover; background-position: center; padding: 100px 0; color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.6); }
        .page-header h1 { font-size: 3rem; font-weight: 700; }
        .property-item { background: #fff; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; height: 100%; display: flex; flex-direction: column; }
        .property-item:hover { transform: translateY(-5px); box-shadow: 0 1rem 1.5rem rgba(0,0,0,0.15); }
        .property-item .position-relative { height: 220px; }
        .property-item img { width: 100%; height: 100%; object-fit: cover; }
        .property-item .bg-primary { background-color: #eb4934 !important; }
        .property-item .bg-primary.rounded { border-radius: 0.25rem; font-size: 0.85rem; padding: 0.25rem 0.75rem; }
        .property-item .rounded-top.text-primary { background: #fff !important; color: #eb4934 !important; font-size: 0.85rem; padding: 0.25rem 0.75rem; }
        .property-item h5.price { font-size: 1.5rem; font-weight: 700; color: #eb4934 !important; }
        .property-item .text-dark:hover { color: #eb4934 !important; }
        .property-item .fa-map-marker-alt { color: #28a745; margin-right: 0.5rem; }
        .property-item .border-top { margin-top: auto; } /* Push footer to bottom */
        .property-item .border-top small { font-size: 0.85rem; color: #555; }
        .property-item .border-top i { color: #eb4934; margin-right: 0.5rem; }
    </style>
</head>
<body>

<?php include("include/header.php"); ?>

<div class="container-fluid page-header text-center mb-5">
    <div class="container">
        <h1 class="display-3 mb-3">
           <h2 <?php echo isset($_POST['filter']) ? 'style="color: rgb(235, 73, 52);"' : ''; ?>>
    <?php echo isset($_POST['filter']) ? 'Search Results' : 'All Properties'; ?>
</h2>

        </h1>
    </div>
</div>

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <?php
            // ---- START OF FULLY CORRECTED LOGIC ----

            // Base query. We only need to join with `user` to get the username.
            // We DO NOT join with state or city because the names are in the property table.
            $query_select = "SELECT property.*, user.uname FROM property JOIN user ON user.uid = property.uid";
            $query_where = " WHERE 1=1 AND property.status = 'available'"; // Start with true conditions
            $query_order = " ORDER BY property.date DESC";

            $params = [];
            $types = "";

            if (isset($_POST['filter'])) {
                // Filter by Property Type
                if (!empty($_POST['type'])) {
                    $query_where .= " AND property.type = ?";
                    $types .= "s";
                    $params[] = $_POST['type'];
                }
                // Filter by Status Type (Rent/Sale)
                if (!empty($_POST['stype'])) {
                    $query_where .= " AND property.stype = ?";
                    $types .= "s";
                    $params[] = $_POST['stype'];
                }
                // Filter by Price
                if (!empty($_POST['min_price']) && is_numeric($_POST['min_price'])) {
                    $query_where .= " AND property.price >= ?";
                    $types .= "d";
                    $params[] = (float)$_POST['min_price'];
                }
                if (!empty($_POST['max_price']) && is_numeric($_POST['max_price'])) {
                    $query_where .= " AND property.price <= ?";
                    $types .= "d";
                    $params[] = (float)$_POST['max_price'];
                }

                // ** THE MAIN FIX IS HERE **
                // Get State NAME from state_id
                if (!empty($_POST['state_id'])) {
                    $state_id = $_POST['state_id'];
                    $state_stmt = mysqli_prepare($con, "SELECT sname FROM state WHERE sid = ?");
                    mysqli_stmt_bind_param($state_stmt, 'i', $state_id);
                    mysqli_stmt_execute($state_stmt);
                    $state_result = mysqli_stmt_get_result($state_stmt);
                    if ($state_row = mysqli_fetch_assoc($state_result)) {
                        $state_name = $state_row['sname'];
                        $query_where .= " AND property.state = ?";
                        $types .= "s";
                        $params[] = $state_name;
                    }
                }

                // Get City NAME from city_id
                if (!empty($_POST['city_id'])) {
                    $city_id = $_POST['city_id'];
                    $city_stmt = mysqli_prepare($con, "SELECT cname FROM city WHERE cid = ?");
                    mysqli_stmt_bind_param($city_stmt, 'i', $city_id);
                    mysqli_stmt_execute($city_stmt);
                    $city_result = mysqli_stmt_get_result($city_stmt);
                    if ($city_row = mysqli_fetch_assoc($city_result)) {
                        $city_name = $city_row['cname'];
                        $query_where .= " AND property.city = ?";
                        $types .= "s";
                        $params[] = $city_name;
                    }
                }

                $query = $query_select . $query_where . $query_order;
                
                $stmt = mysqli_prepare($con, $query);
                if ($stmt) {
                    if (!empty($params)) {
                        mysqli_stmt_bind_param($stmt, $types, ...$params);
                    }
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                } else {
                    $result = false;
                    echo "<p class='text-center text-danger'>Error preparing the query: " . mysqli_error($con) . "</p>";
                }

            } else {
                // If form not submitted, just show all available properties
                $query = $query_select . " WHERE property.status = 'available'" . $query_order;
                $result = mysqli_query($con, $query);
            }

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                        <div class="property-item">
                            <div class="position-relative overflow-hidden">
                                <a href="propertydetail.php?pid=<?php echo $row['pid']; ?>">
                                    <img src="admin/property/<?php echo htmlspecialchars($row['pimage']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                </a>
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-3">
                                    For <?php echo htmlspecialchars($row['stype']); ?>
                                </div>
                                <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 m-3">
                                    <?php echo htmlspecialchars($row['type']); ?>
                                </div>
                            </div>
                            <div class="p-4 pb-0">
                                <h5 class="price mb-2">Rs. <?php echo number_format($row['price']); ?></h5>
                                <a href="propertydetail.php?pid=<?php echo $row['pid']; ?>" class="d-block h5 mb-2 text-dark">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                                <p><i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($row['location']); ?>, 
                                    <?php echo htmlspecialchars($row['city']); ?>, 
                                    <?php echo htmlspecialchars($row['state']); ?>
                                </p>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($row['size']); ?> sq ft
                                </small>
                                <small class="flex-fill text-center border-end py-2">
                                    <i class="fas fa-bed"></i> <?php echo htmlspecialchars($row['bedroom']); ?> Bed
                                </small>
                                <small class="flex-fill text-center py-2">
                                    <i class="fas fa-bath"></i> <?php echo htmlspecialchars($row['bathroom']); ?> Bath
                                </small>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='col-12'><p class='text-center text-danger h4 p-5 bg-light rounded'>No properties found matching your criteria.</p></div>";
            }
            ?>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>