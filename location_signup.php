<?php
require_once 'phpqrcode/qrlib.php';

// Get inputs
$location_name = sanitize($_POST['location_name']);
$institution_id = $_SESSION['institution_id'];
$institution_name = $_SESSION['institution'];

// Insert location
$stmt = $dbconnect->prepare("INSERT INTO institution_locations (institution_id, name) VALUES (?, ?)");
$stmt->bind_param("is", $institution_id, $location_name);
$stmt->execute();
$location_id = $stmt->insert_id;

// QR content (hidden data)
$baseURL = "http://localhost/codestudio/scan_handler.php"; // or your online domain
$qrContent = "$baseURL?institution_id=$institution_id&institution_name=" . urlencode($institution_name) .
             "&location_id=$location_id&location_name=" . urlencode($location_name);

// Path to save the QR code
$filename = "qrcodes/location_" . $location_id . ".png";
if (!file_exists('qrcodes')) {
    mkdir('qrcodes', 0777, true);
}

// Generate QR code
QRcode::png($qrContent, $filename, QR_ECLEVEL_H, 5);

// Display to user (just location name visually)
echo "<h3>QR Code for <strong>$location_name</strong></h3>";
echo "<img src='$filename' alt='QR Code for $location_name'>";
