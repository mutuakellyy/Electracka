<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['institution'], $_SESSION['role']) || strtolower($_SESSION['role']) !== 'manager') {
    echo "Access Denied.";
    header('Location: login.php');
    exit();
}

$institution_id = $_SESSION['institution'];
$today = date('Y-m-d');

// Fetch core data
$totalQuery = "SELECT COUNT(*) AS total FROM user WHERE institution = '$institution_id'";
$presentQuery = "SELECT COUNT(*) AS present FROM attendance WHERE institution_id = '$institution_id' AND present = 1 AND date = '$today'";
$absentQuery = "SELECT COUNT(*) AS absent FROM attendance WHERE institution_id = '$institution_id' AND present = 0 AND date = '$today'";
$activeQuery = "SELECT COUNT(*) AS active FROM user WHERE institution = '$institution_id' AND active = 1";
$inactiveQuery = "SELECT COUNT(*) AS inactive FROM user WHERE institution = '$institution_id' AND active = 0";

$total = mysqli_fetch_assoc(mysqli_query($dbconnect, $totalQuery));
$present = mysqli_fetch_assoc(mysqli_query($dbconnect, $presentQuery));
$absent = mysqli_fetch_assoc(mysqli_query($dbconnect, $absentQuery));
$active = mysqli_fetch_assoc(mysqli_query($dbconnect, $activeQuery));
$inactive = mysqli_fetch_assoc(mysqli_query($dbconnect, $inactiveQuery));
$today = date('Y-m-d'); // '2025-07-30'
$institution_id = $_SESSION['institution']; // From your session


// Fetch all personnel
$users = mysqli_query($dbconnect, "SELECT * FROM user WHERE institution = '$institution_id'");

// Fetch attendance logs
$attendance_logs = mysqli_query($dbconnect, "SELECT * FROM attendance WHERE institution_id = '$institution_id' AND DATE(date) = '$today'");

// Fetch supervisor reports
$reports = mysqli_query($dbconnect, "SELECT * FROM supervisor_report WHERE institution_id = '$institution_id'");

//get the email addresses of guards
$today = date('Y-m-d');
$sql = "
    SELECT 
        u.emailaddress,
         u.firstname,
        u.surname,
        a.present,
        a.timein,
        a.timeout
    FROM 
        attendance a
    JOIN 
        user u ON a.guard_id = u.id
    WHERE 
        a.date = ?
    ORDER BY 
        a.timein DESC
";

$stmt = $dbconnect->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$attendance_logs = $stmt->get_result();
