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

if (!$user) {
    header('Location: ../landingpage/login.php');
    exit;
}

$picPath = $user['profile_pic'] 
    ? '../uploads/profile_pics/' . $user['profile_pic']
    : null;

// Get initials for avatar
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$fullName  = $user['first_name'] . ' ' . $user['last_name'];

// FIRST: Ensure the student has grade records for all their scheduled courses
// This runs automatically every time they view the page
$ensure_grades_query = "
    INSERT INTO grades (student_id, course_code, course_name, units, semester, school_year)
    SELECT 
        ? as student_id,
        s.course_code,
        s.course_name,
        s.units,
        '1st Semester',
        '2025-2026'
    FROM schedules s
    WHERE s.section = ?
    AND NOT EXISTS (
        SELECT 1 FROM grades g 
        WHERE g.student_id = ? 
        AND g.course_code = s.course_code
    )
";

$stmt = mysqli_prepare($conn, $ensure_grades_query);
mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['student_id'], $user['section'], $_SESSION['student_id']);
mysqli_stmt_execute($stmt);

// Now fetch all courses with their grades (or NULL if not graded yet)
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
        g.id as grade_id
    FROM schedules s
    LEFT JOIN grades g ON g.course_code = s.course_code 
        AND g.student_id = ? 
        AND g.semester = '1st Semester'
        AND g.school_year = '2025-2026'
    WHERE s.section = ?
    ORDER BY s.course_code
";

$stmt = mysqli_prepare($conn, $grades_query);
mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['student_id'], $user['section']);
mysqli_stmt_execute($stmt);
$grades_result = mysqli_stmt_get_result($stmt);

$grades = [];
$total_units = 0;
$total_points = 0;
$has_grades = false;

while ($row = mysqli_fetch_assoc($grades_result)) {
    $grades[] = $row;
    $total_units += $row['units'];
    
    // Only count towards GPA if grade exists and is valid
    if ($row['grade_point'] && $row['grade'] && $row['grade'] != 'INC' && $row['grade'] != 'W') {
        $total_points += $row['grade_point'] * $row['units'];
        $has_grades = true;
    }
}

$gpa = ($has_grades && $total_units > 0) ? number_format($total_points / $total_units, 2) : 'N/A';

function getGradeColor($grade) {
    if (!$grade || $grade == '') return '#9ca3af';
    if ($grade == 'A' || $grade == 'A-') return '#10b981';
    if ($grade == 'B+' || $grade == 'B' || $grade == 'B-') return '#3b82f6';
    if ($grade == 'C+' || $grade == 'C' || $grade == 'C-') return '#f59e0b';
    if ($grade == 'D') return '#8b5cf6';
    if ($grade == 'F') return '#ef4444';
    return '#6b7280';
}

function getGradeStatus($grade) {
    if (!$grade || $grade == '') return 'Pending';
    if ($grade == 'F') return 'Failed';
    if ($grade == 'INC') return 'Incomplete';
    if ($grade == 'W') return 'Withdrawn';
    return 'Passed';
}

// Calculate completed units (with valid grades)
$completed_units = 0;
foreach ($grades as $grade) {
    if ($grade['grade'] && $grade['grade'] != 'INC' && $grade['grade'] != 'W') {
        $completed_units += $grade['units'];
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
    }
    body {
      margin: 0; min-height: 100vh;
      background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%);
      color: var(--text);
    }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }

    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    /* Sidebar */
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

    /* Main */
    .main-area { padding: 28px 32px; }

    /* Topbar */
    .topbar {
      display: flex; flex-wrap: wrap; justify-content: space-between;
      align-items: center; gap: 24px; margin-bottom: 28px;
    }
    .user-greeting { margin: 0; color: #475569; font-size: 14px; }
    .page-title { margin: 8px 0 0; font-size: clamp(26px,3vw,34px); line-height: 1.05; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .chatbot-btn {
      width: 52px; height: 52px; border-radius: 16px;
      border: 1px solid var(--border); background: #fff; color: #7c3aed;
      display: grid; place-items: center; font-size: 22px;
      transition: background .2s, transform .2s, box-shadow .2s;
      box-shadow: 0 2px 8px rgba(15,23,42,.04);
    }
    .chatbot-btn:hover { background: var(--accent-soft); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(91,33,182,.12); }
    .profile-card {
      display: flex; align-items: center; gap: 16px;
      padding: 14px 18px; background: #fff;
      border: 1px solid var(--border); border-radius: 18px;
    }
    .profile-avatar {
      width: 52px; height: 52px; border-radius: 16px;
      background: linear-gradient(135deg,#7c3aed,#2563eb);
      color: #fff; display: grid; place-items: center; font-weight: 800;
    }
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }

    /* Stat Cards */
    .stat-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 16px; margin-bottom: 28px;
    }
    .stat-card {
      border-radius: 18px; padding: 22px 24px;
      color: #fff; position: relative; overflow: hidden;
    }
    .stat-card::after {
      content: ''; position: absolute; right: -20px; bottom: -20px;
      width: 90px; height: 90px; border-radius: 50%;
      background: rgba(255,255,255,.1);
    }
    .stat-card.blue   { background: linear-gradient(135deg,#1e3a8a,#1d4ed8); }
    .stat-card.green  { background: linear-gradient(135deg,#065f46,#059669); }
    .stat-card.amber  { background: linear-gradient(135deg,#92400e,#d97706); }
    .stat-card.purple { background: linear-gradient(135deg,#4c1d95,#7c3aed); }
    .stat-label { font-size: 12px; font-weight: 600; opacity: .8; margin-bottom: 8px; }
    .stat-value { font-size: 36px; font-weight: 800; line-height: 1; margin-bottom: 6px; }
    .stat-sub { font-size: 12px; opacity: .75; }

    /* Table */
    .table-wrap { border-radius: 16px; overflow: hidden; border: 1px solid var(--border); margin-bottom: 24px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a8a; color: #fff; }
    thead th { padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 700; white-space: nowrap; }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }
    .course-name { font-weight: 700; font-size: 14px; }
    .units-val { color: #2563eb; font-weight: 700; }
    .prof-name { color: var(--text-muted); }

    /* Grade Badge */
    .grade-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 48px;
      padding: 8px 12px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 800;
      color: white;
    }
    .grade-na {
      background: #e5e7eb;
      color: #6b7280;
    }
    .grade-small {
      font-size: 11px;
      font-weight: 600;
      margin-top: 4px;
    }
    .status-pill {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }
    .status-passed { background: #d1fae5; color: #065f46; }
    .status-failed { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef3c7; color: #92400e; }

    /* Card */
    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 24px;
    }
    .card-header {
      display: flex; align-items: center; gap: 10px; margin-bottom: 20px;
    }
    .card-header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    .card-header-icon { font-size: 20px; }

    /* GPA Card */
    .gpa-card {
      background: linear-gradient(135deg, #eef2ff, #f5f3ff);
      border-radius: 20px;
      padding: 24px;
      text-align: center;
    }
    .gpa-value {
      font-size: 48px;
      font-weight: 800;
      color: var(--accent);
      line-height: 1;
    }
    .gpa-label {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 8px;
    }

    /* Responsive */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 12px; padding: 16px 20px; }
      .sidebar-nav { flex-direction: row; flex-wrap: wrap; }
      .sidebar-footer { margin-top: 0; }
      .stat-grid { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 14px; }
      .stat-grid { grid-template-columns: 1fr; }
      .table-wrap { overflow-x: auto; }
      table { min-width: 700px; }
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
      <a href="dashboard.php" class="nav-item"><span class="nav-item-icon">🏠</span>Dashboard</a>
      <a href="events.php" class="nav-item"><span class="nav-item-icon">📅</span>Events</a>
      <a href="SchoolSched.php" class="nav-item"><span class="nav-item-icon">📋</span>Schedule</a>
      <a href="grades.php" class="nav-item active"><span class="nav-item-icon">📝</span>Grades</a>
      <a href="digital-id.php" class="nav-item"><span class="nav-item-icon">🪪</span>Digital ID</a>
      <a href="account.php" class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <div>
        <p class="user-greeting">Welcome back, <?= htmlspecialchars($fullName) ?>!</p>
        <h1 class="page-title">My Grades</h1>
      </div>
      <div class="topbar-right">
        <button type="button" class="chatbot-btn" title="AI Assistant" onclick="alert('Chatbot coming soon!')">🤖</button>
        <div class="profile-card">
          <div class="profile-avatar" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden;">
            <?php if ($picPath): ?>
              <img src="<?= htmlspecialchars($picPath) ?>" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;" />
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

    <!-- Academic Statistics -->
    <div class="stat-grid">
      <div class="stat-card blue">
        <div class="stat-label">Current GPA</div>
        <div class="stat-value"><?= $gpa ?></div>
        <div class="stat-sub">
          <?php 
            if ($gpa !== 'N/A') {
                echo $gpa >= 3.0 ? 'Excellent Standing' : ($gpa >= 2.0 ? 'Good Standing' : 'Academic Attention');
            } else {
                echo 'No grades yet';
            }
          ?>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-label">Total Units</div>
        <div class="stat-value"><?= $total_units ?></div>
        <div class="stat-sub">Enrolled this semester</div>
      </div>
      <div class="stat-card amber">
        <div class="stat-label">Courses Taken</div>
        <div class="stat-value"><?= count($grades) ?></div>
        <div class="stat-sub">Current semester</div>
      </div>
      <div class="stat-card purple">
        <div class="stat-label">Year Level</div>
        <div class="stat-value" style="font-size: 28px;"><?= htmlspecialchars($user['year_level']) ?></div>
        <div class="stat-sub"><?= htmlspecialchars($user['course']) ?></div>
      </div>
    </div>

    <!-- Grade Report -->
    <div class="card">
      <div class="card-header">
        <span class="card-header-icon">📊</span>
        <h2>Grade Report - 1st Semester, AY 2025-2026</h2>
      </div>

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
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($grades)): ?>
              <tr>
                <td colspan="7" style="text-align: center; padding: 60px;">
                  <p>No courses found in your schedule.</p>
                  <p style="color: var(--text-muted);">Please contact the registrar if you believe this is an error.</p>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($grades as $grade): ?>
                <?php
                  $status = getGradeStatus($grade['grade']);
                  $statusClass = $status == 'Passed' ? 'status-passed' : ($status == 'Failed' ? 'status-failed' : 'status-pending');
                  $gradeColor = getGradeColor($grade['grade']);
                  $hasGrade = !empty($grade['grade']) && $grade['grade'] != '';
                  $schedule_info = '';
                  if (!empty($grade['day']) && !empty($grade['time'])) {
                      $schedule_info = htmlspecialchars($grade['day']) . ', ' . htmlspecialchars($grade['time']);
                      if (!empty($grade['room'])) {
                          $schedule_info .= ' • ' . htmlspecialchars($grade['room']);
                      }
                  } else {
                      $schedule_info = 'Schedule TBA';
                  }
                ?>
                <tr>
                  <td class="course-name"><?= htmlspecialchars($grade['course_code']) ?></td>
                  <td>
                    <?= htmlspecialchars($grade['course_name']) ?>
                    <div class="grade-small" style="color: var(--text-muted);"></div>
                  </td>
                  <td class="units-val"><?= $grade['units'] ?></td>
                  <td class="prof-name"><?= $schedule_info ?></td>
                  <td class="prof-name"><?= htmlspecialchars($grade['professor'] ?? 'TBA') ?></td>
                  <td>
                    <?php if ($hasGrade): ?>
                      <div class="grade-badge" style="background: <?= $gradeColor ?>;">
                        <?= htmlspecialchars($grade['grade']) ?>
                      </div>
                      <?php if ($grade['grade_point']): ?>
                        <div class="grade-small">(<?= number_format($grade['grade_point'], 2) ?>)</div>
                      <?php endif; ?>
                    <?php else: ?>
                      <div class="grade-badge grade-na">
                        N/A
                      </div>
                      <div class="grade-small">Not yet graded</div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="status-pill <?= $statusClass ?>"><?= $status ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
          <tfoot>
            <tr style="background: #f8fafc; font-weight: 700;">
              <td colspan="2" style="text-align: right;">Totals:</td>
              <td class="units-val"><?= $total_units ?></td>
              <td colspan="4"></td>
            </tr>
            <?php if ($has_grades): ?>
            <tr style="background: #f8fafc;">
              <td colspan="2" style="text-align: right;">GPA (Grade Point Average):</td>
              <td colspan="5">
                <span style="font-size: 24px; font-weight: 800; color: #4c1d95;"><?= $gpa ?></span>
                <span style="margin-left: 12px; font-size: 13px; color: var(--text-muted);">
                  <?php
                    if ($gpa >= 3.5) echo '🏆 President\'s Lister';
                    elseif ($gpa >= 3.0) echo '⭐ Dean\'s Lister';
                    elseif ($gpa >= 2.5) echo '✅ Good Standing';
                    elseif ($gpa >= 2.0) echo '⚠️ Satisfactory';
                    else echo '📚 Needs Improvement';
                  ?>
                </span>
              </td>
            </tr>
            <?php endif; ?>
          </tfoot>
        </table>
      </div>

      <!-- Grading Scale -->
      <div style="margin-top: 24px;">
        <h3 style="font-size: 14px; margin-bottom: 12px;">Grading Scale</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px;">
          <div style="padding: 8px; background: #f0fdf4; border-radius: 8px; text-align: center;"><strong style="color: #16a34a;">A</strong><br><small>4.00</small></div>
          <div style="padding: 8px; background: #eff6ff; border-radius: 8px; text-align: center;"><strong style="color: #2563eb;">B+ / B / B-</strong><br><small>3.30 - 2.70</small></div>
          <div style="padding: 8px; background: #fffbeb; border-radius: 8px; text-align: center;"><strong style="color: #d97706;">C+ / C / C-</strong><br><small>2.30 - 1.70</small></div>
          <div style="padding: 8px; background: #fef2f2; border-radius: 8px; text-align: center;"><strong style="color: #dc2626;">D / F</strong><br><small>1.00 / 0.00</small></div>
        </div>
        <p style="font-size: 11px; color: var(--text-muted); margin-top: 12px; text-align: center;">
          * Grades marked as "N/A" are still pending. Please check back later or contact your instructor.
        </p>
      </div>
    </div>
  </main>
</div>
</body>
</html>