<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['emailaddress'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['emailaddress'];
$query = $dbconnect->prepare("SELECT firstname, surname, contact, emailaddress, role FROM user WHERE emailaddress = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>

<h2>👤 Your Profile</h2>
<div class="profile-container">
    <p><strong>First Name:</strong> <?= htmlspecialchars($user['firstname']) ?></p>
    <p><strong>Surname:</strong> <?= htmlspecialchars($user['surname']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['contact']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['emailaddress']) ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
</div>
<br>
<a href="edit_profile.php"><button>Edit Profile</button></a>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, sans-serif;
        background-color: #f0f4f7;
        color: #2c3e50;
        padding: 30px;
        font-size: 18px;
    }

    h2 {
        font-size: 26px;
        color: #1a73e8;
        margin-bottom: 20px;
        border-bottom: 2px solid #1a73e8;
        padding-bottom: 5px;
    }

    .profile-container,
    form {
        background-color: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }

    .profile-container p {
        margin: 12px 0;
        font-size: 18px;
        line-height: 1.6;
    }

    label {
        font-weight: 600;
        display: block;
        margin: 20px 0 5px;
        color: #2c3e50;
    }

    input[type="text"],
    input[type="email"],
    input[type="number"] {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 17px;
    }

    button {
        margin-top: 20px;
        padding: 12px 20px;
        background-color: #1a73e8;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 17px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #155ab6;
    }

    .profile-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        max-width: 500px;
    }

    .profile-container p {
        margin: 10px 0;
        font-size: 16px;
        color: #34495e;
    }

    button {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #2980b9;
    }
</style>