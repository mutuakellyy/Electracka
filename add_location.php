<?php
declare(strict_types=1);

// Composer autoload (adjust path if your vendor folder is elsewhere)
require __DIR__ . '/../vendor/autoload.php';

// DB connection file (this file must define $dbconnect as a mysqli connection)
require __DIR__ . '/db_connect.php';

// Endroid v6 imports — must be declared before other executable code
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;

session_start();

// Helpful debug during development (remove or turn off in production)
ini_set('display_errors', '1');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'No form submitted.';
    exit;
}

// Basic validation
if (!isset($_SESSION['institution'])) {
    echo 'Session error: institution not set.';
    exit;
}

$locationName = trim((string)($_POST['location_name'] ?? ''));
if ($locationName === '') {
    echo 'Please provide a location name.';
    exit;
}

$institutionId = (int) $_SESSION['institution'];

// Insert location using prepared statement
$insertSql = 'INSERT INTO institution_locations (institution_id, name) VALUES (?, ?)';
$stmt = mysqli_prepare($dbconnect, $insertSql);
if ($stmt === false) {
    echo 'DB prepare failed: ' . mysqli_error($dbconnect);
    exit;
}

mysqli_stmt_bind_param($stmt, 'is', $institutionId, $locationName);
$execOk = mysqli_stmt_execute($stmt);
if (! $execOk) {
    echo 'DB execute failed: ' . mysqli_stmt_error($stmt);
    exit;
}
mysqli_stmt_close($stmt);

$locationId = mysqli_insert_id($dbconnect);
if (! $locationId) {
    echo 'Failed to get inserted location id.';
    exit;
}

// Build QR payload
$qrData = json_encode([
    'location_id'    => $locationId,
    'institution_id' => $institutionId,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Ensure qrcodes directory exists and is writable
$qrDir = __DIR__ . '/qrcodes';
if (!is_dir($qrDir)) {
    if (!mkdir($qrDir, 0777, true) && !is_dir($qrDir)) {
        echo 'Failed to create qrcodes directory. Check permissions.';
        exit;
    }
}

$qrFilename = "location_{$locationId}.png";
$qrPath = $qrDir . '/' . $qrFilename;

try {
    // Create QrCode using v6 constructor style
    $qrCode = new QrCode(
        data: $qrData,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Low, // <- use the constant on this class
        size: 300,
        margin: 10
    );

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Save output file
    $result->saveToFile($qrPath);

    // Redirect back with filename (adjust URL as needed)
    header('Location: supervisor.php?qr=' . urlencode($qrFilename));
    exit;
} catch (\Throwable $e) {
    // Catch any writer/Qr errors and show message for debugging
    echo 'QR generation failed: ' . $e->getMessage();
    exit;
}
