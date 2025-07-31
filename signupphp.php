<?php
require "db_connect.php";
require "my_function.php";

$firstname = $surname = $securitynumber = $phonenumber = $email = $password = '';
$institution_name = $institution_email = $institution_location = $institution_phone = '';
$role = '';
$institution_id = '';
$location_names = [];
$location_names = isset($_POST['location_names']) ? $_POST['location_names'] : [];
$error = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = sanitize($_POST['fname'] ?? '');
    $surname = sanitize($_POST['surname'] ?? '');
    $securitynumber = sanitize($_POST['securitynumber'] ?? '');
    $phonenumber = sanitize($_POST['phonenumber'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($role === 'manager') {
        $institution_name = sanitize($_POST['institution_name'] ?? '');
        $institution_email = sanitize($_POST['institution_email'] ?? '');
        $institution_phone = sanitize($_POST['institution_phone'] ?? '');
        $institution_location = sanitize($_POST['institution_location'] ?? '');
    } elseif ($role === 'supervisor') {
        $institution_id = $_POST['institution_id_supervisor'] ?? '';
    } elseif ($role === 'guard') {
        $institution_id = $_POST['institution_id_guard'] ?? '';
    }

    // Validation
    if (!$firstname || !preg_match('/^[a-zA-Z]+$/', $firstname)) {
        $error['firstname'] = 'Enter a valid first name.';
    }
    if (!$surname || !preg_match('/^[a-zA-Z]+$/', $surname)) {
        $error['surname'] = 'Enter a valid surname.';
    }
    if (!$securitynumber || !ctype_digit($securitynumber)) {
        $error['securitynumber'] = 'Security number must be digits only.';
    }
    if (!$phonenumber || !ctype_digit($phonenumber) || strlen($phonenumber) != 10) {
        $error['phonenumber'] = 'Phone must be 10 digits.';
    }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error['email'] = 'Invalid email.';
    } else {
        $checkEmail = "SELECT id FROM user WHERE emailaddress = ?";
        $stmt = $dbconnect->prepare($checkEmail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error['email'] = 'Email already registered.';
        }
        $stmt->close();
    }
    if (!$password) {
        $error['password'] = 'Password required.';
    } else {
        $password = crypt($password, 'vote_445');
    }
    if (!isset($_POST['policy']) || $_POST['policy'] !== 'yes') {
        $error['policy'] = 'You must agree to the privacy policy.';
    }

    // Institution validation for manager
    if ($role === 'manager') {
        if (!$institution_name || !preg_match('/^[a-zA-Z\s]+$/', $institution_name)) {
            $error['institution_name'] = 'Institution name can only contain letters and spaces.';
        }
        if (!$institution_email || !filter_var($institution_email, FILTER_VALIDATE_EMAIL)) {
            $error['institution_email'] = 'Invalid institution email format.';
        } else {
            $sql = "SELECT * FROM institution WHERE emailaddress = '$institution_email'";
            $result = mysqli_query($dbconnect, $sql);
            if (mysqli_num_rows($result) > 0) {
                $error['institution_email'] = 'Institution email is already registered.';
            }
        }
        if (!$institution_phone || !ctype_digit($institution_phone) || strlen($institution_phone) != 10) {
            $error['institution_phone'] = 'Institution phone must be 10 digits.';
        }
        if (!$institution_location || !preg_match('/^[a-zA-Z\s]+$/', $institution_location)) {
            $error['institution_location'] = 'Institution location can only contain letters and spaces.';
        }
    }

    // If no errors, insert data
    if (empty($error)) {
        if ($role === 'manager') {
            $sqlinstitution = "INSERT INTO institution (name, emailaddress, phonenumber, location) VALUES(?, ?, ?, ?)";
            $stmt = $dbconnect->prepare($sqlinstitution);
            $stmt->bind_param("ssss", $institution_name, $institution_email, $institution_phone, $institution_location);
            $stmt->execute();
            $institution_id = $stmt->insert_id;
            $stmt->close();
        }

        // Get institution name for storing in user table
        if (!empty($institution_id)) {
            $sql = "INSERT INTO user (firstname, surname, id_number, contact, emailaddress, password,institution, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $dbconnect->prepare($sql);
            $stmt->bind_param("ssssssss", $firstname, $surname, $idnumber, $phonenumber, $email, $password, $institution_id, $role, );
            $user_saved = $stmt->execute();
            $stmt->close();
        }


        if ($user_saved && $role === 'supervisor' && !empty($location_names)) {
            $stmt = $dbconnect->prepare("INSERT INTO institution_locations (institution_id, name) VALUES (?, ?)");
            foreach ($location_names as $loc) {
                $loc_sanitized = sanitize($loc);
                $stmt->bind_param("is", $institution_id, $loc_sanitized);
                $stmt->execute();
            }
            $stmt->close();
        }

        $success = "Signup successful! <a href='login.php'>Login</a>";
    }
}

// Fetch institution options
$institutions = [];
$result = $dbconnect->query("SELECT id, name FROM institution");
if ($result) {
    $institutions = $result->fetch_all(MYSQLI_ASSOC);
}

