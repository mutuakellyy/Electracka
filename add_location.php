<?php
require 'db_connect.php';
require 'vendor/autoload.php'; // ✅ Include Composer autoloader
session_start();

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($dbconnect, $_POST['location_name']);
    $institution_id = $_SESSION['institution'];

    $sql = "INSERT INTO institution_locations (institution_id, name) VALUES ('$institution_id', '$name')";
    if (mysqli_query($dbconnect, $sql)) {
        $location_id = mysqli_insert_id($dbconnect);

        // ✅ Generate QR code with location + institution
        $qrData = json_encode([
            'location_id' => $location_id,
            'institution_id' => $institution_id
        ]);

        $qrPath = "qrcodes/location_$location_id.png";

        Builder::create()
            ->writer(new PngWriter())
            ->data($qrData)
            ->size(300)
            ->margin(10)
            ->build()
            ->saveToFile($qrPath);

        // ✅ Redirect with QR filename
        header("Location: supervisor.php?qr=location_$location_id.png");
        exit();
    } else {
        echo "❌ Failed to add location.";
    }
}
