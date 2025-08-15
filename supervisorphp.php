<?php
session_start();
require 'db_connect.php';
include 'generate_location_qrcodes.php';

if (!isset($_SESSION['emailaddress'], $_SESSION['institution'], $_SESSION['role']) || strtolower($_SESSION['role']) !== 'supervisor') {
    echo "Access Denied.";
    header('Location: login.php');
    exit();
}

$institution_id = $_SESSION['institution'];
$supervisor_email = $_SESSION['emailaddress'];
$today = date('Y-m-d');
if (!$institution_id) {
    die("Institution ID missing from session.");
}
// ✅ Present Guards
$users = mysqli_query($dbconnect, "SELECT * FROM user WHERE role = 'guard' AND institution = $institution_id AND active = 1");

// ✅ Available Locations
$locations = mysqli_query($dbconnect, "SELECT * FROM institution_locations WHERE institution_id = '$institution_id'");

// ✅ Guard Reports
$guardReports = mysqli_query($dbconnect, "
    SELECT * FROM guard_reports 
    WHERE institution_id = '$institution_id' 
    ORDER BY submitted_at DESC
");
$guardReports = mysqli_query($dbconnect, "
    SELECT 
        gr.guard_email,
        il.name AS location_name,
        gr.submitted_at,
        gr.details
    FROM 
        guard_reports gr
    JOIN 
        institution_locations il ON gr.location_id = il.id
    WHERE 
        gr.institution_id = '$institution_id'
    ORDER BY 
        gr.submitted_at DESC
");
// get guard emails
$guards = $dbconnect->prepare("SELECT emailaddress, firstname, surname FROM user WHERE role = 'guard' AND active = 1 AND institution = ?");
$guards->bind_param("i", $institution_id);
$guards->execute();
$guardResult = $guards->get_result();
$guardList = [];
while ($g = $guardResult->fetch_assoc()) {
    $guardList[] = $g;
}

$qrLocations = mysqli_query($dbconnect, "
    SELECT id, name 
    FROM institution_locations 
    WHERE institution_id = '$institution_id'
");
if (!$qrLocations) {
    die("Error fetching locations: " . mysqli_error($dbconnect));
}
