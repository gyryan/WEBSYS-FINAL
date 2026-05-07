<?php
session_start();
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Fetch student data from DB
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, 's', $_SESSION['student_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$picPath = $user['profile_pic'] 
    ? '../uploads/profile_pics/' . $user['profile_pic']
    : null;

// Get initials for avatar
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$fullName  = $user['first_name'] . ' ' . $user['last_name'];

// Fetch student's grades
$student_id = $_SESSION['student_id'];
$student_section = $user['section'];

// Get all courses for the student's section with their grades
$grades_query = "
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
        g.semester,
        g.school_year
    FROM schedules s
    LEFT JOIN grades g ON g.course_code = s.course_code 
        AND g.student_id = '$student_id'
    WHERE s.section = '$student_section'
    ORDER BY s.course_code
";
$grades_result = mysqli_query($conn, $grades_query);
$student_grades = [];
while ($row = mysqli_fetch_assoc($grades_result)) {
    $student_grades[] = $row;
}

// Calculate GPA function
function calculateStudentGPA($courses) {
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
        
        if ($gradePoint !== null && $units > 0) {
            $total_grade_points += $gradePoint * $units;
            $total_units += $units;
        }
    }
    
    return $total_units > 0 ? number_format($total_grade_points / $total_units, 2) : '0.00';
}

// Calculate total enrolled units
function getStudentTotalUnits($courses) {
    $total_units = 0;
    foreach ($courses as $course) {
        $total_units += floatval($course['units']);
    }
    return $total_units;
}

// Get grade remark
function getGradeRemark($grade) {
    if (empty($grade)) return 'Not yet graded';
    
    $remarks = [
        '1.00' => 'Excellent',
        '1.25' => 'Excellent',
        '1.50' => 'Very Good',
        '1.75' => 'Good',
        '2.00' => 'Satisfactory',
        '2.25' => 'Satisfactory',
        '2.50' => 'Satisfactory',
        '2.75' => 'Fair',
        '3.00' => 'Fair',
        '5.00' => 'Failed',
        'INC' => 'Incomplete',
        'W' => 'Withdrawn'
    ];
    
    return $remarks[$grade] ?? $grade;
}

$gpa = calculateStudentGPA($student_grades);
$totalUnits = getStudentTotalUnits($student_grades);
$passedCourses = 0;
$failedCourses = 0;

foreach ($student_grades as $course) {
    if (!empty($course['grade']) && $course['grade'] != 'INC' && $course['grade'] != 'W') {
        if (floatval($course['grade']) >= 3.00 && floatval($course['grade']) <= 5.00) {
            if (floatval($course['grade']) == 5.00) {
                $failedCourses++;
            } else {
                $passedCourses++;
            }
        } elseif (floatval($course['grade']) < 3.00) {
            $passedCourses++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — My Grades</title>
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
      --warning: #f59e0b;
      --danger: #ef4444;
    }
    body {
      margin: 0; min-height: 100vh;
      background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%);
      color: var(--text);
    }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }

    /* ── App Shell ── */
    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    /* ── Sidebar ── */
    .sidebar {
      background: linear-gradient(180deg,#3b0d51 0%,#14132b 100%);
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

    /* ── Main ── */
    .main-area { padding: 28px 32px; }

    /* ── Topbar ── */
    .topbar {
      display: flex; flex-wrap: wrap; justify-content: space-between;
      align-items: center; gap: 24px; margin-bottom: 28px;
    }
    .page-title { margin: 0; font-size: clamp(22px,3vw,28px); font-weight: 800; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .chatbot-btn {
      width: 52px; height: 52px; border-radius: 16px;
      border: 1px solid var(--border); background: #fff; color: #7c3aed;
      display: grid; place-items: center; font-size: 22px;
      transition: background .2s, transform .2s, box-shadow .2s;
      box-shadow: 0 2px 8px rgba(15,23,42,.04);
    }
    .chatbot-btn:hover { background: var(--accent-soft); transform: translateY(-1px); }
    .profile-card {
      display: flex; align-items: center; gap: 16px;
      padding: 14px 18px; background: #fff;
      border: 1px solid var(--border); border-radius: 18px;
    }
    .profile-avatar {
      width: 52px; height: 52px; border-radius: 50%;
      background: linear-gradient(135deg,#7c3aed,#2563eb);
      color: #fff; place-items: center; font-weight: 800; display: grid;
      overflow: hidden;
    }
    .profile-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }

    /* GPA Summary Cards */
    .gpa-summary {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }
    .gpa-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 20px;
      text-align: center;
      transition: transform .2s, box-shadow .2s;
    }
    .gpa-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(15,23,42,.1);
    }
    .gpa-card-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }
    .gpa-card-value {
      font-size: 42px;
      font-weight: 800;
      line-height: 1;
      margin-bottom: 8px;
    }
    .gpa-card-value.small {
      font-size: 28px;
    }
    .gpa-card-note {
      font-size: 11px;
      color: var(--text-muted);
    }
    .gpa-card.primary .gpa-card-value { color: #7c3aed; }
    .gpa-card.success .gpa-card-value { color: #10b981; }
    .gpa-card.warning .gpa-card-value { color: #f59e0b; }
    .gpa-card.danger .gpa-card-value { color: #ef4444; }

    /* Table Styles */
    .grades-table-container {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(15,23,42,.05);
    }
    .table-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
    }
    .table-header h2 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
    }
    .table-wrap {
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
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

    .grade-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 13px;
    }
    .grade-excellent { background: #d1fae5; color: #065f46; }
    .grade-good { background: #dbeafe; color: #1e40af; }
    .grade-satisfactory { background: #fed7aa; color: #92400e; }
    .grade-fair { background: #fef3c7; color: #b45309; }
    .grade-failed { background: #fee2e2; color: #991b1b; }
    .grade-na { background: #f3f4f6; color: #6b7280; }

    .grade-point {
      font-weight: 700;
      color: #7c3aed;
    }

    .empty-grades {
      text-align: center;
      padding: 60px 20px;
    }
    .empty-grades p {
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .gpa-summary { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 14px; }
      .gpa-summary { grid-template-columns: 1fr; }
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
        <span class="brand-sub">Student Dashboard</span>
      </div>
    </div>
    <nav class="sidebar-nav" aria-label="Dashboard navigation">
      <a href="dashboard.php"   class="nav-item"><span class="nav-item-icon">🏠</span>Dashboard</a>
      <a href="events.php"      class="nav-item"><span class="nav-item-icon">📅</span>Events</a>
      <a href="SchoolSched.php" class="nav-item"><span class="nav-item-icon">📋</span>Schedule</a>
      <a href="grades.php"      class="nav-item active"><span class="nav-item-icon">📝</span>Grades</a>
      <a href="account.php"     class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'"> Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <h1 class="page-title">My Grades</h1>
      <div class="topbar-right">
        <button type="button" class="chatbot-btn" title="AI Assistant" onclick="alert('Chatbot coming soon!')">🤖</button>
        <div class="profile-card">
          <div class="profile-avatar">
            <?php if ($picPath): ?>
              <img src="<?= htmlspecialchars($picPath) ?>" alt="Profile Picture" />
            <?php else: ?>
              <?= $initials ?>
            <?php endif; ?>
          </div>
          <div>
            <p class="profile-name"><?= htmlspecialchars($fullName) ?></p>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
          </div>
        </div>
      </div>
    </header>

    <!-- GPA Summary Cards -->
    <div class="gpa-summary">
      <div class="gpa-card primary">
        <div class="gpa-card-label">Current GPA</div>
        <div class="gpa-card-value"><?= $gpa ?></div>
        <div class="gpa-card-note">Grade Point Average</div>
      </div>
      <div class="gpa-card success">
        <div class="gpa-card-label">Total Units</div>
        <div class="gpa-card-value small"><?= $totalUnits ?></div>
        <div class="gpa-card-note">Enrolled this semester</div>
      </div>
      <div class="gpa-card warning">
        <div class="gpa-card-label">Passed Courses</div>
        <div class="gpa-card-value small"><?= $passedCourses ?></div>
        <div class="gpa-card-note">Successfully completed</div>
      </div>
      <div class="gpa-card danger">
        <div class="gpa-card-label">Failed Courses</div>
        <div class="gpa-card-value small"><?= $failedCourses ?></div>
        <div class="gpa-card-note">Need to retake</div>
      </div>
    </div>

    <!-- Grades Table -->
    <div class="grades-table-container">
      <div class="table-header">
        <h2>📚 Academic Record - 1st Semester, 2025-2026</h2>
      </div>
      
      <?php if (empty($student_grades)): ?>
        <div class="empty-grades">
          <p>No courses found for your section.</p>
          <p style="font-size: 12px;">Please contact your administrator to assign your schedule.</p>
        </div>
      <?php else: ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Units</th>
                <th>Schedule</th>
                <th>Professor</th>
                <th>Grade</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($student_grades as $course): ?>
                <tr>
                  <td class="course-code"><?= htmlspecialchars($course['course_code']) ?></td>
                  <td><?= htmlspecialchars($course['course_name']) ?></td>
                  <td class="units-val"><?= $course['units'] ?></td>
                  <td>
                    <?= htmlspecialchars($course['day'] ?? 'TBA') ?><br>
                    <small><?= htmlspecialchars($course['time'] ?? 'TBA') ?></small>
                  </td>
                  <td><?= htmlspecialchars($course['professor'] ?? 'TBA') ?></td>
                  <td>
                    <?php if (!empty($course['grade'])): ?>
                      <?php
                        $grade = $course['grade'];
                        $gradeClass = '';
                        if (is_numeric($grade)) {
                          $numGrade = floatval($grade);
                          if ($numGrade <= 1.50) $gradeClass = 'grade-excellent';
                          elseif ($numGrade <= 2.00) $gradeClass = 'grade-good';
                          elseif ($numGrade <= 2.75) $gradeClass = 'grade-satisfactory';
                          elseif ($numGrade <= 3.00) $gradeClass = 'grade-fair';
                          elseif ($numGrade == 5.00) $gradeClass = 'grade-failed';
                          else $gradeClass = 'grade-na';
                        } else {
                          $gradeClass = 'grade-na';
                        }
                      ?>
                      <span class="grade-badge <?= $gradeClass ?>"><?= htmlspecialchars($course['grade']) ?></span>
                    <?php else: ?>
                      <span class="grade-badge grade-na">Not yet graded</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($course['grade'])): ?>
                      <?= htmlspecialchars(getGradeRemark($course['grade'])) ?>
                    <?php else: ?>
                      Pending
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>