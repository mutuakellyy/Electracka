<?php
require 'db_connect.php';
session_start();

if (
    !isset($_SESSION['emailaddress'], $_SESSION['institution'], $_SESSION['role']) ||
    strtolower($_SESSION['role']) !== 'supervisor') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit();
}
 
// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || !isset($data['title'], $data['details'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields.']);
    exit();
}

$title = mysqli_real_escape_string($dbconnect, $data['title']);
$details = mysqli_real_escape_string($dbconnect, $data['details']);
$supervisor_email = $_SESSION['emailaddress'];
$institution_id = $_SESSION['institution'];

$sql = "INSERT INTO supervisor_report (supervisor_email, institution_id, title, details)
        VALUES ('$supervisor_email', '$institution_id', '$title', '$details')";

if (mysqli_query($dbconnect, $sql)) {
    echo json_encode(['status' => 'success', 'message' => 'Report submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit report.']);
}
