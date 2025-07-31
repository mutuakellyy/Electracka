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

if (!$email || !$institution_id || !$location_id || !$details) {
    http_response_code(400);
    echo 'Missing required data.';
    exit();
}

// Use prepared statement to avoid SQL injection
$stmt = $dbconnect->prepare("INSERT INTO guard_reports (guard_email, institution_id, location_id, details, submitted_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("siis", $email, $institution_id, $location_id, $details);

if ($stmt->execute()) {
    echo "✅ Report submitted successfully!";
} else {
    http_response_code(500);
    echo "❌ Failed to submit report.";
}

$stmt->close();
?>
