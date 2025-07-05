<?php
include("config.php");
$sql = "SELECT appointment.*, user.uname, property.title 
        FROM appointment 
        JOIN user ON appointment.uid = user.uid 
        JOIN property ON appointment.pid = property.pid 
        ORDER BY appointment.date DESC";
$result = mysqli_query($con, $sql);
?>

<table border="1">
    <tr>
        <th>ID</th><th>User</th><th>Property</th><th>Date</th><th>Time</th><th>Status</th><th>Message</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['appid']; ?></td>
        <td><?php echo $row['uname']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['date']; ?></td>
        <td><?php echo $row['time']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['message']; ?></td>
    </tr>
    <?php } ?>
</table>
