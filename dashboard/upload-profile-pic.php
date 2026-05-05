<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $file     = $_FILES['profile_pic'];
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize  = 2 * 1024 * 1024; // 2MB

    // Validate
    if (!in_array($file['type'], $allowed)) {
        die("Only JPG, PNG, WEBP, or GIF allowed.");
    }
    if ($file['size'] > $maxSize) {
        die("File too large. Max 2MB.");
    }

    // Generate unique filename
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $_SESSION['student_id'] . '_' . time() . '.' . $ext;
    $dest     = '../uploads/profile_pics/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        // Delete old profile pic if exists
        $stmt = mysqli_prepare($conn, "SELECT profile_pic FROM students WHERE student_id = ?");
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['student_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $old    = mysqli_fetch_assoc($result);
        if ($old['profile_pic'] && file_exists('../uploads/profile_pics/' . $old['profile_pic'])) {
            unlink('../uploads/profile_pics/' . $old['profile_pic']);
        }

        // Save new filename to DB
        $stmt2 = mysqli_prepare($conn, "UPDATE students SET profile_pic = ? WHERE student_id = ?");
        mysqli_stmt_bind_param($stmt2, 'ss', $filename, $_SESSION['student_id']);
        mysqli_stmt_execute($stmt2);

        header('Location: account.php?success=1');
        exit;
    } else {
        die("Upload failed. Check folder permissions.");
    }
}
?>