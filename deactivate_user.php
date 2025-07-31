<?php
session_start();
require_once 'db_connect.php'; // Your DB connection file

// Ensure only managers can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

// Validate user ID from POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Use a prepared statement to prevent SQL injection
    $sql = "UPDATE user SET active = 0 WHERE id = ?";
    $stmt = $dbconnect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "User ID {$user_id} has been successfully deactivated.";
            header("Location: manager.php");
            exit();

        } else {
            echo "Error: Could not deactivate user.";
        }
        $stmt->close();
    } else {
        echo "Error: Failed to prepare statement.";
    }
} else {
    echo "Invalid request.";
}
$dbconnect->close();

