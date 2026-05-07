<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
$password = $_POST['password'];

// Check if it's admin login
if ($student_id === 'admin' && $password === 'admin123') {
    $_SESSION['student_id'] = 'admin';
    $_SESSION['full_name'] = 'Administrator';
    $_SESSION['role'] = 'admin';
    header('Location: ../admin_dashboard/admin_dashboard.php');
    exit;
}

// Regular student login
$query = "SELECT * FROM students WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
<<<<<<< HEAD
    // Verify password
    if (password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        // Redirect to dashboard
        header('Location: ../dashboard/dashboard.php');
        exit;
=======
    // ========== CHECK FOR ADMIN LOGIN FIRST ==========
    if ($student_id === 'admin' && $password === 'admin123') {
        $_SESSION['student_id'] = 'admin';
        $_SESSION['full_name'] = 'Administrator';
        $_SESSION['is_admin'] = true;
        header('Location: ../admin_dashboard/admin_dashboard.php');
        exit;
    }
    
    // ========== CHECK FOR REGULAR STUDENT LOGIN ==========
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Try password_verify first (for hashed passwords)
        if (password_verify($password, $user['password'])) {
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['is_admin'] = false;
            
            header('Location: ../students/dashboard.php');
            exit;
        } 
        // Fallback for plain text passwords (like your existing data)
        elseif ($user['password'] === $password) {
            // Update to hashed password for future logins
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE students SET password = '$hashed' WHERE student_id = '$student_id'");
            
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['is_admin'] = false;
            
            header('Location: ../students/dashboard.php');
            exit;
        } 
        else {
            $_SESSION['login_error'] = 'Invalid password. Please try again.';
            header('Location: ../landingpage/login.php');
            exit;
        }
>>>>>>> b065e5be80900c03658d0b64868b5d5515654a40
    } else {
        $_SESSION['login_error'] = 'Invalid password. Please try again.';
        header('Location: login.php');
        exit;
    }
} else {
    $_SESSION['login_error'] = 'Student ID not found. Please check your credentials.';
    header('Location: login.php');
    exit;
}
?>
