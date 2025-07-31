<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['institution_id'])) {
    echo json_encode(['status' => 'error']);
    exit();
}

$institution_id = $_SESSION['institution_id'];

// Count today’s reports
$today = date('Y-m-d');
$sql = "SELECT COUNT(*) as count FROM report WHERE institution_id = '$institution_id' AND DATE(date_created) = '$today'";
$res = mysqli_fetch_assoc(mysqli_query($dbconnect, $sql));

echo json_encode(['status' => 'success', 'new_reports' => $res['count']]);
?>
