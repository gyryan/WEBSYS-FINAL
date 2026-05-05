<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year_level = mysqli_real_escape_string($conn, $_POST['year_level']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if student already exists
    $check_query = "SELECT student_id FROM students WHERE student_id = '$student_id' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['signup_error'] = 'Student ID or Email already exists. Please use different credentials.';
        header('Location: sign-up.php');
        exit;
    }
    
    // Insert new student
    $insert_query = "INSERT INTO students (student_id, first_name, last_name, middle_name, email, phone_number, address, birthday, gender, course, year_level, section, password) 
                     VALUES ('$student_id', '$first_name', '$last_name', '$middle_name', '$email', '$phone_number', '$address', '$birthday', '$gender', '$course', '$year_level', '$section', '$password')";
    
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['signup_success'] = 'Account created successfully! Please login.';
        header('Location: ../landingpage/login.php');
        exit;
    } else {
        $_SESSION['signup_error'] = 'Registration failed. Please try again.';
        header('Location: sign-up.php');
        exit;
    }
} else {
    header('Location: sign-up.php');
    exit;
}
?>