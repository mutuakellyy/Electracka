<?php
require 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($dbconnect, $_POST['location_name']);
    $institution_id = $_SESSION['institution'];

    $sql = "INSERT INTO institution_locations (institution_id, name) VALUES ('$institution_id', '$name')";
    mysqli_query($dbconnect, $sql);

    header("Location: supervisor.php");
    exit();
}

