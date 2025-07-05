<?php
// --- INITIAL SETUP AND SESSION ---
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");

// --- HARD-CODED AES SETTINGS (NOT RECOMMENDED FOR PRODUCTION) ---
// These constants must be defined here to be used by openssl_encrypt below.
define('AES_CIPHER', 'aes-256-cbc');
define('AES_KEY', 'b4a8e7f1c9d2g5h3j6k8m0n2p5r7t9v1'); // 32-char key
define('AES_IV', 'q3s6u9x2z5c8f1g4');               // 16-char IV
// --- END OF AES SETTINGS ---

/*
// The functions are no longer needed in this hard-coded version.
function aes_encrypt($data) { ... } // <-- REMOVED
function aes_decrypt($data) { ... } // <-- REMOVED
*/

// --- SECURITY CHECK ---
if (!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit;
}

// --- VARIABLE INITIALIZATION ---
$uid = $_SESSION['uid'];
$error = $msg = $profile_msg = $profile_error = '';
$user_data = [];
$appointment_data = null;
$agent_data = null;

// --- FETCH USER DATA ---
$stmt = $con->prepare("SELECT * FROM user WHERE uid = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
$stmt->close();

// --- PROFILE UPDATE HANDLER ---
if (isset($_POST['update_profile'])) {
    $up_name = trim($_POST['up_name']);
    $up_email = trim($_POST['up_email']);
    $up_phone = trim($_POST['up_phone']);
    $up_type = trim($_POST['up_type']);

    if (!empty($up_name) && !empty($up_email) && !empty($up_phone) && !empty($up_type)) {
        $stmt_update = $con->prepare("UPDATE user SET uname=?, uemail=?, uphone=?, utype=? WHERE uid=?");
        $stmt_update->bind_param("ssssi", $up_name, $up_email, $up_phone, $up_type, $uid);

        if ($stmt_update->execute()) {
            $profile_msg = "<p class='message-success'>Profile Updated Successfully</p>";
            // Refresh data to show changes immediately
            $stmt_refetch = $con->prepare("SELECT * FROM user WHERE uid = ?");
            $stmt_refetch->bind_param("i", $uid);
            $stmt_refetch->execute();
            $result_refetch = $stmt_refetch->get_result();
            if ($result_refetch->num_rows > 0) {
                $user_data = $result_refetch->fetch_assoc();
            }
            $stmt_refetch->close();
        } else {
            $profile_error = "<p class='message-error'>Failed to Update Profile</p>";
        }
        $stmt_update->close();
    } else {
        $profile_error = "<p class='message-error'>Please fill all profile fields</p>";
    }
}

// --- FEEDBACK SUBMISSION HANDLER ---
if (isset($_POST['insert'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $content = trim($_POST['content']);

    if (!empty($name) && !empty($phone) && !empty($content)) {
        
        // --- DIRECT, HARD-CODED ENCRYPTION ---
        // The openssl_encrypt call is placed directly here, using the constants from the top of the file.
        $encrypted_content = openssl_encrypt($content, AES_CIPHER, AES_KEY, 0, AES_IV);
        
        $stmt_feedback = $con->prepare("INSERT INTO feedback (uid, fdescription, status) VALUES (?, ?, '0')");
        $stmt_feedback->bind_param("is", $uid, $encrypted_content);

        if ($stmt_feedback->execute()) {
            $msg = "<p class='message-success'>Feedback Sent Successfully</p>";
        } else {
            $error = "<p class='message-error'>Feedback Not Sent: " . $stmt_feedback->error . "</p>";
        }
        $stmt_feedback->close();
    } else {
        $error = "<p class='message-error'>Please fill all the fields</p>";
    }
}

// --- FETCH APPOINTMENT AND AGENT DATA ---
$stmt_appt = $con->prepare("SELECT * FROM appointment WHERE uid = ?");
$stmt_appt->bind_param("i", $uid);
$stmt_appt->execute();
$result_appt = $stmt_appt->get_result();
if ($result_appt->num_rows > 0) {
    $appointment_data = $result_appt->fetch_assoc();

    if (isset($appointment_data['agent_uid'])) {
        $agent_uid = $appointment_data['agent_uid'];
        $stmt_agent = $con->prepare("SELECT uname, uphone, uemail FROM user WHERE uid = ?");
        $stmt_agent->bind_param("i", $agent_uid);
        $stmt_agent->execute();
        $result_agent = $stmt_agent->get_result();
        if ($result_agent->num_rows > 0) {
            $agent_data = $result_agent->fetch_assoc();
        }
        $stmt_agent->close();
    }
}
$stmt_appt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Profile & Feedback</title>
<style>
  body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 20px; color: #333; }
  .container-wrapper { background: #fff; max-width: 500px; margin: 0 auto 40px auto; padding: 25px 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
  h5 { margin-bottom: 20px; color: #eb4934; font-weight: 600; font-size: 1.3rem; }
  .profile-photo { display: block; margin: 0 auto 20px auto; width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 3px solid #eb4934; }
  .input-group { margin-bottom: 15px; }
  .input-label { display: block; font-weight: 600; margin-bottom: 6px; color: #555; }
  .input-field, .input-select, .input-textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; font-size: 1rem; border: 1.5px solid #ccc; border-radius: 4px; transition: border-color 0.3s ease; }
  .input-field:focus, .input-select:focus, .input-textarea:focus { outline: none; border-color: #eb4934; box-shadow: 0 0 6px rgba(235, 73, 52, 0.4); }
  .input-textarea { min-height: 100px; resize: vertical; }
  .primary-btn { background-color: #eb4934; border: none; padding: 12px 25px; font-size: 1rem; color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: background-color 0.3s ease; }
  .primary-btn:hover { background-color: #c93f2b; }
  .message-success { color: #2e7d32; background-color: #d7ffd9; border: 1px solid #2e7d32; padding: 8px 12px; border-radius: 4px; margin-bottom: 15px; }
  .message-error { color: #d32f2f; background-color: #ffd7d7; border: 1px solid #d32f2f; padding: 8px 12px; border-radius: 4px; margin-bottom: 15px; }
  @media (max-width: 600px) { .container-wrapper { max-width: 100%; padding: 20px; } }
</style>
</head>
<body>

<?php include("include/header.php"); ?>

<!-- Profile Update Form -->
<div class="container-wrapper">
    <h5>Edit Profile</h5>
    <?php echo $profile_msg; ?>
    <?php echo $profile_error; ?>

    <img src="<?php echo !empty($user_data['uimage']) ? 'admin/user/' . htmlspecialchars($user_data['uimage']) : 'https://via.placeholder.com/150?text=No+Image'; ?>" class="profile-photo" alt="Profile" />
    <form method="post" action="">
        <div class="input-group">
            <label class="input-label">Name</label>
            <input class="input-field" name="up_name" required value="<?php echo htmlspecialchars($user_data['uname'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label class="input-label">Email</label>
            <input type="email" class="input-field" name="up_email" required value="<?php echo htmlspecialchars($user_data['uemail'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label class="input-label">Phone</label>
            <input class="input-field" name="up_phone" required value="<?php echo htmlspecialchars($user_data['uphone'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label class="input-label">User Type</label>
            <select class="input-select" name="up_type" required>
                <option value="user" <?php echo ($user_data['utype'] ?? '') === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="agent" <?php echo ($user_data['utype'] ?? '') === 'agent' ? 'selected' : ''; ?>>Agent</option>
            </select>
        </div>
        <button class="primary-btn" type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

<!-- Feedback Form -->
<div class="container-wrapper">
    <h5>Send Feedback</h5>
    <?php echo $msg; ?>
    <?php echo $error; ?>
    <form method="post" action="">
        <div class="input-group">
            <label class="input-label">Name</label>
            <input class="input-field" name="name" required value="<?php echo htmlspecialchars($user_data['uname'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label class="input-label">Phone</label>
            <input class="input-field" name="phone" required value="<?php echo htmlspecialchars($user_data['uphone'] ?? ''); ?>">
        </div>
        <div class="input-group">
            <label class="input-label">Feedback</label>
            <textarea class="input-textarea" name="content" required></textarea>
        </div>
        <button class="primary-btn" name="insert" type="submit">Send Feedback</button>
    </form>
</div>

<!-- Appointment and Agent Info -->
<div class="container-wrapper">
    <h5>Appointment Status</h5>
    <?php if ($appointment_data): ?>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($appointment_data['title'] ?? ''); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($appointment_data['status'] ?? ''); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment_data['date'] ?? ''); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_data['time'] ?? ''); ?></p>

        <?php if ($agent_data): ?>
            <hr>
            <p><strong>Agent Name:</strong> <?php echo htmlspecialchars($agent_data['uname'] ?? ''); ?></p>
            <p><strong>Agent Contact:</strong> <?php echo htmlspecialchars($agent_data['uphone'] ?? ''); ?></p>
            <p><strong>Agent Email:</strong> <?php echo htmlspecialchars($agent_data['uemail'] ?? ''); ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p style="text-align:center;">No appointment record found.</p>
    <?php endif; ?>
</div>

<?php include("include/footer.php"); ?>

</body>
</html>