<?php
require 'db_connect.php';
header("Content-Type: application/json");

// Read and decode input once
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data["location_id"], $data["institution_id"], $data["latitude"], $data["longitude"])) {
    echo json_encode(["message" => "Invalid location."]);
    exit;
}

$location_id = $data['location_id'];
$institution_id = $data['institution_id'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Get stored location
$stmt = $dbconnect->prepare("SELECT latitude, longitude FROM institution_locations WHERE id = ? AND institution_id = ?");
$stmt->bind_param("ii", $location_id, $institution_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["message" => "Invalid location."]);
    exit;
}

$row = $result->fetch_assoc();

// If no lat/lng stored yet, update this as the first scan
if (is_null($row['latitude']) || is_null($row['longitude'])) {
    // First scan: update coordinates
    $update = $dbconnect->prepare("UPDATE institution_locations SET latitude = ?, longitude = ? WHERE id = ? AND institution_id = ?");
    $update->bind_param("ddii", $latitude, $longitude, $location_id, $institution_id);
    $update->execute();

    echo json_encode([
        "message" => "First scan: coordinates recorded successfully.",
        "location_id" => $location_id
    ]);
    exit;
}

// Compare coordinates
function distance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371000; // meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earthRadius * $c;
}

$distance = distance($latitude, $longitude, $row['latitude'], $row['longitude']);
$status = ($distance <= 20) ? "Scan Success" : "Scan Failed";
// Log scan
$log = $dbconnect->prepare("INSERT INTO location_scan (institution_id, location_id, latitude, longitude) VALUES (?, ?, ?, ?)");
$log->bind_param("iidd", $institution_id, $location_id, $latitude, $longitude);
$log->execute();
$last_id = $dbconnect->insert_id;

$response = [
    "message" => $status,
    "location_id" => $location_id,
    "scan_id" => $last_id,
    "distance" => round($distance, 2) . " meters"
];
echo json_encode($response);




