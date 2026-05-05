<?php
require_once '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sign-up.php');
    exit;
}

// Sanitize inputs
$studentId  = trim($_POST['studentId']);
$firstName  = trim($_POST['firstName']);
$lastName   = trim($_POST['lastName']);
$middleName = trim($_POST['middleName'] ?? '');
$email      = trim($_POST['email']);
$phone      = trim($_POST['phoneNumber'] ?? '');
$address    = trim($_POST['address'] ?? '');
$birthday   = $_POST['birthday'] ?? null;
$course     = $_POST['course'];
$yearLevel  = $_POST['yearLevel'];
$section    = $_POST['section'];
$password   = $_POST['password'];
$confirm    = $_POST['confirmPassword'];
$gender = $_POST['gender'] ?? '';

// Basic validation
if ($password !== $confirm) {
    die("Passwords do not match.");
}


// Insert into DB
$stmt = mysqli_prepare($conn,
    "INSERT INTO students 
     (student_id, first_name, last_name, middle_name, email, phone_number, address, birthday, course, year_level, section, password, gender)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, 'sssssssssssss',
    $studentId, $firstName, $lastName, $middleName,
    $email, $phone, $address, $birthday,
    $course, $yearLevel, $section, $password, $gender
);

if (mysqli_stmt_execute($stmt)) {
    header('Location: login.php?registered=1');
    exit;
} else {
    $error = mysqli_error($conn);
    if (strpos($error, 'Duplicate') !== false) {
        die("Student ID or email already exists.");
    }
    die("Registration failed: " . $error);
}
?>