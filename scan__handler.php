<?php
require 'db_connect.php'; // Make sure your DB connection is included

// Get and sanitize inputs from the QR code
$institution_id = isset($_GET['institution_id']) ? intval($_GET['institution_id']) : null;
$institution_name = isset($_GET['institution_name']) ? urldecode($_GET['institution_name']) : null;
$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : null;
$location_name = isset($_GET['location_name']) ? urldecode($_GET['location_name']) : null;

// Basic validation
if (!$institution_id || !$location_id || !$location_name) {
    echo "<h2 style='color: red;'>Invalid or incomplete QR Code data.</h2>";
    exit();
}

// Optional: Log scan into database (you can customize this part)
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date("Y-m-d H:i:s");

// Sample logging table: location_scans (optional)
// $stmt = $dbconnect->prepare("INSERT INTO location_scans (institution_id, location_id, location_name, ip_address, scanned_at) VALUES (?, ?, ?, ?, ?)");
// $stmt->bind_param("iisss", $institution_id, $location_id, $location_name, $ip, $timestamp);
// $stmt->execute();
// $stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Location Scanned</title>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        .box {
            background: white;
            display: inline-block;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            font-size: 28px;
        }
        .location-name {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>You are at: <span class="location-name"><?= htmlspecialchars($location_name) ?></span></h2>
        <!-- Optional: You can also show institution if needed -->
        <!-- <p>Institution: <?= htmlspecialchars($institution_name) ?></p> -->
    </div>
</body>
</html>
