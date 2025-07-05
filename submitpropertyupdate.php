<?php
ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);
session_start();

include("config.php");
require_once 'verify_signature.php'; // Ensure this file exists and contains your signature functions

if (!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit();
}

// Public key is in user table => public_key
// Signature is in property table => digital_signature
$msg = "";
$pid = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

if ($pid <= 0) {
    header("Location: feature.php?msg=" . urlencode("Invalid property ID."));
    exit();
}

// Fetch property details
$propertyQuery = mysqli_query($con, "SELECT * FROM property WHERE pid='$pid'");
$propertyDetails = mysqli_fetch_assoc($propertyQuery);

if (!$propertyDetails) {
    $msg = "Property not found.";
    header("Location: feature.php?msg=" . urlencode($msg));
    exit();
}

// Check ownership
if ($_SESSION['uid'] != $propertyDetails['uid']) {
    $msg = "Unauthorized: You can't update this property.";
    header("Location: feature.php?msg=" . urlencode($msg));
    exit();
}

// Fetch the public key from the user table
$ownerUid = $propertyDetails['uid'];
// Make sure 'user' is your user table name and 'public_key' is the column for the public key
$userQuery = mysqli_query($con, "SELECT public_key FROM user WHERE uid='$ownerUid'");
$userDetails = mysqli_fetch_assoc($userQuery);

if (!$userDetails || empty($userDetails['public_key'])) {
    $msg = "Public key for the property owner not found in the user table. Cannot verify signature.";
    error_log("Public key not found for user UID: " . $ownerUid . " for property PID: " . $pid);
    header("Location: feature.php?msg=" . urlencode($msg));
    exit();
}
$storedPublicKey = $userDetails['public_key']; // This is the public key for verification

if (isset($_POST['add'])) {
    // Sanitize inputs
    $title      = mysqli_real_escape_string($con, trim($_POST['title']));
    $content    = mysqli_real_escape_string($con, trim($_POST['content']));
    $ptype      = mysqli_real_escape_string($con, $_POST['ptype']);
    $bhk        = mysqli_real_escape_string($con, $_POST['bhk']);
    $bed        = mysqli_real_escape_string($con, $_POST['bed']);
    $balc       = mysqli_real_escape_string($con, $_POST['balc']);
    $hall       = mysqli_real_escape_string($con, $_POST['hall']);
    $stype      = mysqli_real_escape_string($con, $_POST['stype'] ?? '');
    $bath       = mysqli_real_escape_string($con, $_POST['bath']);
    $kitc       = mysqli_real_escape_string($con, $_POST['kitc']);
    $floor      = mysqli_real_escape_string($con, $_POST['floor']);
    $price      = mysqli_real_escape_string($con, trim($_POST['price']));
    $city       = mysqli_real_escape_string($con, $_POST['city']);
    $asize      = mysqli_real_escape_string($con, $_POST['asize']);
    $loc        = mysqli_real_escape_string($con, $_POST['loc']);
    $state      = mysqli_real_escape_string($con, $_POST['state']);
    $status     = mysqli_real_escape_string($con, $_POST['status']);
    $totalfloor = mysqli_real_escape_string($con, $_POST['totalfl']);
    $currentUid = $_SESSION['uid']; // The logged-in user updating the property

    // Private key file upload and read
    if (!isset($_FILES['private_key']) || $_FILES['private_key']['error'] !== UPLOAD_ERR_OK) {
        $msg = "Private key file upload failed or missing. Error code: " . ($_FILES['private_key']['error'] ?? 'Not set');
        header("Location: feature.php?msg=" . urlencode($msg));
        exit();
    }
    $privateKeyFile = $_FILES['private_key']['tmp_name'];
    $privateKeyContent = file_get_contents($privateKeyFile);

    if ($privateKeyContent === false) {
        $msg = "Could not read private key file.";
        header("Location: feature.php?msg=" . urlencode($msg));
        exit();
    }

    // Data to be signed
    $dataToSign = $price;

    // Generate digital signature using functions from verify_signature.php
    $signatureResult = createDigitalSignature($privateKeyContent, $dataToSign);
    $base64Signature = "";

    if (!isset($signatureResult['error']) || $signatureResult['error']) { // Defensive check
        $msg = "Signature generation failed: " . htmlspecialchars($signatureResult['message'] ?? 'Unknown signature generation error');
        header("Location: feature.php?msg=" . urlencode($msg));
        exit();
    }
    $base64Signature = $signatureResult['signature'];

    // Verify signature with stored public key (from user table)
    // The function verifyDigitalSignature should be in verify_signature.php
    // Ensure its parameters match: verifyDigitalSignature($data, $base64Signature, $publicKeyString)
    $verificationResult = verifyDigitalSignature($dataToSign, $base64Signature, $storedPublicKey);

    if (!isset($verificationResult['success']) || !$verificationResult['success']) { // Defensive check
        $detailedMsg = "Signature verification failed. Update not allowed. Reason: " . htmlspecialchars($verificationResult['message'] ?? 'Unknown verification error');
        error_log("--- DEBUG INFO FOR FAILED VERIFICATION ---");
        error_log("Property PID: " . $pid);
        error_log("Owner UID: " . $ownerUid);
        error_log("Data Signed: " . $dataToSign);
        error_log("Generated Signature (Base64): " . $base64Signature);
        error_log("Stored Public Key from user table (UID: " . $ownerUid . ") (first 100 chars):\n" . substr($storedPublicKey, 0, 100));
        error_log("Uploaded Private Key (first 100 chars):\n" . substr($privateKeyContent, 0, 100));
        error_log("Verification result message: " . ($verificationResult['message'] ?? 'No message'));
        error_log("--- END DEBUG INFO ---");
        header("Location: feature.php?msg=" . urlencode($detailedMsg));
        exit();
    }

    // Image upload helper function
    function handleImageUpload($inputName, $defaultFilename = '', $propertyId) {
        if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == UPLOAD_ERR_OK && !empty($_FILES[$inputName]['name'])) {
            $originalFilename = basename($_FILES[$inputName]['name']);
            $sanitizedOriginalFilename = preg_replace("/[^a-zA-Z0-9._-]/", "_", $originalFilename);
            $filename = $propertyId . "_" . time() . "_" . $sanitizedOriginalFilename;
            $targetDir = "admin/property/"; // Ensure this path is correct relative to this script's location
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0755, true)) {
                    error_log("Failed to create directory: " . $targetDir);
                    return $defaultFilename;
                }
            }
            $targetFile = $targetDir . $filename;
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFile)) {
                return $filename;
            } else {
                error_log("Failed to move uploaded file '" . $_FILES[$inputName]['tmp_name'] . "' to '" . $targetFile . "' for input '" . $inputName . "'");
                return $defaultFilename;
            }
        }
        return $defaultFilename;
    }

    $aimage  = handleImageUpload('aimage',  $propertyDetails['pimage'], $pid);
    $aimage1 = handleImageUpload('aimage1', $propertyDetails['pimage1'], $pid);
    $aimage2 = handleImageUpload('aimage2', $propertyDetails['pimage2'], $pid);
    $aimage3 = handleImageUpload('aimage3', $propertyDetails['pimage3'], $pid);
    $aimage4 = handleImageUpload('aimage4', $propertyDetails['pimage4'], $pid);

    // Update property in DB
    $sql = "UPDATE property SET
        title='$title',
        pcontent='$content',
        type='$ptype',
        bhk='$bhk',
        stype='$stype',
        bedroom='$bed',
        bathroom='$bath',
        balcony='$balc',
        kitchen='$kitc',
        hall='$hall',
        floor='$floor',
        size='$asize',
        price='$price',
        location='$loc',
        city='$city',
        state='$state',
        pimage='$aimage',
        pimage1='$aimage1',
        pimage2='$aimage2',
        pimage3='$aimage3',
        pimage4='$aimage4',
        uid='$currentUid',
        status='$status',
        totalfloor='$totalfloor',
        digital_signature='$base64Signature' -- Corrected column name
        WHERE pid=$pid";

    $result = mysqli_query($con, $sql);

    if ($result) {
        $msg = "Property updated successfully with verified digital signature.";
    } else {
        $msg = "Failed to update property: " . mysqli_error($con);
        error_log("SQL Error on property update: " . mysqli_error($con) . " | Query: " . $sql);
    }

    header("Location: feature.php?msg=" . urlencode($msg));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Update Property - Real Estate</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css" />
    <style>
        /* Add some basic styling if needed */
        .form-group label { font-weight: bold; }
        .current-image-info { font-size: 0.9em; color: #555; }
        .current-image-info img { border: 1px solid #ddd; margin-top: 5px; }
    </style>
</head>
<body>

<div id="page-wrapper">
    <?php include("include/header.php"); // Make sure this file exists and paths are correct ?>

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-secondary text-center mb-4">Update Property</h2>
            </div>
        </div>

        <?php
        // Display messages passed via GET from redirects (e.g., after form submission or error)
        // This ensures messages from the PHP block above are shown.
        if (isset($_GET['msg']) && !empty($_GET['msg'])) {
            $urlMsg = htmlspecialchars(urldecode($_GET['msg']));
            $alertClass = 'alert-info'; // Default
            if (strpos(strtolower($urlMsg), "success") !== false) {
                $alertClass = 'alert-success';
            } elseif (strpos(strtolower($urlMsg), "fail") !== false || strpos(strtolower($urlMsg), "error") !== false || strpos(strtolower($urlMsg), "invalid") !== false || strpos(strtolower($urlMsg), "not found") !== false || strpos(strtolower($urlMsg), "unauthorized") !== false) {
                $alertClass = 'alert-danger';
            }
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert <?= $alertClass ?>"><?= $urlMsg; ?></div>
                </div>
            </div>
        <?php
        } elseif (!empty($msg) && !isset($_POST['add'])) {
            // This handles initial messages if $msg was set before POST block (less likely with current flow)
            $alertClass = (strpos(strtolower($msg), "success") !== false) ? 'alert-success' : 'alert-danger';
        ?>
             <div class="row">
                <div class="col-lg-12">
                    <div class="alert <?= $alertClass ?>"><?= htmlspecialchars($msg); ?></div>
                </div>
            </div>
        <?php } ?>


        <?php if ($propertyDetails): // Only show form if property details are loaded ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-5 bg-white shadow-sm rounded">
                        <form method="post" enctype="multipart/form-data" action="submitpropertyupdate.php?id=<?= intval($pid); ?>">
                            <input type="hidden" name="id" value="<?= intval($pid); ?>">

                            <div class="form-group mb-3">
                                <label for="title">Title</label>
                                <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($propertyDetails['title']); ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="content">Content / Description</label>
                                <textarea id="content" name="content" class="form-control" rows="5" required><?= htmlspecialchars($propertyDetails['pcontent']); ?></textarea>
                            </div>

                            <hr>
                            <h5 class="text-secondary">Price & Signature</h5>
                            <div class="form-group mb-3">
                                <label for="price">Price (Data to be signed)</label>
                                <input type="text" id="price" name="price" class="form-control" required value="<?= htmlspecialchars($propertyDetails['price']); ?>" placeholder="e.g., 5000000">
                            </div>

                            <div class="form-group mb-3">
                                <label for="private_key">Your Private Key File (.pem)</label>
                                <input type="file" id="private_key" name="private_key" class="form-control" required accept=".pem">
                                <small class="form-text text-muted">This key will be used to sign the price. It must correspond to the public key associated with your user account.</small>
                            </div>
                             <hr>
                            <h5 class="text-secondary">Property Details</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="ptype">Property Type</label>
                                        <select id="ptype" name="ptype" class="form-control form-select" required>
                                            <option value="">Select Type</option>
                                            <option <?= $propertyDetails['type'] == 'apartment' ? 'selected' : '' ?> value="apartment">Apartment</option>
                                            <option <?= $propertyDetails['type'] == 'flat' ? 'selected' : '' ?> value="flat">Flat</option>
                                            <option <?= $propertyDetails['type'] == 'building' ? 'selected' : '' ?> value="building">Building</option>
                                            <option <?= $propertyDetails['type'] == 'house' ? 'selected' : '' ?> value="house">House</option>
                                            <option <?= $propertyDetails['type'] == 'villa' ? 'selected' : '' ?> value="villa">Villa</option>
                                            <option <?= $propertyDetails['type'] == 'office' ? 'selected' : '' ?> value="office">Office</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="stype">Sell Type (Sale/Rent)</label>
                                        <select id="stype" name="stype" class="form-control form-select">
                                            <option value="">Select Sell Type</option>
                                            <option <?= ($propertyDetails['stype'] ?? '') == 'sale' ? 'selected' : '' ?> value="sale">For Sale</option>
                                            <option <?= ($propertyDetails['stype'] ?? '') == 'rent' ? 'selected' : '' ?> value="rent">For Rent</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="bhk">BHK</label>
                                        <input type="number" id="bhk" name="bhk" class="form-control" value="<?= htmlspecialchars($propertyDetails['bhk'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="bed">Bedrooms</label>
                                        <input type="number" id="bed" name="bed" class="form-control" value="<?= htmlspecialchars($propertyDetails['bedroom'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="bath">Bathrooms</label>
                                        <input type="number" id="bath" name="bath" class="form-control" value="<?= htmlspecialchars($propertyDetails['bathroom'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="balc">Balconies</label>
                                        <input type="number" id="balc" name="balc" class="form-control" value="<?= htmlspecialchars($propertyDetails['balcony'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="kitc">Kitchens</label>
                                        <input type="number" id="kitc" name="kitc" class="form-control" value="<?= htmlspecialchars($propertyDetails['kitchen'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="hall">Halls</label>
                                        <input type="number" id="hall" name="hall" class="form-control" value="<?= htmlspecialchars($propertyDetails['hall'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="floor">Floor</label>
                                        <input type="text" id="floor" name="floor" class="form-control" value="<?= htmlspecialchars($propertyDetails['floor'] ?? ''); ?>" placeholder="e.g., 3rd, Ground">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="totalfl">Total Floors in Building</label>
                                        <input type="number" id="totalfl" name="totalfl" class="form-control" value="<?= htmlspecialchars($propertyDetails['totalfloor'] ?? ''); ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="asize">Area Size (sq ft)</label>
                                        <input type="text" id="asize" name="asize" class="form-control" value="<?= htmlspecialchars($propertyDetails['size'] ?? ''); ?>" placeholder="e.g., 1200">
                                    </div>
                                </div>
                            </div>
                             <hr>
                            <h5 class="text-secondary">Location</h5>
                            <div class="form-group mb-3">
                                <label for="loc">Address / Location</label>
                                <input type="text" id="loc" name="loc" class="form-control" value="<?= htmlspecialchars($propertyDetails['location'] ?? ''); ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($propertyDetails['city'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="state">State</label>
                                        <input type="text" id="state" name="state" class="form-control" value="<?= htmlspecialchars($propertyDetails['state'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h5 class="text-secondary">Status & Images</h5>
                            <div class="form-group mb-3">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control form-select" required>
                                    <option value="active" <?= ($propertyDetails['status'] ?? '') == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?= ($propertyDetails['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <!-- Images (show current filename, option to upload new) -->
                            <div class="form-group mb-3">
                                <label for="aimage">Main Image</label>
                                <?php if(!empty($propertyDetails['pimage'])): ?>
                                    <p class="current-image-info">Current: <?= htmlspecialchars($propertyDetails['pimage']); ?> <br> <img src="admin/property/<?= htmlspecialchars($propertyDetails['pimage']); ?>" alt="Current Main Image" style="max-width: 100px; max-height: 100px;"></p>
                                <?php endif; ?>
                                <input type="file" id="aimage" name="aimage" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group mb-3">
                                <label for="aimage1">Image 2</label>
                                <?php if(!empty($propertyDetails['pimage1'])): ?>
                                    <p class="current-image-info">Current: <?= htmlspecialchars($propertyDetails['pimage1']); ?>  <br> <img src="admin/property/<?= htmlspecialchars($propertyDetails['pimage1']); ?>" alt="Current Image 2" style="max-width: 100px; max-height: 100px;"></p>
                                <?php endif; ?>
                                <input type="file" id="aimage1" name="aimage1" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group mb-3">
                                <label for="aimage2">Image 3</label>
                                 <?php if(!empty($propertyDetails['pimage2'])): ?>
                                    <p class="current-image-info">Current: <?= htmlspecialchars($propertyDetails['pimage2']); ?>  <br> <img src="admin/property/<?= htmlspecialchars($propertyDetails['pimage2']); ?>" alt="Current Image 3" style="max-width: 100px; max-height: 100px;"></p>
                                <?php endif; ?>
                                <input type="file" id="aimage2" name="aimage2" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group mb-3">
                                <label for="aimage3">Image 4</label>
                                 <?php if(!empty($propertyDetails['pimage3'])): ?>
                                    <p class="current-image-info">Current: <?= htmlspecialchars($propertyDetails['pimage3']); ?>  <br> <img src="admin/property/<?= htmlspecialchars($propertyDetails['pimage3']); ?>" alt="Current Image 4" style="max-width: 100px; max-height: 100px;"></p>
                                <?php endif; ?>
                                <input type="file" id="aimage3" name="aimage3" class="form-control" accept="image/*">
                            </div>
                            <div class="form-group mb-3">
                                <label for="aimage4">Image 5</label>
                                 <?php if(!empty($propertyDetails['pimage4'])): ?>
                                    <p class="current-image-info">Current: <?= htmlspecialchars($propertyDetails['pimage4']); ?>  <br> <img src="admin/property/<?= htmlspecialchars($propertyDetails['pimage4']); ?>" alt="Current Image 5" style="max-width: 100px; max-height: 100px;"></p>
                                <?php endif; ?>
                                <input type="file" id="aimage4" name="aimage4" class="form-control" accept="image/*">
                            </div>
                            <hr>
                            <button type="submit" name="add" class="btn btn-primary w-100">Update Property</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: // This 'else' corresponds to 'if ($propertyDetails)'
                // If $propertyDetails was not found initially, $msg would have been set.
                // This block is a fallback if $propertyDetails becomes unexpectedly unavailable
                // and no GET message was set.
                if (empty($_GET['msg']) && empty($msg) && !isset($_POST['add'])) :
        ?>
             <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-warning">Could not load property details. It might have been deleted or the ID is incorrect.</div>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include("include/footer.php"); // Make sure this file exists and paths are correct ?>
</div>

<!-- JS -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script> <!-- Bootstrap 5 uses bundle for Popper -->
</body>
</html>