<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");

if(!isset($_SESSION['uemail'])) {
    header("location:login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>User Listed Property - Real Estate PHP</title>
<link rel="shortcut icon" href="images/favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Muli:400,500,600,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    font-family: 'Muli', sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 0;
}
.page-wrapper {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
    background: #fff;
}
.page-title {
    text-align: center;
    font-family: 'Comfortaa', cursive;
    margin-bottom: 30px;
}
.property-table, .appointment-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
.property-table th, .property-table td,
.appointment-table th, .appointment-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}
.property-table th {
    background-color: #e74934;
    color: #fff;
}
.appointment-table th {
    background-color: #f1f1f1;
}
.property-info {
    margin-top: 10px;
}
.appointment-table th {
    background-color: #f1f1f1;
    color: black; /* <-- Set text color to black */
}

.property-info h5 {
    margin: 0;
}
.property-info a {
    text-decoration: none;
    color: #e74934;
}
.property-info .property-location {
    font-size: 14px;
    color: #666;
}
.price {
    font-weight: bold;
    color: #333;
}
.property-table img {
    width: 120px;
    height: auto;
    border-radius: 5px;
}
.status-select {
    padding: 5px;
    border-radius: 4px;
}
.btn {
    padding: 6px 12px;
    text-decoration: none;
    color: #fff;
    border-radius: 4px;
    display: inline-block;
    margin-top: 5px;
}
.btn-info {
    background-color: #17a2b8;
}
.btn-danger {
    background-color: #dc3545;
}
.btn-success {
    background-color: #28a745;
}
</style>
</head>
<body>

<div class="page-wrapper">
    <?php include("include/header.php"); ?>

    <h2 class="page-title" style="color: #e74934;">User Listed Property</h2>

    <?php if(isset($_GET['msg'])): ?>
        <p style='text-align:center; color:green;'><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <table class="property-table">
        <thead>
            <tr>
                <th>Properties</th>
                <th>BHK</th>
                <th>Type</th>
                <th>Added Date</th>
                <th>Status</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $uid = $_SESSION['uid'];
        $query = mysqli_query($con, "SELECT * FROM `property` WHERE uid='$uid'");
        while($row = mysqli_fetch_array($query)) {
            $pid = $row['0'];
        ?>
        <tr>
            <td>
                <img src="admin/property/<?php echo htmlspecialchars($row['18']);?>" alt="property image"><br>
                <div class="property-info">
                    <h5><a href="propertydetail.php?pid=<?php echo $pid;?>"><?php echo htmlspecialchars($row['1']);?></a></h5>
                    <span class="property-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['14']);?></span>
                    <div class="price">Rs. <?php echo htmlspecialchars($row['13']);?></div>
                </div>
            </td>
            <td><?php echo htmlspecialchars($row['4']);?></td>
            <td><?php echo ucfirst(htmlspecialchars($row['5'])); ?></td>
            <td><?php echo date("d M Y", strtotime($row['25'])); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($row['23'])); ?></td>
            <td><a class="btn btn-info" href="submitpropertyupdate.php?id=<?php echo $pid;?>">Update</a></td>
            <td><a class="btn btn-danger" href="submitpropertydelete.php?id=<?php echo $pid;?>" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a></td>
        </tr>

        <!-- Appointment details -->
        <tr>
            <td colspan="7" style="background-color:#f9f9f9; padding: 15px;">
                <strong>Appointments for this property:</strong>
                <table class="appointment-table ">
                    <thead>
                        <tr>
                            <th>User Name</th>  
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Agent UID</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $app_query = mysqli_query($con, "
                            SELECT a.appid, a.title, a.date, a.time, a.message, a.status, a.agent_uid, u.uname AS username 
                            FROM appointment a 
                            JOIN user u ON a.uid = u.uid 
                            WHERE a.pid = '$pid'
                            ORDER BY a.date DESC, a.time DESC
                        ");
                        if(mysqli_num_rows($app_query) > 0){
                            while($app = mysqli_fetch_assoc($app_query)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['username']); ?></td>
                            <td><?php echo htmlspecialchars($app['title']); ?></td>
                            <td><?php echo date("d M Y", strtotime($app['date'])); ?></td>
                            <td><?php echo date("h:i A", strtotime($app['time'])); ?></td>
                            <td><?php echo htmlspecialchars($app['message']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($app['status'])); ?></td>
                            <td><?php echo htmlspecialchars($app['agent_uid']); ?></td>
                            <td>
                                <form method="POST" action="user_update_appointment_process.php" style="margin:0;">
                                    <input type="hidden" name="appid" value="<?php echo $app['appid']; ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php if($app['status']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="confirmed" <?php if($app['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                                        <option value="cancelled" <?php if($app['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                        <option value="completed" <?php if($app['status']=='completed') echo 'selected'; ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='8'>No appointments yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php include("include/footer.php"); ?>
</div>

</body>
</html>
