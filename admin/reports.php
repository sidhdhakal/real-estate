<?php
// 1. Enable Full Error Reporting (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Start Session AT THE VERY TOP
session_start();

// 3. Include configuration and CHECK CONNECTION
include("config.php");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// 4. Check if admin is logged in
if (!isset($_SESSION['auser'])) {
    header("Location: index.php");
    exit();
}

// --- Helper function for executing queries and fetching associative array ---
function executeQuery($connection, $sql, $description = "Query") {
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        die("SQL Error in " . $description . ": " . mysqli_error($connection) . "<br>Query: " . $sql);
    }
    return $result;
}

function fetchAssoc($result, $description = "Fetch Assoc") {
    $data = mysqli_fetch_assoc($result);
    return $data;
}

function fetchAllAssoc($result, $description = "Fetch All Assoc") {
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $data;
}


// --- ORIGINAL SUMMARY COUNTS (from previous version) ---
$property_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM property", "Total Properties Count"));
$property_count_total = $property_count_total_row['total'] ?? 0;
// ... (other original summary counts like user_count, appointment_count, etc. - kept for brevity here, assume they are present)
$user_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype != 'admin'", "Total Users Count"));
$user_count_total = $user_count_total_row['total'] ?? 0;
$agent_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype = 'agent'", "Agent Users Count"));
$agent_count_total = $agent_count_total_row['total'] ?? 0;
$regular_user_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype = 'user'", "Regular Users Count"));
$regular_user_count_total = $regular_user_count_total_row['total'] ?? 0;
$appointment_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM appointment", "Appointments Count"));
$appointment_count_total = $appointment_count_total_row['total'] ?? 0;
$feedback_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM feedback", "Feedbacks Count"));
$feedback_count_total = $feedback_count_total_row['total'] ?? 0;
$featured_count_total_row = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM property WHERE isFeatured = 1", "Featured Properties Count"));
$featured_count_total = $featured_count_total_row['total'] ?? 0;

// --- ORIGINAL FINANCIAL SUMMARIES ---
$for_sale_active_data = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'sale' AND status = 'available'", "Active For Sale Summary"));
$for_sale_active_count = $for_sale_active_data['count'] ?? 0;
$for_sale_active_value = $for_sale_active_data['total_value'] ?? 0;
// ... (other financial summaries - kept for brevity, assume present)
$for_rent_active_data = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'rent' AND status = 'available'", "Active For Rent Summary"));
$for_rent_active_count = $for_rent_active_data['count'] ?? 0;
$for_rent_active_value = $for_rent_active_data['total_value'] ?? 0;
$sold_data = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'sale' AND status = 'inactive'", "Sold Properties Summary"));
$sold_count = $sold_data['count'] ?? 0;
$sold_value = $sold_data['total_value'] ?? 0;
$rented_out_data = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'rent' AND status = 'inactive'", "Rented Out Properties Summary"));
$rented_out_count = $rented_out_data['count'] ?? 0;
$rented_out_value = $rented_out_data['total_value'] ?? 0;


// --- ORIGINAL DETAILED LISTS ---
$recent_properties_query = executeQuery($con, "SELECT p.pid, p.title, p.type, p.stype, p.price, p.status, p.date, u.uname as lister_name FROM property p LEFT JOIN user u ON p.uid = u.uid ORDER BY p.date DESC LIMIT 5", "Recent Properties List");
// ... (other original detailed lists - kept for brevity, assume present)
$properties_for_sale_list_query = executeQuery($con, "SELECT p.pid, p.title, p.price, p.location, p.city, DATE_FORMAT(p.date, '%Y-%m-%d') as listing_date, u.uname as lister_name FROM property p LEFT JOIN user u ON p.uid = u.uid WHERE p.stype = 'sale' AND p.status = 'available' ORDER BY p.date DESC", "Available For Sale List");
$properties_for_rent_list_query = executeQuery($con, "SELECT p.pid, p.title, p.price, p.location, p.city, DATE_FORMAT(p.date, '%Y-%m-%d') as listing_date, u.uname as lister_name FROM property p LEFT JOIN user u ON p.uid = u.uid WHERE p.stype = 'rent' AND p.status = 'available' ORDER BY p.date DESC", "Available For Rent List");
$properties_sold_list_query = executeQuery($con, "SELECT p.pid, p.title, p.price, p.location, p.city, DATE_FORMAT(p.date, '%Y-%m-%d') as listing_date, u.uname as lister_name FROM property p LEFT JOIN user u ON p.uid = u.uid WHERE p.stype = 'sale' AND p.status = 'inactive' ORDER BY p.date DESC", "Sold Properties List");
$properties_rented_list_query = executeQuery($con, "SELECT p.pid, p.title, p.price, p.location, p.city, DATE_FORMAT(p.date, '%Y-%m-%d') as listing_date, u.uname as lister_name FROM property p LEFT JOIN user u ON p.uid = u.uid WHERE p.stype = 'rent' AND p.status = 'inactive' ORDER BY p.date DESC", "Rented Out Properties List");
$recent_appointments_query_orig = executeQuery($con, "SELECT app.appid, p.title as property_title, uc.uname as client_name, ua.uname as agent_name, app.date, app.time, app.status FROM appointment app LEFT JOIN property p ON app.pid = p.pid LEFT JOIN user uc ON app.uid = uc.uid LEFT JOIN user ua ON app.agent_uid = ua.uid ORDER BY app.date DESC, app.time DESC LIMIT 5", "Original Recent Appointments List");
$all_users_query = executeQuery($con, "SELECT uid, uname, uemail, uphone, utype FROM user WHERE utype != 'admin' ORDER BY utype, uname", "All Users List");


// --- NEW IN-DEPTH REPORT DATA FETCHING ---

// 1. Property Analysis
$properties_by_type = fetchAllAssoc(executeQuery($con, "SELECT type, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY type ORDER BY count DESC", "Properties by Type"));
$properties_by_bhk = fetchAllAssoc(executeQuery($con, "SELECT bhk, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY bhk ORDER BY count DESC", "Properties by BHK"));
$properties_by_city = fetchAllAssoc(executeQuery($con, "SELECT city, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY city ORDER BY count DESC LIMIT 10", "Properties by City (Top 10)"));
$properties_by_state = fetchAllAssoc(executeQuery($con, "SELECT state, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY state ORDER BY count DESC", "Properties by State"));

$top_expensive_sale = fetchAllAssoc(executeQuery($con, "SELECT pid, title, price, city FROM property WHERE stype = 'sale' AND status = 'available' ORDER BY price DESC LIMIT 5", "Top Expensive Sale Properties"));
$top_expensive_rent = fetchAllAssoc(executeQuery($con, "SELECT pid, title, price, city FROM property WHERE stype = 'rent' AND status = 'available' ORDER BY price DESC LIMIT 5", "Top Expensive Rent Properties"));

$property_lister_type_query = executeQuery($con, "SELECT u.utype as lister_type, COUNT(p.pid) as property_count 
                                                 FROM property p 
                                                 JOIN user u ON p.uid = u.uid 
                                                 GROUP BY u.utype", "Properties by Lister Type");
$property_lister_types = [];
while ($row = fetchAssoc($property_lister_type_query)) {
    $property_lister_types[$row['lister_type']] = $row['property_count'];
}

$digital_signature_counts_row = fetchAssoc(executeQuery($con, "SELECT 
    SUM(CASE WHEN digital_signature IS NOT NULL AND digital_signature != '' THEN 1 ELSE 0 END) as with_signature,
    SUM(CASE WHEN digital_signature IS NULL OR digital_signature = '' THEN 1 ELSE 0 END) as without_signature
    FROM property", "Digital Signature Counts"));
$properties_with_signature = $digital_signature_counts_row['with_signature'] ?? 0;
$properties_without_signature = $digital_signature_counts_row['without_signature'] ?? 0;

// 2. User Activity & Analysis
$top_agents_by_listings = fetchAllAssoc(executeQuery($con, "SELECT u.uname, COUNT(p.pid) as property_count 
                                                            FROM user u 
                                                            JOIN property p ON u.uid = p.uid 
                                                            WHERE u.utype = 'agent' 
                                                            GROUP BY u.uid 
                                                            ORDER BY property_count DESC LIMIT 5", "Top Agents by Listings"));
$top_users_by_listings = fetchAllAssoc(executeQuery($con, "SELECT u.uname, COUNT(p.pid) as property_count 
                                                           FROM user u 
                                                           JOIN property p ON u.uid = p.uid 
                                                           WHERE u.utype = 'user' 
                                                           GROUP BY u.uid 
                                                           ORDER BY property_count DESC LIMIT 5", "Top Users by Listings"));

// 3. Appointment Analysis
$appointments_by_status = fetchAllAssoc(executeQuery($con, "SELECT status, COUNT(*) as count FROM appointment GROUP BY status ORDER BY count DESC", "Appointments by Status"));
$top_properties_by_appointments = fetchAllAssoc(executeQuery($con, "SELECT p.title, COUNT(app.appid) as appointment_count 
                                                                    FROM appointment app 
                                                                    JOIN property p ON app.pid = p.pid 
                                                                    GROUP BY app.pid 
                                                                    ORDER BY appointment_count DESC LIMIT 5", "Top Properties by Appointments"));
$top_agents_by_appointments = fetchAllAssoc(executeQuery($con, "SELECT u.uname as agent_name, COUNT(app.appid) as appointment_count 
                                                                FROM appointment app 
                                                                JOIN user u ON app.agent_uid = u.uid 
                                                                WHERE u.utype = 'agent' 
                                                                GROUP BY app.agent_uid 
                                                                ORDER BY appointment_count DESC LIMIT 5", "Top Agents by Appointments"));
$top_clients_by_appointments = fetchAllAssoc(executeQuery($con, "SELECT u.uname as client_name, COUNT(app.appid) as appointment_count
                                                                 FROM appointment app
                                                                 JOIN user u ON app.uid = u.uid
                                                                 GROUP BY app.uid
                                                                 ORDER BY appointment_count DESC LIMIT 5", "Top Clients by Appointments"));


// 4. Feedback Analysis
$feedback_by_status_db = fetchAllAssoc(executeQuery($con, "SELECT status, COUNT(*) as count FROM feedback GROUP BY status", "Feedback by Status"));
$feedback_by_status = ['active' => 0, 'inactive' => 0];
foreach($feedback_by_status_db as $row) {
    if($row['status'] == 1) $feedback_by_status['active'] = $row['count'];
    else $feedback_by_status['inactive'] = $row['count'];
}
$recent_feedbacks_query = executeQuery($con, "SELECT f.fdescription, f.status as feedback_status, f.date as feedback_date, u.uname as user_name 
                                              FROM feedback f 
                                              JOIN user u ON f.uid = u.uid 
                                              ORDER BY f.date DESC LIMIT 5", "Recent Feedbacks");


$sidebar_width = "260px"; // Define sidebar width here. You MUST ADJUST THIS.
$top_navbar_height = "70px"; // Define top navbar height for padding. You MUST ADJUST THIS.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin In-Depth Reports</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #a8a432; /* Olive Green */
            --primary-text-color: #ffffff; /* White */
            --secondary-color: #d4d1a0; /* Lighter olive for accents if needed */
            --border-color: #cccccc;
            --card-header-bg: var(--primary-color);
            --card-header-text: var(--primary-text-color);
            --button-primary-bg: var(--primary-color);
            --button-primary-text: var(--primary-text-color);
            --table-header-bg: var(--primary-color);
            --table-header-text: var(--primary-text-color);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .content-wrapper {
            margin-left: <?php echo $sidebar_width; ?>; 
            padding-top: <?php echo $top_navbar_height; ?>; 
            padding-right: 20px;
            padding-left: 20px;
            padding-bottom: 40px; 
            background-color: #f4f6f9; 
            min-height: calc(100vh - <?php echo $top_navbar_height; ?>); 
        }
        .page-title-reports { color: #333; font-weight: 600; margin-bottom: 25px !important; font-size: 1.75rem; }
        h3.section-title { margin-top: 35px; margin-bottom: 20px; border-bottom: 2px solid var(--primary-color); padding-bottom: 10px; color: #3a3a3a; font-weight: 500; font-size: 1.4rem; }
        .card { border: 1px solid #e0e0e0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 0.375rem; margin-bottom: 1.5rem; }
        .card.border-primary { border-left: 4px solid var(--primary-color); }
        .card.border-success { border-left: 4px solid #28a745; } 
        .card.border-info { border-left: 4px solid #17a2b8; }    
        .card.border-warning { border-left: 4px solid #ffc107; } 
        .card.border-danger { border-left: 4px solid #dc3545; }  
        .card.border-secondary { border-left: 4px solid #6c757d; }
        .card-header-custom { background-color: var(--card-header-bg); color: var(--card-header-text); font-weight: 500; border-bottom: none; padding: 0.85rem 1.25rem; border-radius: calc(0.375rem - 1px) calc(0.375rem - 1px) 0 0;}
        .card-header-custom .card-title { font-size: 1.1rem; margin-bottom: 0; }
        .card-title-sm { font-size: 0.95rem; font-weight: 500; color: #666; margin-bottom: 0.5rem; } 
        .card-text-lg { font-size: 2rem; font-weight: 600; color: var(--primary-color); }
        .nav-pills .nav-link { color: var(--primary-color); border: 1px solid var(--secondary-color); margin: 0 4px; border-radius: 0.3rem; padding: 0.6rem 1.1rem; font-weight:500; }
        .nav-pills .nav-link.active, .nav-pills .show > .nav-link { color: var(--primary-text-color); background-color: var(--primary-color); border-color: var(--primary-color); }
        .nav-pills .nav-link:hover { background-color: var(--secondary-color); color: #495057; }
        .table-responsive { margin-top: 0; /* Handled by card-body padding */ }
        .table { margin-bottom: 0; /* Remove default Bootstrap margin */ }
        .table thead.thead-custom th { background-color: var(--table-header-bg); color: var(--table-header-text); border-color: var(--secondary-color); font-weight: 500; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;}
        .table-striped tbody tr:nth-of-type(odd) { background-color: #fdfdfd; }
        .table td, .table th { vertical-align: middle; padding: 0.85rem; font-size: 0.9rem; }
        .btn-download-report { background-color: var(--button-primary-bg); color: var(--button-primary-text); border-color: var(--button-primary-bg); padding: 0.75rem 1.5rem; font-size: 1.05rem; font-weight: 500;}
        .btn-download-report:hover { background-color: #8f8b2e; color: var(--button-primary-text); border-color: #8f8b2e; }
        .list-group-item { font-size: 0.9rem; }
        .stat-value { font-weight: 600; color: var(--primary-color); }
        @media (max-width: 991.98px) { .content-wrapper { margin-left: 0; } }
        .sub-section-title { font-size: 1.1rem; font-weight: 500; color: #555; margin-top: 1.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px dashed #ddd; }
    </style>
</head>
<body>

<?php include("header.php"); ?>
 
<div class="content-wrapper">
    <div class="container-fluid">
        <h2 class="mb-4 text-center page-title-reports">Admin In-Depth Reports Dashboard</h2>

        <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="pills-summary-tab" data-toggle="pill" href="#pills-summary" role="tab">Overall Summary</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-properties-tab" data-toggle="pill" href="#pills-properties" role="tab">Property Analysis</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-users-tab" data-toggle="pill" href="#pills-users" role="tab">User & Agent Activity</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-appointments-tab" data-toggle="pill" href="#pills-appointments" role="tab">Appointment Insights</a></li>
            <li class="nav-item"><a class="nav-link" id="pills-engagement-tab" data-toggle="pill" href="#pills-engagement" role="tab">Engagement Metrics</a></li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <!-- Overall Summary Tab -->
            <div class="tab-pane fade show active" id="pills-summary" role="tabpanel">
                <h3 class="section-title">Key Performance Indicators</h3>
                <div class="row">
                    <div class="col-xl-3 col-lg-6 mb-4">
                        <div class="card border-primary h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title card-title-sm">Total Properties</h5>
                                <p class="card-text card-text-lg"><?= htmlspecialchars($property_count_total) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 mb-4">
                        <div class="card border-success h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title card-title-sm">Featured Properties</h5>
                                <p class="card-text card-text-lg"><?= htmlspecialchars($featured_count_total) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 mb-4">
                        <div class="card border-info h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title card-title-sm">Total Users</h5>
                                <p class="card-text card-text-lg"><?= htmlspecialchars($user_count_total) ?></p>
                                <small>Agents: <?= htmlspecialchars($agent_count_total) ?> | Clients: <?= htmlspecialchars($regular_user_count_total) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 mb-4">
                        <div class="card border-warning h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title card-title-sm">Total Appointments</h5>
                                <p class="card-text card-text-lg"><?= htmlspecialchars($appointment_count_total) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 class="section-title">Financial Snapshot</h3>
                 <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title card-title-sm">Properties for Sale (Active)</h5>
                                <p class="card-text card-text-lg mb-1"><?= htmlspecialchars($for_sale_active_count) ?></p>
                                <small>Total Value: Rs. <?= htmlspecialchars(number_format($for_sale_active_value, 0)) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="card-title card-title-sm">Properties for Rent (Active)</h5>
                                <p class="card-text card-text-lg mb-1"><?= htmlspecialchars($for_rent_active_count) ?></p>
                                <small>Total Rental Value (per period): Rs. <?= htmlspecialchars(number_format($for_rent_active_value, 0)) ?></small>
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h5 class="card-title card-title-sm">Properties Sold</h5>
                                <p class="card-text card-text-lg mb-1"><?= htmlspecialchars($sold_count) ?></p>
                                <small>Total Sale Value: Rs. <?= htmlspecialchars(number_format($sold_value, 0)) ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-info h-100">
                            <div class="card-body">
                                <h5 class="card-title card-title-sm">Properties Rented Out</h5>
                                <p class="card-text card-text-lg mb-1"><?= htmlspecialchars($rented_out_count) ?></p>
                                <small>Total Value of Rents (per period): Rs. <?= htmlspecialchars(number_format($rented_out_value, 0)) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /#pills-summary -->

            <!-- Property Analysis Tab -->
            <div class="tab-pane fade" id="pills-properties" role="tabpanel">
                <h3 class="section-title">Property Portfolio Analysis</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Properties by Type</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach($properties_by_type as $ptype): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars(ucfirst($ptype['type'])) ?>
                                            <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $ptype['count'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                         <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Properties by BHK</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach($properties_by_bhk as $pbhk): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars($pbhk['bhk']) ?>
                                            <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $pbhk['count'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Most Expensive (For Sale)</h4></div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="thead-custom"><tr><th>Title</th><th>City</th><th>Price (Rs.)</th></tr></thead>
                                        <tbody>
                                        <?php foreach($top_expensive_sale as $prop): ?>
                                            <tr><td><?= htmlspecialchars($prop['title']) ?></td><td><?= htmlspecialchars($prop['city']) ?></td><td class="text-right"><?= number_format($prop['price'],0) ?></td></tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Most Expensive (For Rent)</h4></div>
                            <div class="card-body p-0">
                                 <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="thead-custom"><tr><th>Title</th><th>City</th><th>Price (Rs.)</th></tr></thead>
                                        <tbody>
                                        <?php foreach($top_expensive_rent as $prop): ?>
                                            <tr><td><?= htmlspecialchars($prop['title']) ?></td><td><?= htmlspecialchars($prop['city']) ?></td><td class="text-right"><?= number_format($prop['price'],0) ?></td></tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Properties by Lister Type</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Agents
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $property_lister_types['agent'] ?? 0 ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Users (Non-Agents)
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $property_lister_types['user'] ?? 0 ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Properties with Digital Signatures</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        With Digital Signature
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $properties_with_signature ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Without Digital Signature
                                        <span class="badge badge-pill" style="background-color:var(--secondary-color); color:#333;"><?= $properties_without_signature ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="sub-section-title">Properties by City (Top 10 by Count)</h4>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="thead-custom"><tr><th>City</th><th>Property Count</th><th>Avg. Price (Rs.)</th></tr></thead>
                                <tbody>
                                <?php foreach($properties_by_city as $pcity): ?>
                                    <tr><td><?= htmlspecialchars(ucfirst($pcity['city'])) ?></td><td><?= $pcity['count'] ?></td><td class="text-right"><?= number_format($pcity['avg_price'] ?? 0, 0) ?></td></tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <h4 class="sub-section-title">Properties by State</h4>
                 <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="thead-custom"><tr><th>State</th><th>Property Count</th><th>Avg. Price (Rs.)</th></tr></thead>
                                <tbody>
                                <?php foreach($properties_by_state as $pstate): ?>
                                    <tr><td><?= htmlspecialchars(ucfirst($pstate['state'])) ?></td><td><?= $pstate['count'] ?></td><td class="text-right"><?= number_format($pstate['avg_price'] ?? 0, 0) ?></td></tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detailed Lists (For Sale, For Rent, Sold, Rented Out) can go here, or in separate sub-tabs if too long -->
                <!-- For brevity, I'm not repeating the full tables from previous version here but you would integrate them -->

            </div> <!-- /#pills-properties -->

            <!-- User & Agent Activity Tab -->
            <div class="tab-pane fade" id="pills-users" role="tabpanel">
                <h3 class="section-title">User & Agent Performance</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Agents (by Listings)</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($top_agents_by_listings as $agent): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($agent['uname']) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $agent['property_count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if(empty($top_agents_by_listings)): ?> <li class="list-group-item text-center">No agent listings found.</li> <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Users (by Listings)</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($top_users_by_listings as $user): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($user['uname']) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $user['property_count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if(empty($top_users_by_listings)): ?> <li class="list-group-item text-center">No user listings found.</li> <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="sub-section-title">All Users List</h4>
                 <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-custom">
                                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Type</th></tr>
                                </thead>
                                <tbody>
                                    <?php mysqli_data_seek($all_users_query, 0); /* Reset pointer */ ?>
                                    <?php if ($all_users_query && mysqli_num_rows($all_users_query) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($all_users_query)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['uid']) ?></td>
                                                <td><?= htmlspecialchars($row['uname']) ?></td>
                                                <td><?= htmlspecialchars($row['uemail']) ?></td>
                                                <td><?= htmlspecialchars($row['uphone']) ?></td>
                                                <td><span class="badge badge-<?= $row['utype'] == 'agent' ? 'success' : 'info' ?> p-2"><?= htmlspecialchars(ucfirst($row['utype'])) ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4">No users found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> <!-- /#pills-users -->

            <!-- Appointment Insights Tab -->
            <div class="tab-pane fade" id="pills-appointments" role="tabpanel">
                <h3 class="section-title">Appointment Analysis</h3>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Appointments by Status</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($appointments_by_status as $app_status): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars(ucfirst($app_status['status'])) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $app_status['count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Agents (by Appointments)</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($top_agents_by_appointments as $agent_app): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($agent_app['agent_name']) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $agent_app['appointment_count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                 <?php if(empty($top_agents_by_appointments)): ?> <li class="list-group-item text-center">No agent appointments.</li> <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Properties (by Appointments)</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($top_properties_by_appointments as $prop_app): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($prop_app['title']) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $prop_app['appointment_count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if(empty($top_properties_by_appointments)): ?> <li class="list-group-item text-center">No property appointments.</li> <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Top 5 Clients (by Appointments)</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                <?php foreach($top_clients_by_appointments as $client_app): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($client_app['client_name']) ?>
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $client_app['appointment_count'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                                 <?php if(empty($top_clients_by_appointments)): ?> <li class="list-group-item text-center">No client appointments.</li> <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="sub-section-title">Recent Appointments (Last 5)</h4>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-custom">
                                    <tr><th>Property</th><th>Client</th><th>Agent</th><th>Date & Time</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php mysqli_data_seek($recent_appointments_query_orig, 0); /* Reset pointer */ ?>
                                    <?php if ($recent_appointments_query_orig && mysqli_num_rows($recent_appointments_query_orig) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($recent_appointments_query_orig)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['property_title'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($row['client_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($row['agent_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars(date("M d, Y h:i A", strtotime($row['date'].' '.$row['time']))) ?></td>
                                                <td>
                                                    <?php
                                                    $status_badge = 'secondary'; 
                                                    $status_text = htmlspecialchars($row['status']);
                                                    if (strtolower($row['status']) == 'pending') $status_badge = 'warning';
                                                    elseif (strtolower($row['status']) == 'confirmed' || strtolower($row['status']) == 'completed') $status_badge = 'success';
                                                    elseif (strtolower($row['status']) == 'cancelled') $status_badge = 'danger';
                                                    ?>
                                                    <span class="badge badge-<?= $status_badge ?> p-2"><?= $status_text ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4">No recent appointments.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- /#pills-appointments -->

            <!-- Engagement Metrics Tab (Feedbacks, Contacts etc.) -->
            <div class="tab-pane fade" id="pills-engagement" role="tabpanel">
                <h3 class="section-title">Platform Engagement</h3>
                 <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header card-header-custom"><h4 class="card-title">Feedback Summary</h4></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Active / Displayed Feedbacks
                                        <span class="badge badge-pill" style="background-color:var(--primary-color); color:white;"><?= $feedback_by_status['active'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Inactive Feedbacks
                                        <span class="badge badge-pill" style="background-color:var(--secondary-color); color:#333;"><?= $feedback_by_status['inactive'] ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Feedbacks
                                        <span class="badge badge-pill" style="background-color:#6c757d; color:white;"><?= htmlspecialchars($feedback_count_total) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 mb-4">
                        <?php 
                        $contact_query_count = fetchAssoc(executeQuery($con, "SELECT COUNT(*) as total FROM contact", "Total Contact Queries"));
                        $total_contact_queries = $contact_query_count['total'] ?? 0;
                        ?>
                        <div class="card border-info h-100">
                             <div class="card-body text-center">
                                <h5 class="card-title card-title-sm">Total Contact Form Submissions</h5>
                                <p class="card-text card-text-lg"><?= htmlspecialchars($total_contact_queries) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="sub-section-title">Recent Feedbacks (Last 5)</h4>
                <div class="card">
                     <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-custom">
                                    <tr><th>User</th><th>Feedback Description</th><th>Date</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                <?php if ($recent_feedbacks_query && mysqli_num_rows($recent_feedbacks_query) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($recent_feedbacks_query)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                                        <td><?= nl2br(htmlspecialchars(substr($row['fdescription'], 0, 100))) . (strlen($row['fdescription']) > 100 ? '...' : '') ?></td>
                                        <td><?= htmlspecialchars(date("M d, Y", strtotime($row['feedback_date']))) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $row['feedback_status'] == 1 ? 'success' : 'secondary' ?> p-2">
                                                <?= $row['feedback_status'] == 1 ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4">No recent feedbacks.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- /#pills-engagement -->
        </div> <!-- /.tab-content -->

        <div class="text-center mt-5 mb-4">
            <a href="generate_report.php" class="btn btn-download-report btn-lg">Download PDF Report</a>
            <p class="mt-2"><small>(Note: PDF report contains a subset of this data)</small></p>
        </div>
    </div> 
</div> 

<?php // include("footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $('#pills-tab a').on('click', function (e) {
          e.preventDefault();
          $(this).tab('show');
        });
        // Optional: Activate tab based on URL hash
        var hash = window.location.hash;
        if (hash) {
            $('#pills-tab a[href="' + hash + '"]').tab('show');
        }
    });
</script>
</body>
</html>