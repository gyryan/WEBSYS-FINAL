<?php
session_start();
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Handle grade update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grades'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $grades = $_POST['grades'] ?? [];
    
    foreach ($grades as $course_code => $grade_data) {
        $grade_value = mysqli_real_escape_string($conn, $grade_data['grade']);
        $grade_point = calculateGradePoint($grade_value);
        
        // Check if grade record exists
        $check_query = mysqli_query($conn, "SELECT id FROM grades WHERE student_id = '$student_id' AND course_code = '$course_code'");
        
        if (mysqli_num_rows($check_query) > 0) {
            $update_query = "UPDATE grades SET 
                             grade = '$grade_value', 
                             grade_point = '$grade_point',
                             updated_at = NOW()
                             WHERE student_id = '$student_id' AND course_code = '$course_code'";
            mysqli_query($conn, $update_query);
        } else {
            // Get course info from schedules
            $course_query = mysqli_query($conn, "SELECT course_name, units FROM schedules WHERE course_code = '$course_code' LIMIT 1");
            $course = mysqli_fetch_assoc($course_query);
            
            $insert_query = "INSERT INTO grades (student_id, course_code, course_name, units, grade, grade_point, semester, school_year) 
                             VALUES ('$student_id', '$course_code', '{$course['course_name']}', '{$course['units']}', '$grade_value', '$grade_point', '1st Semester', '2025-2026')";
            mysqli_query($conn, $insert_query);
        }
    }
    
    $_SESSION['message'] = "Grades updated successfully!";
    header("Location: admin_grades.php?student_id=" . urlencode($student_id));
    exit;
}

// Search student
$search_student_id = $_GET['student_id'] ?? '';
$student_data = null;
$student_courses = [];

if ($search_student_id) {
    $student_query = mysqli_query($conn, "SELECT * FROM students WHERE student_id = '".mysqli_real_escape_string($conn, $search_student_id)."'");
    $student_data = mysqli_fetch_assoc($student_query);
    
    if ($student_data) {
        // Get all courses from schedule with their grades (or NULL if not graded)
        $courses_query = "
            SELECT 
                s.course_code,
                s.course_name,
                s.units,
                s.day,
                s.time,
                s.room,
                s.professor,
                g.grade,
                g.grade_point,
                g.id as grade_id
            FROM schedules s
            LEFT JOIN grades g ON g.course_code = s.course_code 
                AND g.student_id = '{$student_data['student_id']}'
            WHERE s.section = '{$student_data['section']}'
            ORDER BY s.course_code
        ";
        $courses_result = mysqli_query($conn, $courses_query);
        while ($row = mysqli_fetch_assoc($courses_result)) {
            $student_courses[] = $row;
        }
    }
}

// Get all students for listing
$students_query = mysqli_query($conn, "SELECT student_id, first_name, last_name, course, section FROM students ORDER BY last_name");

function calculateGradePoint($grade) {
    $grade_map = [
        'A' => 4.00, 'A-' => 3.70,
        'B+' => 3.30, 'B' => 3.00, 'B-' => 2.70,
        'C+' => 2.30, 'C' => 2.00, 'C-' => 1.70,
        'D' => 1.00, 'F' => 0.00,
        'INC' => 0.00, 'W' => 0.00
    ];
    return $grade_map[$grade] ?? null;
}

function calculateGPA($courses) {
    $total_points = 0;
    $total_units = 0;
    foreach ($courses as $course) {
        if ($course['grade_point'] && $course['grade'] != 'INC' && $course['grade'] != 'W') {
            $total_points += $course['grade_point'] * $course['units'];
            $total_units += $course['units'];
        }
    }
    return $total_units > 0 ? number_format($total_points / $total_units, 2) : 'N/A';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Admin Grade Management</title>
  <link rel="icon" type="image/png" href="../images/QCU-logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    :root {
      font-family: 'Montserrat', sans-serif;
      --bg: #f8fafc;
      --surface: #ffffff;
      --surface-strong: #f1f5f9;
      --accent: #5b21b6;
      --accent-soft: #eef2ff;
      --text: #1f2937;
      --text-muted: #64748b;
      --border: rgba(148,163,184,.15);
      --success: #10b981;
    }
    body {
      margin: 0; min-height: 100vh;
      background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%);
      color: var(--text);
    }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }

    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    .sidebar {
      background: black;
      color: #f8fafc; padding: 32px 24px;
      display: flex; flex-direction: column; gap: 32px;
      position: sticky; top: 0; height: 100vh;
    }
    .sidebar-brand { display: flex; align-items: center; gap: 14px; }
    .nav-logo {
      width: 58px; height: 58px; border-radius: 18px; overflow: hidden;
      border: 1px solid rgba(255,255,255,.18);
    }
    .nav-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-title { display: block; font-size: 16px; font-weight: 800; letter-spacing: .5px; }
    .brand-sub { display: block; color: rgba(248,250,252,.72); font-size: 12px; margin-top: 4px; }
    .sidebar-nav { display: grid; gap: 10px; }
    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 18px; border: none; background: transparent;
      color: #f8fafc; font-size: 14px; border-radius: 14px;
      transition: background .2s, transform .2s;