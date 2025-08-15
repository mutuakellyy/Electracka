<?php
require 'db_connect.php';
require __DIR__ . '/phpqrcode/qrlib.php';

// Institution ID — replace with session-based value in production
$institution_id = isset($_GET['institution_id']) ? intval($_GET['institution_id']) : null;

// QR code storage folder
$qr_folder = __DIR__ . '/qrcodes/';
if (!file_exists($qr_folder)) {
    mkdir($qr_folder, 0777, true);
}

// Build SQL query
$sql = "SELECT id, name, institution_id 
        FROM institution_locations";
if ($institution_id !== null) {
    $sql .= " WHERE institution_id = ?";
}

$stmt = $dbconnect->prepare($sql);
if ($institution_id !== null) {
    $stmt->bind_param("i", $institution_id);
}
$stmt->execute();
$result = $stmt->get_result();

// Process each location
while ($row = $result->fetch_assoc()) {
    $qr_file = $qr_folder . "location_" . $row['id'] . ".png";

    if (!file_exists($qr_file)) {
        // QR data — can be JSON or query string
        $qr_content = json_encode([
            'location_id' => $row['id'],
            'institution_id' => $row['institution_id']
        ]);

        // Generate QR code
        QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 8, 2);
    }
}

