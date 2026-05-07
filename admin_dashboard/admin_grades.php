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
        '1.00' => 1.00,
        '1.25' => 1.25,
        '1.50' => 1.50,
        '1.75' => 1.75,
        '2.00' => 2.00,
        '2.25' => 2.25,
        '2.50' => 2.50,
        '2.75' => 2.75,
        '3.00' => 3.00,
        '5.00' => 5.00,
        'INC' => null,
        'W' => null
    ];
    return isset($grade_map[$grade]) ? $grade_map[$grade] : null;
}

function calculateGPA($courses) {
    $total_grade_points = 0;
    $total_units = 0;
    
    foreach ($courses as $course) {
        // Skip if no grade or grade is INC/W
        if (empty($course['grade']) || $course['grade'] == 'INC' || $course['grade'] == 'W') {
            continue;
        }
        
        $gradeValue = $course['grade'];
        $units = floatval($course['units']);
        
        // Get grade point based on the numeric grade
        $gradePoint = null;
        
        // Handle numeric grade values (1.00, 1.25, etc.)
        if (is_numeric($gradeValue)) {
            $numericGrade = floatval($gradeValue);
            if ($numericGrade == 1.00) $gradePoint = 1.00;
            elseif ($numericGrade == 1.25) $gradePoint = 1.25;
            elseif ($numericGrade == 1.50) $gradePoint = 1.50;
            elseif ($numericGrade == 1.75) $gradePoint = 1.75;
            elseif ($numericGrade == 2.00) $gradePoint = 2.00;
            elseif ($numericGrade == 2.25) $gradePoint = 2.25;
            elseif ($numericGrade == 2.50) $gradePoint = 2.50;
            elseif ($numericGrade == 2.75) $gradePoint = 2.75;
            elseif ($numericGrade == 3.00) $gradePoint = 3.00;
            elseif ($numericGrade == 5.00) $gradePoint = 5.00;
        }
        // Handle letter grades if any
        elseif ($gradeValue == 'A') $gradePoint = 4.00;
        elseif ($gradeValue == 'B') $gradePoint = 3.00;
        elseif ($gradeValue == 'C') $gradePoint = 2.00;
        elseif ($gradeValue == 'D') $gradePoint = 1.00;
        elseif ($gradeValue == 'F') $gradePoint = 0.00;
        
        if ($gradePoint !== null && $units > 0) {
            $total_grade_points += $gradePoint * $units;
            $total_units += $units;
        }
    }
    
    return $total_units > 0 ? number_format($total_grade_points / $total_units, 2) : '0.00';
}

function getTotalEnrolledUnits($courses) {
    $total_units = 0;
    foreach ($courses as $course) {
        $total_units += floatval($course['units']);
    }
    return $total_units;
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
    }
    .nav-item-icon {
      width: 34px; height: 34px; display: grid; place-items: center;
      border-radius: 12px; background: rgba(255,255,255,.08); font-size: 16px;
    }
    .nav-item:hover, .nav-item.active { background: rgba(255,255,255,.08); transform: translateX(2px); }
    .nav-item:hover .nav-item-icon, .nav-item.active .nav-item-icon { background: rgba(255,255,255,.18); }
    .sidebar-footer { margin-top: auto; }
    .logout-button {
      width: 100%; padding: 14px 18px;
      border: 1px solid rgba(255,255,255,.18); border-radius: 14px;
      background: transparent; color: #f8fafc; font-size: 14px; font-weight: 600;
      transition: background .2s;
    }
    .logout-button:hover { background: rgba(255,255,255,.08); }

    .main-area { padding: 28px 32px; }

    .topbar {
      display: flex; flex-wrap: wrap; justify-content: space-between;
      align-items: center; gap: 24px; margin-bottom: 28px;
    }
    .page-title { margin: 0; font-size: clamp(22px,3vw,28px); font-weight: 800; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }

    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 24px;
      margin-bottom: 24px;
    }
    .card-header {
      display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
    }
    .card-header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    .card-header-icon { font-size: 20px; }

    .search-section {
      display: flex;
      gap: 12px;
      align-items: flex-end;
      flex-wrap: wrap;
    }
    .search-group {
      flex: 1;
      min-width: 250px;
    }
    .search-group label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      margin-bottom: 6px;
    }
    .search-input {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      font-family: inherit;
      transition: border-color .2s;
    }
    .search-input:focus {
      outline: none;
      border-color: var(--accent);
    }
    .btn-primary {
      padding: 12px 24px;
      background: linear-gradient(135deg, var(--accent), #6d28d9);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      transition: transform .2s, box-shadow .2s;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 14px rgba(91,33,182,.25);
    }
    .btn-secondary {
      padding: 12px 24px;
      background: #fff;
      color: var(--text);
      border: 1.5px solid var(--border);
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      transition: all .2s;
    }
    .btn-secondary:hover {
      border-color: var(--accent);
      color: var(--accent);
    }

    .table-wrap {
      border-radius: 16px;
      overflow-x: auto;
      border: 1px solid var(--border);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }
    thead tr {
      background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
      color: #fff;
    }
    thead th {
      padding: 14px 16px;
      text-align: left;
      font-size: 13px;
      font-weight: 700;
    }
    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background .15s;
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    td {
      padding: 14px 16px;
      font-size: 13px;
      vertical-align: middle;
    }
    .course-code { font-weight: 700; color: #1d4ed8; }
    .units-val { font-weight: 700; color: #d97706; }

    .grade-select {
      padding: 8px 12px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 13px;
      font-family: inherit;
      background: #fff;
      cursor: pointer;
    }
    .grade-select:focus {
      outline: none;
      border-color: var(--accent);
    }

    .student-info {
      background: linear-gradient(135deg, #eef2ff, #f5f3ff);
      border-radius: 16px;
      padding: 20px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
    }
    .student-details h3 {
      margin: 0 0 4px;
      font-size: 18px;
    }
    .student-details p {
      margin: 0;
      color: var(--text-muted);
      font-size: 13px;
    }
    .gpa-card {
      text-align: right;
    }
    .gpa-value {
      font-size: 36px;
      font-weight: 800;
      color: var(--accent);
      line-height: 1;
    }
    .gpa-label {
      font-size: 12px;
      color: var(--text-muted);
    }

    .alert {
      padding: 14px 18px;
      border-radius: 12px;
      margin-bottom: 20px;
    }
    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .student-list {
      display: grid;
      gap: 8px;
      max-height: 400px;
      overflow-y: auto;
    }
    .student-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid var(--border);
      transition: all .2s;
    }
    .student-item:hover {
      background: var(--surface-strong);
    }
    .student-item a {
      font-weight: 600;
      color: var(--accent);
    }
    .badge-na {
      background: #e5e7eb;
      color: #6b7280;
      padding: 4px 8px;
      border-radius: 8px;
      font-size: 12px;
    }

    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 14px; }
    }
  </style>
</head>
<body>
<div class="app-shell">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="nav-logo">
        <img src="../images/QCU-logo.png" alt="QCU Logo" />
      </div>
      <div>
        <span class="brand-title">QCUS-PORTAL</span>
        <span class="brand-sub">Admin Dashboard</span>
      </div>
    </div>
    <nav class="sidebar-nav" aria-label="Dashboard navigation">
      <a href="admin_dashboard.php"    class="nav-item"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php"       class="nav-item"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php"        class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item active"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"      class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer"> 
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <h1 class="page-title">Grade Management</h1>
      <div class="topbar-right">
        <button type="button" class="chatbot-btn" title="AI Assistant" style="width:52px; height:52px; border-radius:16px; border:1px solid rgba(148,163,184,.15); background:#fff; color:#7c3aed; display:grid; place-items:center; font-size:22px;" onclick="alert('Chatbot coming soon!')">🤖</button>
      </div>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Search Student Card -->
    <div class="card">
      <div class="card-header">
        <span class="card-header-icon">🔍</span>
        <h2>Find Student</h2>
      </div>
      <div class="search-section">
        <form method="GET" action="admin_grades.php" style="display: flex; gap: 12px; flex: 1; flex-wrap: wrap;">
          <div class="search-group">
            <label>Student ID</label>
            <input type="text" name="student_id" class="search-input" placeholder="Enter Student ID" value="<?= htmlspecialchars($search_student_id) ?>">
          </div>
          <button type="submit" class="btn-primary">🔍 Search</button>
          <button type="button" class="btn-secondary" onclick="window.location.href='admin_grades.php'">Clear</button>
        </form>
      </div>
    </div>

    <?php if ($search_student_id && !$student_data): ?>
      <div class="alert alert-error">Student not found. Please check the Student ID and try again.</div>
    <?php endif; ?>

    <?php if ($student_data): ?>
      <!-- Student Information -->
      <div class="student-info">
        <div class="student-details">
          <h3><?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']) ?></h3>
          <p>Student ID: <?= htmlspecialchars($student_data['student_id']) ?> | Course: <?= htmlspecialchars($student_data['course']) ?> | Section: <?= htmlspecialchars($student_data['section']) ?></p>
        </div>
        <div class="gpa-card">
          <div class="gpa-value"><?= calculateGPA($student_courses) ?></div>
          <div class="gpa-label">Current GPA</div>
        </div>
      </div>

      <!-- Grade Entry Form -->
      <div class="card">
        <div class="card-header">
          <span class="card-header-icon">📝</span>
          <h2>Edit Grades for <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']) ?></h2>
        </div>
        
        <form method="POST" action="admin_grades.php">
          <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_data['student_id']) ?>">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Course Code</th>
                  <th>Course Name</th>
                  <th>Units</th>
                  <th>Time</th>
                  <th>Professor</th>
                  <th>Grade</th>
                  <th>Current Grade</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($student_courses as $course): ?>
                  <tr>
                    <td class="course-code"><?= htmlspecialchars($course['course_code']) ?></td>
                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                    <td class="units-val"><?= $course['units'] ?></td>
                    <td><?= htmlspecialchars($course['time'] ?? 'TBA') ?></td>
                    <td><?= htmlspecialchars($course['professor'] ?? 'TBA') ?></td>
                    <td>
                      <select name="grades[<?= $course['course_code'] ?>][grade]" class="grade-select">
                        <option value="">-- Select Grade --</option>
                        <option value="1.00" <?= ($course['grade'] ?? '') == '1.00' ? 'selected' : '' ?>>1.00 - Excellent</option>
                        <option value="1.25" <?= ($course['grade'] ?? '') == '1.25' ? 'selected' : '' ?>>1.25 - Excellent</option>
                        <option value="1.50" <?= ($course['grade'] ?? '') == '1.50' ? 'selected' : '' ?>>1.50 - Very Good</option>
                        <option value="1.75" <?= ($course['grade'] ?? '') == '1.75' ? 'selected' : '' ?>>1.75 - Good</option>
                        <option value="2.00" <?= ($course['grade'] ?? '') == '2.00' ? 'selected' : '' ?>>2.00 - Satisfactory</option>
                        <option value="2.25" <?= ($course['grade'] ?? '') == '2.25' ? 'selected' : '' ?>>2.25 - Satisfactory</option>
                        <option value="2.50" <?= ($course['grade'] ?? '') == '2.50' ? 'selected' : '' ?>>2.50 - Satisfactory</option>
                        <option value="2.75" <?= ($course['grade'] ?? '') == '2.75' ? 'selected' : '' ?>>2.75 - Fair</option>
                        <option value="3.00" <?= ($course['grade'] ?? '') == '3.00' ? 'selected' : '' ?>>3.00 - Fair</option>
                        <option value="5.00" <?= ($course['grade'] ?? '') == '5.00' ? 'selected' : '' ?>>5.00 - Failed</option>
                        <option value="INC" <?= ($course['grade'] ?? '') == 'INC' ? 'selected' : '' ?>>INC - Incomplete</option>
                        <option value="W" <?= ($course['grade'] ?? '') == 'W' ? 'selected' : '' ?>>W - Withdrawn</option>
                      </select>
                    </td>
                    <td>
                      <?php if (!empty($course['grade'])): ?>
                        <span style="font-weight: 700; color: #4c1d95;"><?= htmlspecialchars($course['grade']) ?></span>
                      <?php else: ?>
                        <span class="badge-na">Not graded yet</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
            <button type="submit" name="update_grades" class="btn-primary">💾 Save All Grades</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='admin_grades.php'">Cancel</button>
          </div>
        </form>
      </div>
    <?php endif; ?>

    <!-- Quick Student Access -->
    <div class="card">
      <div class="card-header">
        <span class="card-header-icon">👥</span>
        <h2>All Students</h2>
      </div>
      <div class="student-list">
        <?php while ($student = mysqli_fetch_assoc($students_query)): ?>
          <div class="student-item">
            <div>
              <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>
              <br>
              <small><?= htmlspecialchars($student['student_id']) ?> | <?= htmlspecialchars($student['course']) ?> | Section: <?= htmlspecialchars($student['section']) ?></small>
            </div>
            <a href="admin_grades.php?student_id=<?= urlencode($student['student_id']) ?>">View Grades →</a>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>