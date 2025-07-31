<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['emailaddress'])) {
    echo "Access Denied.";
    exit();
}

$firstname = mysqli_real_escape_string($dbconnect, $_POST['firstname']);
$surname = mysqli_real_escape_string($dbconnect, $_POST['surname']);
$email = $_SESSION['emailaddress'];

$query = $dbconnect->prepare("UPDATE user SET firstname = ?, surname = ? WHERE emailaddress = ?");
$query->bind_param("sss", $firstname, $surname, $email);
if ($query->execute()) {
    echo "✅ Profile updated successfully.";
} else {
    echo "❌ Failed to update profile.";
}
