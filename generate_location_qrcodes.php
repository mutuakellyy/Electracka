<?php
require 'db_connect.php';
require __DIR__ . '/phpqrcode/qrlib.php';

$institution_id = 1; // or use session-based value

$query = $dbconnect->prepare("SELECT id, name FROM institution_locations WHERE institution_id = ?");
$query->bind_param("i", $institution_id);
$query->execute();
$result = $query->get_result();

if (!is_dir('qrcodes')) {
    mkdir('qrcodes');
}

while ($row = $result->fetch_assoc()) {
    $location_id = $row['id'];
    $location_name = $row['name'];

    $qrData = json_encode([
        'location_id' => $location_id,
        'institution_id' => $institution_id
    ]);

    $filename = "qrcodes/location_" . $location_id . ".png";
    QRcode::png($qrData, $filename, QR_ECLEVEL_L, 6, 2);
}

echo "✅ QR codes generated successfully.";
?>
