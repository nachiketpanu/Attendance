<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    die("Not logged in!");
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "attendance_db";

$conn = new mysqli($host, $user, $pass, $dbname);

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION["user_id"];
$type = $data["type"];
$time = $data["time"];

$img = $data["photo"];
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$binary = base64_decode($img);

// default
$duration = 0;
$salary = 0;
$ratePerHour = 100; // <- Salary Rate (₹100/hour)

// Agar Check-Out ho raha hai to duration aur salary calculate karo
if ($type == "Check-Out") {
    $sql = "SELECT * FROM attendance 
            WHERE user_id=? AND type='Check-In' 
            AND DATE(time)=DATE(?) 
            ORDER BY time DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checkin = $result->fetch_assoc();
        $checkinTime = strtotime($checkin["time"]);
        $checkoutTime = strtotime($time);

        $seconds = $checkoutTime - $checkinTime;
        $duration = round($seconds / 3600, 2); // hours
        $salary = $duration * $ratePerHour;
    }
}

// Save attendance
$stmt = $conn->prepare("INSERT INTO attendance (user_id, type, photo, time, duration_hours, salary) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssdd", $user_id, $type, $binary, $time, $duration, $salary);

if ($stmt->execute()) {
    echo "Attendance saved!";
} else {
    echo "Error: " . $conn->error;
}
