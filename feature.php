<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");
if(!isset($_SESSION['uemail'])) {
	header("location:login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Real Estate PHP</title>
<link rel="shortcut icon" href="images/favicon.ico">
<link href="https://fonts.googleapis.com/css?family=Muli:400,500,600,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    font-family: 'Muli', sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.page-wrapper {
    width: 100%;
    margin: 0 auto;
}

.page-title {
    font-family: 'Comfortaa', cursive;
    font-size: 28px;
    text-align: center;
    margin: 40px 0;
    color: #333;
}

/* Table Styling */
.property-table {
    width: 95%;
    margin: 0 auto 60px auto;
    background-color: #fff;
    border-collapse: collapse;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.property-table thead {
    background-color: #222;
    color: white;
}

.property-table th, 
.property-table td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

.property-table img {
    width: 120px;
    border-radius: 6px;
}

.property-info h5 {
    margin: 5px 0;
    font-size: 16px;
}

.property-info a {
    text-decoration: none;
    color: #333;
}

.property-info a:hover {
    color: #e74934;
}

.property-location {
    color: #444;
    font-size: 13px;
}

.price {
    margin-top: 8px;
    font-weight: bold;
    color: green;
}

/* Buttons */
.btn {
    padding: 6px 14px;
    font-size: 13px;
    text-decoration: none;
    border-radius: 4px;
    display: inline-block;
    color: #fff;
    transition: background-color 0.3s ease;
}

.btn-info {
    background-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-danger {
    background-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Scroll to Top */
#scroll {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #e74934;
    color: white;
    padding: 10px 12px;
    border-radius: 50%;
    display: none;
    z-index: 999;
}

#scroll i {
    font-size: 18px;
}
</style>
</head>
<body>

<div class="page-wrapper">
    <?php include("include/header.php"); ?>

<h2 class="page-title" style="color: #e74934;">User Listed Property</h2>

    <?php if(isset($_GET['msg'])) echo "<p style='text-align:center; color:green;'>{$_GET['msg']}</p>"; ?>

    <table class="property-table">
        <thead style="background-color: #e74934;">
>
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
        ?>
        <tr>
            <td>
                <img src="admin/property/<?php echo $row['18'];?>" alt="property image"><br>
                <div class="property-info">
                    <h5><a href="propertydetail.php?pid=<?php echo $row['0'];?>"><?php echo $row['1'];?></a></h5>
                    <span class="property-location"><i class="fas fa-map-marker-alt"></i> <?php echo $row['14'];?></span>
                    <div class="price">Rs. <?php echo $row['13'];?></div>
                </div>
            </td>
            <td><?php echo $row['4'];?></td>
            <td><?php echo ucfirst($row['5']); ?></td>
            <td><?php echo $row['25'];?></td>
            <td><?php echo ucfirst($row['23']); ?></td>
            <td><a class="btn btn-info" href="submitpropertyupdate.php?id=<?php echo $row['0'];?>">Update</a></td>
            <td><a class="btn btn-danger" href="submitpropertydelete.php?id=<?php echo $row['0'];?>">Delete</a></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php include("include/footer.php"); ?>

</div>

</body>
</html>
