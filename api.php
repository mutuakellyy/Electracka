<?php
header("Content-Type: application/json");
session_start();

require_once "db_connect.php";
require_once "helpers/functions.php";

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

$response = ['status' => 'error', 'message' => 'Invalid request'];

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function hasMarkedToday($dbconnect, $user_id) {
    $today = date('Y-m-d');
    $stmt = $dbconnect->prepare("SELECT * FROM attendance WHERE user_id = ? AND DATE(marked_at) = ?");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

switch ($action) {

    case 'login':
    if ($method === 'POST') {
        $username = clean($_POST['username'] ?? '');
        $password = clean($_POST['password'] ?? '');

        $stmt = $dbconnect->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                $alreadyMarked = hasMarkedToday($dbconnect, $user['id']);

                // Role-based dashboard redirect
                $dashboards = [
                    'manager' => 'manager_dashboard.php',
                    'supervisor' => 'supervisor_dashboard.php',
                    'guard' => 'guard_dashboard.php'
                ];

                $redirect_url = !$alreadyMarked ? 'attendance.php' : ($dashboards[$user['role']] ?? '.php');

                $response = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'role' => $user['role'],
                    'redirect_url' => $redirect_url,
                    'user_id' => $user['id'],
                    'name' => $user['name']
                ];
            } else {
                $response['message'] = 'Incorrect password';
            }
        } else {
            $response['message'] = 'User not found';
        }
    }
    break;


    // Mark Attendance (only once per day)
    case 'mark_attendance':
        if ($method === 'POST') {
            $user_id = clean($_POST['user_id'] ?? '');
            $institution_id = clean($_POST['institution_id'] ?? '');
            $status = clean($_POST['status'] ?? 'present');
            $timestamp = date("Y-m-d H:i:s");

            if (hasMarkedToday($dbconnect, $user_id)) {
                $response['message'] = 'Already marked attendance for today.';
            } else {
                $stmt = $dbconnect->prepare("INSERT INTO attendance (user_id, institution_id, status, marked_at) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $user_id, $institution_id, $status, $timestamp);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Attendance marked successfully'];
                } else {
                    $response['message'] = 'Failed to mark attendance';
                }
            }
        }
        break;

    // Fetch data based on role
    case 'get_dashboard_data':
        if ($method === 'GET' && isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $role = $user['role'];
            $dashboardData = [];

            switch ($role) {
                case 'manager':
                    // Example: fetch total users, total guards, today's attendance summary
                    $result = $dbconnect->query("SELECT COUNT(*) as total_users FROM users");
                    $dashboardData['total_users'] = $result->fetch_assoc()['total_users'];

                    $result = $dbconnect->query("SELECT COUNT(*) as total_present_today FROM attendance WHERE DATE(marked_at) = CURDATE()");
                    $dashboardData['total_present_today'] = $result->fetch_assoc()['total_present_today'];
                    break;

                case 'supervisor':
                    // Example: List of guards under their supervision
                    $stmt = $dbconnect->prepare("SELECT id, name FROM users WHERE role = 'guard' AND supervisor_id = ?");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $guards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    $dashboardData['my_guards'] = $guards;
                    break;

                case 'guard':
                    // Example: Attendance status today
                    $dashboardData['marked_today'] = hasMarkedToday($dbconnect, $user['id']);
                    break;
            }

            $response = ['status' => 'success', 'data' => $dashboardData];
        } else {
            $response['message'] = 'Unauthorized access';
        }
        break;

    // Logout
    case 'logout':
        session_destroy();
        $response = ['status' => 'success', 'message' => 'Logged out'];
        break;

    default:
        $response['message'] = 'Unknown or unsupported action.';
        break;
}

echo json_encode($response);
