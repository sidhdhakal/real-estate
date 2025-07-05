<?php
// Enable error reporting for debugging PDF generation
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ensure TCPDF is loaded correctly
if (file_exists(__DIR__ . '/tcpdf/tcpdf/tcpdf.php')) { // Check relative to current script dir
    require_once(__DIR__ . '/tcpdf/tcpdf/tcpdf.php');
} elseif (file_exists(__DIR__ . '/../tcpdf/tcpdf/tcpdf.php')) { // Common if script is in a subfolder like 'admin'
    require_once(__DIR__ . '/../tcpdf/tcpdf/tcpdf.php');
} elseif (file_exists('tcpdf/tcpdf.php')) { // A common structure
     require_once('tcpdf/tcpdf.php');
} else {
    // Fallback for common installations if the above paths are not found
    @include_once('tcpdf.php');
    if (!class_exists('TCPDF')) {
         die("TCPDF library not found. Please check the path or ensure it's in your include_path. Tried: various common paths and tcpdf.php directly.");
    }
}

// Ensure config.php is included and establishes $con
include("config.php");

// Check database connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8mb4");


// --- Helper functions ---
function executeQuery($connection, $sql, $description = "Query") {
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        $db_error = mysqli_error($connection);
        error_log("SQL Error in " . htmlspecialchars($description) . ": " . $db_error . " | Query: " . $sql);
        die("A database error occurred while generating the report for '" . htmlspecialchars($description) . "'. DB Error: " . htmlspecialchars($db_error));
    }
    return $result;
}
function fetchAssoc($result) {
    if ($result instanceof mysqli_result) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}
function fetchAllAssoc($result) {
    if ($result instanceof mysqli_result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return [];
}

// --- DATA FETCHING ---
// (Same data fetching logic as the previous full code response)
// Overall Summary Counts
$property_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM property", "Total Properties Count");
$property_count_row = fetchAssoc($property_count_res);
$property_count = $property_count_row['total'] ?? 0;
mysqli_free_result($property_count_res);

$user_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype != 'admin'", "Total Users Count");
$user_count_row = fetchAssoc($user_count_res);
$user_count = $user_count_row['total'] ?? 0;
mysqli_free_result($user_count_res);

$agent_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype = 'agent'", "Agent Count");
$agent_count_row = fetchAssoc($agent_count_res);
$agent_count = $agent_count_row['total'] ?? 0;
mysqli_free_result($agent_count_res);

$regular_user_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM user WHERE utype = 'user'", "Regular User Count");
$regular_user_count_row = fetchAssoc($regular_user_count_res);
$regular_user_count = $regular_user_count_row['total'] ?? 0;
mysqli_free_result($regular_user_count_res);

$appointment_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM appointment", "Total Appointments Count");
$appointment_count_row = fetchAssoc($appointment_count_res);
$appointment_count = $appointment_count_row['total'] ?? 0;
mysqli_free_result($appointment_count_res);

$feedback_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM feedback", "Total Feedbacks Count");
$feedback_count_row = fetchAssoc($feedback_count_res);
$feedback_count = $feedback_count_row['total'] ?? 0;
mysqli_free_result($feedback_count_res);

$featured_count_res = executeQuery($con, "SELECT COUNT(*) as total FROM property WHERE isFeatured = 1", "Featured Properties Count");
$featured_count_row = fetchAssoc($featured_count_res);
$featured_count = $featured_count_row['total'] ?? 0;
mysqli_free_result($featured_count_res);

$contact_queries_res = executeQuery($con, "SELECT COUNT(*) as total FROM contact", "Contact Queries Count");
$contact_queries_row = fetchAssoc($contact_queries_res);
$contact_queries_count = $contact_queries_row['total'] ?? 0;
mysqli_free_result($contact_queries_res);


// Financial Summaries
$for_sale_active_res = executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'sale' AND status = 'available'", "Active Sale Value");
$for_sale_active_data = fetchAssoc($for_sale_active_res);
$for_sale_active_count = $for_sale_active_data['count'] ?? 0;
$for_sale_active_value = $for_sale_active_data['total_value'] ?? 0;
mysqli_free_result($for_sale_active_res);

$for_rent_active_res = executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'rent' AND status = 'available'", "Active Rent Value");
$for_rent_active_data = fetchAssoc($for_rent_active_res);
$for_rent_active_count = $for_rent_active_data['count'] ?? 0;
$for_rent_active_value = $for_rent_active_data['total_value'] ?? 0;
mysqli_free_result($for_rent_active_res);

$sold_data_res = executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'sale' AND status = 'inactive'", "Sold Properties Value");
$sold_data = fetchAssoc($sold_data_res);
$sold_count = $sold_data['count'] ?? 0;
$sold_value = $sold_data['total_value'] ?? 0;
mysqli_free_result($sold_data_res);

$rented_out_data_res = executeQuery($con, "SELECT COUNT(*) as count, SUM(price) as total_value FROM property WHERE stype = 'rent' AND status = 'inactive'", "Rented Properties Value");
$rented_out_data = fetchAssoc($rented_out_data_res);
$rented_out_count = $rented_out_data['count'] ?? 0;
$rented_out_value = $rented_out_data['total_value'] ?? 0;
mysqli_free_result($rented_out_data_res);

// Property Analysis
$properties_by_type_res = executeQuery($con, "SELECT type, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY type ORDER BY count DESC", "Properties by Type PDF");
$properties_by_type = fetchAllAssoc($properties_by_type_res);
mysqli_free_result($properties_by_type_res);

$properties_by_bhk_res = executeQuery($con, "SELECT bhk, COUNT(*) as count, AVG(price) as avg_price FROM property GROUP BY bhk ORDER BY count DESC", "Properties by BHK PDF");
$properties_by_bhk = fetchAllAssoc($properties_by_bhk_res);
mysqli_free_result($properties_by_bhk_res);

$properties_by_city_pdf_res = executeQuery($con, "SELECT city, COUNT(*) as count, AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price FROM property GROUP BY city ORDER BY count DESC LIMIT 10", "Properties by City PDF");
$properties_by_city_pdf = fetchAllAssoc($properties_by_city_pdf_res);
mysqli_free_result($properties_by_city_pdf_res);

$properties_by_state_pdf_res = executeQuery($con, "SELECT state, COUNT(*) as count, AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price FROM property GROUP BY state ORDER BY count DESC", "Properties by State PDF");
$properties_by_state_pdf = fetchAllAssoc($properties_by_state_pdf_res);
mysqli_free_result($properties_by_state_pdf_res);

$top_expensive_sale_pdf_res = executeQuery($con, "SELECT pid, title, price, city, `size`, bhk FROM property WHERE stype = 'sale' AND status = 'available' ORDER BY price DESC LIMIT 5", "Top Expensive Sale PDF");
$top_expensive_sale_pdf = fetchAllAssoc($top_expensive_sale_pdf_res);
mysqli_free_result($top_expensive_sale_pdf_res);

$top_expensive_rent_pdf_res = executeQuery($con, "SELECT pid, title, price, city, `size`, bhk FROM property WHERE stype = 'rent' AND status = 'available' ORDER BY price DESC LIMIT 5", "Top Expensive Rent PDF");
$top_expensive_rent_pdf = fetchAllAssoc($top_expensive_rent_pdf_res);
mysqli_free_result($top_expensive_rent_pdf_res);

$property_lister_type_query_res = executeQuery($con, "SELECT u.utype as lister_type, COUNT(p.pid) as property_count FROM property p JOIN user u ON p.uid = u.uid GROUP BY u.utype", "Property Lister Types PDF");
$property_lister_types_pdf = [];
if ($property_lister_type_query_res && mysqli_num_rows($property_lister_type_query_res) > 0) {
    while ($row = fetchAssoc($property_lister_type_query_res)) {
        if (isset($row['lister_type']) && isset($row['property_count'])) {
            $property_lister_types_pdf[$row['lister_type']] = $row['property_count'];
        }
    }
}
mysqli_free_result($property_lister_type_query_res);

$digital_signature_counts_res = executeQuery($con, "SELECT SUM(CASE WHEN digital_signature IS NOT NULL AND digital_signature != '' THEN 1 ELSE 0 END) as with_signature, SUM(CASE WHEN digital_signature IS NULL OR digital_signature = '' THEN 1 ELSE 0 END) as without_signature FROM property", "Digital Signature Counts PDF");
$digital_signature_counts_row = fetchAssoc($digital_signature_counts_res);
$properties_with_signature_pdf = $digital_signature_counts_row['with_signature'] ?? 0;
$properties_without_signature_pdf = $digital_signature_counts_row['without_signature'] ?? 0;
mysqli_free_result($digital_signature_counts_res);

// User Activity & Analysis
$top_agents_by_listings_pdf_res = executeQuery($con, "SELECT u.uname, COUNT(p.pid) as property_count, u.uemail, u.uphone FROM user u JOIN property p ON u.uid = p.uid WHERE u.utype = 'agent' GROUP BY u.uid ORDER BY property_count DESC LIMIT 5", "Top Agents by Listings PDF");
$top_agents_by_listings_pdf = fetchAllAssoc($top_agents_by_listings_pdf_res);
mysqli_free_result($top_agents_by_listings_pdf_res);

$top_users_by_listings_pdf_res = executeQuery($con, "SELECT u.uname, COUNT(p.pid) as property_count, u.uemail, u.uphone FROM user u JOIN property p ON u.uid = p.uid WHERE u.utype = 'user' GROUP BY u.uid ORDER BY property_count DESC LIMIT 5", "Top Users by Listings PDF");
$top_users_by_listings_pdf = fetchAllAssoc($top_users_by_listings_pdf_res);
mysqli_free_result($top_users_by_listings_pdf_res);

$all_users_pdf_res = executeQuery($con, "SELECT uid, uname, uemail, uphone, utype FROM user WHERE utype != 'admin' ORDER BY utype, uname LIMIT 20", "All Users List PDF");

// Appointment Analysis
$appointments_by_status_pdf_res = executeQuery($con, "SELECT status, COUNT(*) as count FROM appointment GROUP BY status ORDER BY count DESC", "Appointments by Status PDF");
$appointments_by_status_pdf = fetchAllAssoc($appointments_by_status_pdf_res);
mysqli_free_result($appointments_by_status_pdf_res);

$top_properties_by_appointments_pdf_res = executeQuery($con, "SELECT p.title, p.pid, COUNT(app.appid) as appointment_count FROM appointment app JOIN property p ON app.pid = p.pid GROUP BY app.pid ORDER BY appointment_count DESC LIMIT 5", "Top Properties by Appointments PDF");
$top_properties_by_appointments_pdf = fetchAllAssoc($top_properties_by_appointments_pdf_res);
mysqli_free_result($top_properties_by_appointments_pdf_res);

$top_agents_by_appointments_pdf_res = executeQuery($con, "SELECT u.uname as agent_name, u.uid, COUNT(app.appid) as appointment_count FROM appointment app JOIN user u ON app.agent_uid = u.uid WHERE u.utype = 'agent' GROUP BY app.agent_uid ORDER BY appointment_count DESC LIMIT 5", "Top Agents by Appointments PDF");
$top_agents_by_appointments_pdf = fetchAllAssoc($top_agents_by_appointments_pdf_res);
mysqli_free_result($top_agents_by_appointments_pdf_res);

$recent_appointments_pdf_res = executeQuery($con, "SELECT app.appid, u_client.uname as client_name, p.title as property_title, app.date as appointment_date, app.time as appointment_time, app.status FROM appointment app LEFT JOIN user u_client ON app.uid = u_client.uid LEFT JOIN property p ON app.pid = p.pid ORDER BY app.appid DESC LIMIT 10", "Recent Appointments PDF");

// Engagement Metrics
$feedback_by_status_db_pdf_res = executeQuery($con, "SELECT status, COUNT(*) as count FROM feedback GROUP BY status", "Feedback by Status DB PDF");
$feedback_by_status_db_pdf = fetchAllAssoc($feedback_by_status_db_pdf_res);
mysqli_free_result($feedback_by_status_db_pdf_res);

$feedback_by_status_pdf = ['active' => 0, 'inactive' => 0];
foreach($feedback_by_status_db_pdf as $row) {
    if(isset($row['status']) && isset($row['count'])){
        if($row['status'] == 1) $feedback_by_status_pdf['active'] = $row['count'];
        else $feedback_by_status_pdf['inactive'] = $row['count'];
    }
}
$recent_feedbacks_pdf_res = executeQuery($con, "SELECT f.fdescription, f.status as feedback_status, f.date as feedback_date, u.uname as user_name FROM feedback f JOIN user u ON f.uid = u.uid ORDER BY f.date DESC LIMIT 5", "Recent Feedbacks PDF");


// --- PDF CLASS AND SETUP ---
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 15, 'Real Estate System Report', 0, true, 'C', 0, '', 0, false, 'M', 'M');
        
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, true, 'R', 0, '', 0, false, 'T', 'M');
        
        // CORRECTED LINE: Use getMargins()['R']
        $margins = $this->getMargins();
        $rightMargin = $margins['R'];
        $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $rightMargin, $this->GetY());
        $this->Ln(5); 
    }

    public function Footer() {
        $this->SetY(-15); 
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('LM Homes Admin Panel'); 
$pdf->SetAuthor('Admin'); 
$pdf->SetTitle('Real Estate System In-Depth Report');
$pdf->SetSubject('Comprehensive platform activity and analysis');
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 35, 15); 
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25); 
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage();

// --- PDF CONTENT GENERATION ---
$th_style = 'style="background-color: #e9e9e9; font-weight: bold; text-align:center;"';
$td_style_right = 'style="text-align:right;"';
$table_style = 'border="1" cellpadding="4" cellspacing="0" style="border-collapse: collapse; width: 100%; font-size: 9pt;"'; 
$h4_style = 'style="font-size:10pt; font-weight:bold; margin-bottom:2px;"';

function addSectionTitle($pdf, $title) {
    $pdf->Ln(6); 
    $pdf->SetFont('helvetica', 'B', 13); 
    $pdf->Cell(0, 10, $title, 0, 1, 'L');
    // CORRECTED LINE: Use getMargins()['R']
    $margins = $pdf->getMargins();
    $rightMargin = $margins['R'];
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - $rightMargin, $pdf->GetY()); 
    $pdf->Ln(2);
    $pdf->SetFont('helvetica', '', 9); 
}

// --- Section 1: Overall Summary & Financial Snapshot ---
addSectionTitle($pdf, 'Platform Overview');
$summary_html = "<table {$table_style}>
    <tr {$th_style}><th width=\"70%\">Metric</th><th width=\"30%\" {$td_style_right}>Value</th></tr>
    <tr><td>Total Properties</td><td {$td_style_right}>{$property_count}</td></tr>
    <tr><td>Featured Properties</td><td {$td_style_right}>{$featured_count}</td></tr>
    <tr><td>Total Users (Clients & Agents)</td><td {$td_style_right}>{$user_count}</td></tr>
    <tr><td> - Agents</td><td {$td_style_right}>{$agent_count}</td></tr>
    <tr><td> - Clients/Regular Users</td><td {$td_style_right}>{$regular_user_count}</td></tr>
    <tr><td>Total Appointments</td><td {$td_style_right}>{$appointment_count}</td></tr>
    <tr><td>Total Feedbacks Received</td><td {$td_style_right}>{$feedback_count}</td></tr>
    <tr><td>Total Contact Queries</td><td {$td_style_right}>{$contact_queries_count}</td></tr>
</table>";
$pdf->writeHTML($summary_html, true, false, true, false, '');

$pdf->Ln(4);
$financial_html = "<table {$table_style}>
    <tr {$th_style}><th width=\"70%\">Financial Metric</th><th width=\"30%\" {$td_style_right}>Value (Rs.)</th></tr>
    <tr><td>Value of Active Sale Listings</td><td {$td_style_right}>".number_format($for_sale_active_value, 0)."</td></tr>
    <tr><td>Value of Active Rent Listings (per period)</td><td {$td_style_right}>".number_format($for_rent_active_value, 0)."</td></tr>
    <tr><td>Total Value of Sold Properties</td><td {$td_style_right}>".number_format($sold_value, 0)."</td></tr>
    <tr><td>Total Value of Rented Out Properties (per period)</td><td {$td_style_right}>".number_format($rented_out_value, 0)."</td></tr>
</table>";
$pdf->writeHTML($financial_html, true, false, true, false, '');

// --- Section 2: Property Analysis ---
$pdf->AddPage(); 
addSectionTitle($pdf, 'Property Portfolio Analysis');

$prop_type_html = "<h4 {$h4_style}>Properties by Type:</h4><table {$table_style}>
    <tr {$th_style}><th width=\"60%\">Property Type</th><th width=\"20%\" {$td_style_right}>Count</th><th width=\"20%\" {$td_style_right}>Avg. Price (Rs.)</th></tr>";
if(!empty($properties_by_type)){
    foreach ($properties_by_type as $ptype) {
        $avg_price = $ptype['avg_price'] ?? 0;
        $prop_type_html .= "<tr><td>".htmlspecialchars(ucfirst($ptype['type']))."</td><td {$td_style_right}>{$ptype['count']}</td><td {$td_style_right}>".number_format($avg_price, 0)."</td></tr>";
    }
} else { $prop_type_html .= "<tr><td colspan='3' style='text-align:center;'>No data by type.</td></tr>"; }
$prop_type_html .= "</table>";
$pdf->writeHTML($prop_type_html, true, false, true, false, '');
$pdf->Ln(2);

$prop_bhk_html = "<h4 {$h4_style}>Properties by BHK:</h4><table {$table_style}>
    <tr {$th_style}><th width=\"60%\">BHK Configuration</th><th width=\"20%\" {$td_style_right}>Count</th><th width=\"20%\" {$td_style_right}>Avg. Price (Rs.)</th></tr>";
if(!empty($properties_by_bhk)){
    foreach ($properties_by_bhk as $pbhk) {
        $avg_price_bhk = $pbhk['avg_price'] ?? 0;
        $prop_bhk_html .= "<tr><td>".htmlspecialchars($pbhk['bhk'])."</td><td {$td_style_right}>{$pbhk['count']}</td><td {$td_style_right}>".number_format($avg_price_bhk, 0)."</td></tr>";
    }
} else { $prop_bhk_html .= "<tr><td colspan='3' style='text-align:center;'>No data by BHK.</td></tr>"; }
$prop_bhk_html .= "</table>";
$pdf->writeHTML($prop_bhk_html, true, false, true, false, '');

$pdf->Ln(4);
$prop_city_html = "<h4 {$h4_style}>Properties by City (Top 10 by Count):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"35%\">City</th><th width=\"15%\" {$td_style_right}>Count</th><th width=\"25%\" {$td_style_right}>Avg. Price (Rs.)</th><th width=\"25%\" {$td_style_right}>Price Range (Min-Max)</th></tr>";
if(!empty($properties_by_city_pdf)){
    foreach ($properties_by_city_pdf as $pcity) {
        $min_price = $pcity['min_price'] ?? 0;
        $max_price = $pcity['max_price'] ?? 0;
        $avg_price_city = $pcity['avg_price'] ?? 0;
        $price_range = ($pcity['count'] > 0 ? number_format($min_price,0) . " - " . number_format($max_price,0) : 'N/A');
        $prop_city_html .= "<tr><td>".htmlspecialchars(ucfirst($pcity['city']))."</td><td {$td_style_right}>{$pcity['count']}</td><td {$td_style_right}>".number_format($avg_price_city, 0)."</td><td {$td_style_right}>{$price_range}</td></tr>";
    }
} else { $prop_city_html .= "<tr><td colspan='4' style='text-align:center;'>No data by city.</td></tr>"; }
$prop_city_html .= "</table>";
$pdf->writeHTML($prop_city_html, true, false, true, false, '');

$pdf->Ln(4);
$prop_state_html = "<h4 {$h4_style}>Properties by State:</h4><table {$table_style}>
    <tr {$th_style}><th width=\"35%\">State</th><th width=\"15%\" {$td_style_right}>Count</th><th width=\"25%\" {$td_style_right}>Avg. Price (Rs.)</th><th width=\"25%\" {$td_style_right}>Price Range (Min-Max)</th></tr>";
if(!empty($properties_by_state_pdf)){
    foreach ($properties_by_state_pdf as $pstate) {
        $min_price_state = $pstate['min_price'] ?? 0;
        $max_price_state = $pstate['max_price'] ?? 0;
        $avg_price_state = $pstate['avg_price'] ?? 0;
        $price_range = ($pstate['count'] > 0 ? number_format($min_price_state,0) . " - " . number_format($max_price_state,0) : 'N/A');
        $prop_state_html .= "<tr><td>".htmlspecialchars(ucfirst($pstate['state']))."</td><td {$td_style_right}>{$pstate['count']}</td><td {$td_style_right}>".number_format($avg_price_state, 0)."</td><td {$td_style_right}>{$price_range}</td></tr>";
    }
} else { $prop_state_html .= "<tr><td colspan='4' style='text-align:center;'>No data by state.</td></tr>"; }
$prop_state_html .= "</table>";
$pdf->writeHTML($prop_state_html, true, false, true, false, '');

$pdf->Ln(4);
$top_sale_html = "<h4 {$h4_style}>Top 5 Most Expensive (For Sale):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"35%\">Title</th><th width=\"20%\">City</th><th width=\"15%\">Size (sqft)</th><th width=\"10%\">BHK</th><th width=\"20%\" {$td_style_right}>Price (Rs.)</th></tr>";
if(!empty($top_expensive_sale_pdf)){
    foreach ($top_expensive_sale_pdf as $prop) {
        $top_sale_html .= "<tr><td>".htmlspecialchars($prop['title'])."</td><td>".htmlspecialchars($prop['city'])."</td><td>".htmlspecialchars($prop['size'])."</td><td>".htmlspecialchars($prop['bhk'])."</td><td {$td_style_right}>".number_format($prop['price'],0)."</td></tr>";
    }
} else { $top_sale_html .= "<tr><td colspan='5' style='text-align:center;'>No expensive sale properties to display.</td></tr>"; }
$top_sale_html .= "</table>";
$pdf->writeHTML($top_sale_html, true, false, true, false, '');

$pdf->Ln(4);
$top_rent_html = "<h4 {$h4_style}>Top 5 Most Expensive (For Rent):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"35%\">Title</th><th width=\"20%\">City</th><th width=\"15%\">Size (sqft)</th><th width=\"10%\">BHK</th><th width=\"20%\" {$td_style_right}>Rent (Rs./period)</th></tr>";
if(!empty($top_expensive_rent_pdf)){
    foreach ($top_expensive_rent_pdf as $prop) {
        $top_rent_html .= "<tr><td>".htmlspecialchars($prop['title'])."</td><td>".htmlspecialchars($prop['city'])."</td><td>".htmlspecialchars($prop['size'])."</td><td>".htmlspecialchars($prop['bhk'])."</td><td {$td_style_right}>".number_format($prop['price'],0)."</td></tr>";
    }
} else { $top_rent_html .= "<tr><td colspan='5' style='text-align:center;'>No expensive rent properties to display.</td></tr>"; }
$top_rent_html .= "</table>";
$pdf->writeHTML($top_rent_html, true, false, true, false, '');


$pdf->Ln(4); 
$lister_sig_html = "<h4 {$h4_style}>Property Listing Sources & Verification:</h4>
<table {$table_style}>
    <tr {$th_style}><th width=\"70%\">Metric</th><th width=\"30%\" {$td_style_right}>Count</th></tr>
    <tr><td>Properties Listed by Agents</td><td {$td_style_right}>".($property_lister_types_pdf['agent'] ?? 0)."</td></tr>
    <tr><td>Properties Listed by Users</td><td {$td_style_right}>".($property_lister_types_pdf['user'] ?? 0)."</td></tr>
    <tr><td>Properties with Digital Signature</td><td {$td_style_right}>{$properties_with_signature_pdf}</td></tr>
    <tr><td>Properties without Digital Signature</td><td {$td_style_right}>{$properties_without_signature_pdf}</td></tr>
</table>";
$pdf->writeHTML($lister_sig_html, true, false, true, false, '');


// --- Section 3: User & Agent Activity ---
$pdf->AddPage();
addSectionTitle($pdf, 'User and Agent Activity');

$top_agents_html = "<h4 {$h4_style}>Top 5 Agents (by Property Listings):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"30%\">Agent Name</th><th width=\"35%\">Email</th><th width=\"20%\">Phone</th><th width=\"15%\" {$td_style_right}>Listings</th></tr>";
if(!empty($top_agents_by_listings_pdf)){
    foreach ($top_agents_by_listings_pdf as $agent) {
        $top_agents_html .= "<tr><td>".htmlspecialchars($agent['uname'])."</td><td>".htmlspecialchars($agent['uemail'])."</td><td>".htmlspecialchars($agent['uphone'])."</td><td {$td_style_right}>{$agent['property_count']}</td></tr>";
    }
} else { $top_agents_html .= "<tr><td colspan='4' style='text-align:center;'>No agent listings.</td></tr>"; }
$top_agents_html .= "</table>";
$pdf->writeHTML($top_agents_html, true, false, true, false, '');

$pdf->Ln(4);
$top_users_l_html = "<h4 {$h4_style}>Top 5 Users (by Property Listings):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"30%\">User Name</th><th width=\"35%\">Email</th><th width=\"20%\">Phone</th><th width=\"15%\" {$td_style_right}>Listings</th></tr>";
if(!empty($top_users_by_listings_pdf)){
    foreach ($top_users_by_listings_pdf as $user) {
        $top_users_l_html .= "<tr><td>".htmlspecialchars($user['uname'])."</td><td>".htmlspecialchars($user['uemail'])."</td><td>".htmlspecialchars($user['uphone'])."</td><td {$td_style_right}>{$user['property_count']}</td></tr>";
    }
} else { $top_users_l_html .= "<tr><td colspan='4' style='text-align:center;'>No user listings.</td></tr>"; }
$top_users_l_html .= "</table>";
$pdf->writeHTML($top_users_l_html, true, false, true, false, '');


$pdf->Ln(4);
$all_users_list_html = "<h4 {$h4_style}>Recent Registered Users (Sample of up to 20):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"10%\">ID</th><th width=\"30%\">Name</th><th width=\"30%\">Email</th><th width=\"18%\">Phone</th><th width=\"12%\">Type</th></tr>";
if($all_users_pdf_res && mysqli_num_rows($all_users_pdf_res) > 0){
    mysqli_data_seek($all_users_pdf_res, 0); 
    while($row = fetchAssoc($all_users_pdf_res)){
        $all_users_list_html .= "<tr><td>{$row['uid']}</td><td>".htmlspecialchars($row['uname'])."</td><td>".htmlspecialchars($row['uemail'])."</td><td>".htmlspecialchars($row['uphone'])."</td><td>".htmlspecialchars(ucfirst($row['utype']))."</td></tr>";
    }
} else { $all_users_list_html .= "<tr><td colspan='5' style='text-align:center;'>No users to display.</td></tr>"; }
$all_users_list_html .= "</table>";
$pdf->writeHTML($all_users_list_html, true, false, true, false, '');
if($all_users_pdf_res) mysqli_free_result($all_users_pdf_res);


// --- Section 4: Appointment Insights ---
$pdf->AddPage();
addSectionTitle($pdf, 'Appointment Analysis');

$app_status_html = "<h4 {$h4_style}>Appointments by Status:</h4><table {$table_style}>
    <tr {$th_style}><th width=\"70%\">Status</th><th width=\"30%\" {$td_style_right}>Count</th></tr>";
if(!empty($appointments_by_status_pdf)){
    foreach ($appointments_by_status_pdf as $app_status) {
        $app_status_html .= "<tr><td>".htmlspecialchars(ucfirst($app_status['status']))."</td><td {$td_style_right}>{$app_status['count']}</td></tr>";
    }
} else { $app_status_html .= "<tr><td colspan='2' style='text-align:center;'>No appointment status data.</td></tr>"; }
$app_status_html .= "</table>";
$pdf->writeHTML($app_status_html, true, false, true, false, '');

$pdf->Ln(4);
$top_prop_app_html = "<h4 {$h4_style}>Top 5 Properties (by Appointments):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"15%\">Prop. ID</th><th width=\"60%\">Property Title</th><th width=\"25%\" {$td_style_right}>Appointments</th></tr>";
if(!empty($top_properties_by_appointments_pdf)){
    foreach ($top_properties_by_appointments_pdf as $prop_app) {
        $top_prop_app_html .= "<tr><td>{$prop_app['pid']}</td><td>".htmlspecialchars($prop_app['title'])."</td><td {$td_style_right}>{$prop_app['appointment_count']}</td></tr>";
    }
} else { $top_prop_app_html .= "<tr><td colspan='3' style='text-align:center;'>No property appointment data.</td></tr>"; }
$top_prop_app_html .= "</table>";
$pdf->writeHTML($top_prop_app_html, true, false, true, false, '');

$pdf->Ln(4);
$top_agents_app_html = "<h4 {$h4_style}>Top 5 Agents (by Appointments Handled):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"15%\">Agent ID</th><th width=\"60%\">Agent Name</th><th width=\"25%\" {$td_style_right}>Appointments</th></tr>";
if(!empty($top_agents_by_appointments_pdf)){
    foreach ($top_agents_by_appointments_pdf as $agent_app) {
        $top_agents_app_html .= "<tr><td>{$agent_app['uid']}</td><td>".htmlspecialchars($agent_app['agent_name'])."</td><td {$td_style_right}>{$agent_app['appointment_count']}</td></tr>";
    }
} else { $top_agents_app_html .= "<tr><td colspan='3' style='text-align:center;'>No agent appointment data.</td></tr>"; }
$top_agents_app_html .= "</table>";
$pdf->writeHTML($top_agents_app_html, true, false, true, false, '');


$pdf->Ln(4);
$recent_app_list_html = "<h4 {$h4_style}>Recent Appointments (Last 10):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"10%\">ID</th><th width=\"25%\">Client</th><th width=\"30%\">Property</th><th width=\"20%\">Date & Time</th><th width=\"15%\">Status</th></tr>";
if($recent_appointments_pdf_res && mysqli_num_rows($recent_appointments_pdf_res) > 0){
    mysqli_data_seek($recent_appointments_pdf_res, 0);
    while($row = fetchAssoc($recent_appointments_pdf_res)){
        $client_name = $row['client_name'] ?? 'N/A';
        $property_title = $row['property_title'] ?? 'N/A';
        $datetime = date('Y-m-d h:i A', strtotime($row['appointment_date'].' '.$row['appointment_time']));
        $recent_app_list_html .= "<tr><td>{$row['appid']}</td><td>".htmlspecialchars($client_name)."</td><td>".htmlspecialchars($property_title)."</td><td>{$datetime}</td><td>".htmlspecialchars($row['status'])."</td></tr>";
    }
} else { $recent_app_list_html .= "<tr><td colspan='5' style='text-align:center;'>No recent appointments.</td></tr>"; }
$recent_app_list_html .= "</table>";
$pdf->writeHTML($recent_app_list_html, true, false, true, false, '');
if($recent_appointments_pdf_res) mysqli_free_result($recent_appointments_pdf_res);


// --- Section 5: Engagement Metrics ---
$pdf->AddPage();
addSectionTitle($pdf, 'Platform Engagement');

$feedback_summary_html = "<h4 {$h4_style}>Feedback Summary:</h4><table {$table_style}>
    <tr {$th_style}><th width=\"70%\">Feedback Status</th><th width=\"30%\" {$td_style_right}>Count</th></tr>
    <tr><td>Active / Displayed Feedbacks</td><td {$td_style_right}>".($feedback_by_status_pdf['active'] ?? 0)."</td></tr>
    <tr><td>Inactive Feedbacks</td><td {$td_style_right}>".($feedback_by_status_pdf['inactive'] ?? 0)."</td></tr>
    <tr><td>Total Feedbacks</td><td {$td_style_right}>{$feedback_count}</td></tr>
</table>";
$pdf->writeHTML($feedback_summary_html, true, false, true, false, '');

$pdf->Ln(4);
$recent_feedback_list_html = "<h4 {$h4_style}>Recent Feedbacks (Last 5):</h4><table {$table_style}>
    <tr {$th_style}><th width=\"25%\">User</th><th width=\"45%\">Feedback (Excerpt)</th><th width=\"15%\">Date</th><th width=\"15%\">Status</th></tr>";
if($recent_feedbacks_pdf_res && mysqli_num_rows($recent_feedbacks_pdf_res) > 0){
    mysqli_data_seek($recent_feedbacks_pdf_res, 0);
    while($row = fetchAssoc($recent_feedbacks_pdf_res)){
        $f_status = (isset($row['feedback_status']) && $row['feedback_status'] == 1) ? 'Active' : 'Inactive';
        $f_desc_full = $row['fdescription'] ?? '';
        $f_desc = substr(htmlspecialchars(strip_tags($f_desc_full)), 0, 60) . (strlen($f_desc_full) > 60 ? '...' : '');
        $recent_feedback_list_html .= "<tr><td>".htmlspecialchars($row['user_name'])."</td><td>{$f_desc}</td><td>".date('Y-m-d', strtotime($row['feedback_date']))."</td><td>{$f_status}</td></tr>";
    }
} else { $recent_feedback_list_html .= "<tr><td colspan='4' style='text-align:center;'>No recent feedbacks.</td></tr>"; }
$recent_feedback_list_html .= "</table>";
$pdf->writeHTML($recent_feedback_list_html, true, false, true, false, '');
if($recent_feedbacks_pdf_res) mysqli_free_result($recent_feedbacks_pdf_res);


// --- Close connection ---
if ($con) {
    mysqli_close($con);
}

// --- OUTPUT PDF ---
if (ob_get_level()) {
    ob_end_clean();
}
$pdf->Output('Admin_Report_'.date('Ymd_His').'.pdf', 'D');
exit;
?>