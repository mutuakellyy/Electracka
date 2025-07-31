<?php
session_start();
require 'db_connect.php';
require 'my_function.php';

header("Content-Type: application/json");
ini_set('display_errors', 0);
error_reporting(0);

// Read and decode JSON
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);
if (!is_array($data)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input format.']);
    exit();
}

// Validate input
$email = sanitize( trim($data['email'] ?? ''));
$password_input = $data['password'] ?? '';

if (!$email || !$password_input) {
    echo json_encode(['status' => 'error', 'message' => 'Missing email or password.']);
    exit();
}

// Encrypt and check
$encrypted_password = crypt($password_input, 'vote_445');
$stmt = $dbconnect->prepare("SELECT * FROM user WHERE emailaddress = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect Login Details.']);
    exit();
}

$user = $result->fetch_assoc();
if ($encrypted_password !== $user['password']) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect Credentials.']);
    exit();
}

// Store session and role
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}

$role = strtolower(trim($user['role']));
$firstname = $user['firstname'] ?? '';
$emailaddress = $user['emailaddress'] ?? '';
$today = date('Y-m-d');

if ($role === 'manager') {
    $redirect = 'manager.php';
} elseif($role === 'supervisor'){
    $redirect = 'supervisor.php';
}elseif($role === 'guard'){
    $redirect = 'guard.php';
}else{
    echo json_encode(['status' => 'error', 'message' => 'Invalid role assigned. Please contact admin.']);
    exit();
}

ob_clean();       // Wipe any buffered output

echo json_encode([
    'status' => 'success',
    'message' => "Welcome $firstname!",
    'role' => $role,
    'redirect_url' => $redirect
]);
exit();
