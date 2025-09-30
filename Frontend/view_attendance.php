<?php
session_start();
if ($_SESSION["role"] != "admin") {
    die("Access denied!");
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "attendance_db";

$conn = new mysqli($host, $user, $pass, $dbname);
$result = $conn->query("
    SELECT a.*, u.name 
    FROM attendance a 
    JOIN users u ON a.user_id = u.id 
    ORDER BY a.time DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Attendance Records</title>
</head>
<body>
  <h2>Welcome Admin <?php echo $_SESSION["name"]; ?> 👑</h2>
  <a href="logout.php">Logout</a>
  <table border="1" cellpadding="5">
  <tr>
    <th>User</th>
    <th>Type</th>
    <th>Time</th>
    <th>Hours Worked</th>
    <th>Salary (₹)</th>
    <th>Photo</th>
  </tr>
  <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row["name"] ?></td>
      <td><?= $row["type"] ?></td>
      <td><?= $row["time"] ?></td>
      <td><?= $row["duration_hours"] ?></td>
      <td><?= $row["salary"] ?></td>
      <td><img src="data:image/png;base64,<?= base64_encode($row["photo"]) ?>" width="100"></td>
    </tr>
  <?php endwhile; ?>
</table>

</body>
</html>
