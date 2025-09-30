<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "attendance_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    
    $result = $conn->query("SELECT * FROM users WHERE name='$name' LIMIT 1");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] == "admin") {
            header("Location: view_attendance.php");
        } else {
            header("Location: attendance.php");
        }
        exit;
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
  <h2>Login</h2>
  <form method="post">
    <label>Enter Name:</label>
    <input type="text" name="name" required>
    <button type="submit">Login</button>
  </form>
  <?php if(!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
</body>
</html>
