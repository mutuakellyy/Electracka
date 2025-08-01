<?php
require 'db_connect.php'; // adjust path as needed
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit();
}

$email = $_SESSION['emailaddress'] ?? null;
$institution_id = $_SESSION['institution_id'] ?? null;

// Validate input
$location_id = $_POST['location_id'] ?? null;
$details = $_POST['details'] ?? null;
$scan_id = $_POST['scan_id'] ?? null; // <-- NEW

if (!$email || !$institution_id || !$location_id || !$details || !$scan_id) {
    http_response_code(400);
    echo 'Missing required data.';
    exit();
}

// Get location name and scanned_at from the scan record
$location_name = null;
$scanned_at = null;

$stmt = $dbconnect->prepare("
    SELECT il.name AS location_name, ls.scanned_at
    FROM location_scan ls
    JOIN institution_locations il ON il.id = ls.location_id
    WHERE ls.id = ? AND ls.location_id = ? AND ls.institution_id = ?
");
$stmt->bind_param("iii", $scan_id, $location_id, $institution_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $location_name = $row['location_name'];
    $scanned_at = $row['scanned_at'];
} else {
    $location_name = "Unknown";
    $scanned_at = date("Y-m-d H:i:s");
}
$stmt->close();

// Now insert report including location name and scan time
$stmt = $dbconnect->prepare("
    INSERT INTO guard_reports 
        (guard_email, institution_id, location_id, scan_id, location_name, scanned_at, details, submitted_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("siiisss", $email, $institution_id, $location_id, $scan_id, $location_name, $scanned_at, $details);

if ($stmt->execute()) {
    echo "✅ Report submitted with location & scan info!";
} else {
    http_response_code(500);
    echo "❌ Failed to submit report.";
}

$stmt->close();
$dbconnect->close();
