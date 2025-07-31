<?php
session_start();
include 'db_connect.php'; // include your DB connection logic here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guard_id = $_POST['guard_id'];
    $institution_id = $_SESSION['institution'];
    $status = $_POST['status']; // 1 for present, 0 for absent
    $date = date('Y-m-d');

    // Prevent duplicates by checking if already marked
    $check = $dbconnect->prepare("SELECT id FROM attendance WHERE guard_id = ? AND date = ?");
    $check->bind_param("is", $guard_id, $date);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $stmt = $dbconnect->prepare("INSERT INTO attendance (institution_id, guard_id, present, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $institution_id, $guard_id, $status, $date);
        $stmt->execute();
        $stmt->close();
    }

    $check->close();
    header("Location: supervisor.php"); // Or redirect back as needed
    exit();
}
