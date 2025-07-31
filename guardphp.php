<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['emailaddress'], $_SESSION['institution'], $_SESSION['role']) || strtolower($_SESSION['role']) !== 'guard') {
    echo "Access Denied.";
    header('Location: login.php');
    exit();
}

$email = $_SESSION['emailaddress'];
$institution_id = $_SESSION['institution'];

// Fetch schedule
$scheduleQuery = "SELECT s.*, l.name FROM schedules s JOIN institution_locations l ON s.location_id = l.id WHERE s.guard_email = '$email' AND s.institution_id = '$institution_id'";

$schedules = mysqli_query($dbconnect, $scheduleQuery);

// Fetch available locations
$locationQuery = "SELECT id, name FROM institution_locations WHERE institution_id = '$institution_id'";
$locations = mysqli_query($dbconnect, $locationQuery);
$namequery = "SELECT firstname FROM user WHERE emailaddress = '$email'";
$nameResult = mysqli_query($dbconnect, $namequery);

if ($nameRow = mysqli_fetch_assoc($nameResult)) {
    $firstname = $nameRow['firstname'];
} else {
    $firstname = 'Guard'; // fallback in case query fails
}

