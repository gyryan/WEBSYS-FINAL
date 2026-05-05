<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Get current profile pic filename
$stmt = mysqli_prepare($conn, "SELECT profile_pic FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, 's', $_SESSION['student_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Delete the file from the folder
if ($user['profile_pic'] && file_exists('../uploads/profile_pics/' . $user['profile_pic'])) {
    unlink('../uploads/profile_pics/' . $user['profile_pic']);
}

// Set profile_pic to NULL in DB
$stmt2 = mysqli_prepare($conn, "UPDATE students SET profile_pic = NULL WHERE student_id = ?");
mysqli_stmt_bind_param($stmt2, 's', $_SESSION['student_id']);
mysqli_stmt_execute($stmt2);

header('Location: account.php?removed=1');
exit;
?>