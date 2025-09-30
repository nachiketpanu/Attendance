<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Attendance</title>
</head>
<body>
  <h2>Welcome, <?php echo $_SESSION["name"]; ?> 👋</h2>
  <a href="logout.php">Logout</a>
  <br><br>

  <video id="video" width="320" height="240" autoplay></video>
  <br>
  <button id="checkIn">✅ Check-In</button>
  <button id="checkOut">🚪 Check-Out</button>
  <br><br>

  <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
  <div id="result"></div>

  <script>
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const context = canvas.getContext("2d");

    navigator.mediaDevices.getUserMedia({ video: true })
      .then(stream => { video.srcObject = stream; })
      .catch(err => { alert("Camera error: " + err); });

    function takePhoto(type) {
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imageData = canvas.toDataURL("image/png");
      const time = new Date().toISOString().slice(0,19).replace("T"," ");

      fetch("save_attendance.php", {
        method: "POST",
        body: JSON.stringify({ 
          type: type,
          photo: imageData,
          time: time
        }),
        headers: { "Content-Type": "application/json" }
      }).then(res => res.text())
        .then(data => { alert(data); });
    }

    document.getElementById("checkIn").addEventListener("click", () => takePhoto("Check-In"));
    document.getElementById("checkOut").addEventListener("click", () => takePhoto("Check-Out"));
  </script>
</body>
</html>
