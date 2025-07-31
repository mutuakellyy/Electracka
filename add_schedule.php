<?php
require 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guard = $_POST['guard_email'];
    $start = $_POST['shift_start'];
    $end = $_POST['shift_end'];
    $interval = intval($_POST['scan_interval']);
    $location_id = intval($_POST['location_id']);
    $institution_id = $_SESSION['institution'];

    $sql = "INSERT INTO schedules (guard_email, institution_id, shift_start, shift_end, location_id, scan_interval)
            VALUES ('$guard', '$institution_id', '$start', '$end', '$location_id', '$interval')";
    mysqli_query($dbconnect, $sql);

    header("Location: supervisor.php");
    exit();
}

