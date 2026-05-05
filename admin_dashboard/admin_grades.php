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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Grades</title>
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

    /* ── App Shell ── */
    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    /* ── Sidebar ── */
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
      background: rgba(255,255,255,.12); display: grid; place-items: center; font-size: 22px;
    }
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

    /* ── Stat Cards ── */
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

    /* ── Grade Report Header ── */
    .report-header {
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 16px; margin-bottom: 18px;
    }
    .report-header h2 { margin: 0; font-size: 22px; font-weight: 800; }

    /* ── Tabs ── */
    .view-tabs { display: flex; gap: 4px; background: var(--surface-strong); border-radius: 14px; padding: 4px; }
    .view-tab {
      padding: 9px 18px; border-radius: 10px; border: none;
      background: transparent; font-size: 13px; font-weight: 600; color: var(--text-muted);
      transition: all .2s; display: flex; align-items: center; gap: 6px;
    }
    .view-tab.active { background: #fff; color: var(--text); box-shadow: 0 2px 8px rgba(15,23,42,.08); }
    .view-tab:hover:not(.active) { color: var(--text); }

    /* Semester selector */
    .semester-select {
      padding: 10px 14px; border-radius: 12px; border: 1.5px solid var(--border);
      background: #fff; font-family: inherit; font-size: 13px; font-weight: 600;
      color: var(--text); cursor: pointer; outline: none;
    }
    .semester-select:focus { border-color: #7c3aed; }

    /* ── Table ── */
    .table-wrap { border-radius: 16px; overflow: hidden; border: 1px solid var(--border); margin-bottom: 24px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a8a; color: #fff; }
    thead th { padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 700; white-space: nowrap; }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    .tfoot-row { background: #f8fafc; font-weight: 700; }
    td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }
    .course-name { font-weight: 700; font-size: 14px; }
    .course-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .units-val { color: #2563eb; font-weight: 700; font-size: 15px; }
    .totals-label { font-size: 14px; color: #475569; }
    .totals-gpa { color: #1e40af; font-size: 18px; font-weight: 800; }

    /* Grade badge */
    .grade-pill {
      width: 38px; height: 38px; border-radius: 50%;
      display: grid; place-items: center;
      font-size: 13px; font-weight: 800; color: #fff;
    }
    .grade-A   { background: #16a34a; }
    .grade-Bp  { background: #2563eb; }
    .grade-B   { background: #0891b2; }
    .grade-Cp  { background: #7c3aed; }
    .grade-C   { background: #d97706; }
    .grade-D   { background: #ea580c; }
    .grade-F   { background: #dc2626; }

    /* Status badge */
    .status-pill {
      padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .status-passed { background: #dcfce7; color: #15803d; }
    .status-failed { background: #fee2e2; color: #b91c1c; }
    .status-inc    { background: #fef3c7; color: #92400e; }

    /* ── Grading Scale ── */
    .scale-title { font-size: 16px; font-weight: 700; margin: 0 0 14px; }
    .scale-grid {
      display: grid; grid-template-columns: repeat(8, 1fr); gap: 8px;
    }
    .scale-item {
      border-radius: 12px; padding: 12px 8px; text-align: center;
      border: 1px solid var(--border);
    }
    .scale-letter { font-size: 20px; font-weight: 800; }
    .scale-range  { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
    .scale-desc   { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
    .scale-A  { background: #f0fdf4; } .scale-A .scale-letter  { color: #16a34a; }
    .scale-Bp { background: #eff6ff; } .scale-Bp .scale-letter { color: #2563eb; }
    .scale-B  { background: #ecfeff; } .scale-B .scale-letter  { color: #0891b2; }
    .scale-Cp { background: #f5f3ff; } .scale-Cp .scale-letter { color: #7c3aed; }
    .scale-C  { background: #fffbeb; } .scale-C .scale-letter  { color: #d97706; }
    .scale-D  { background: #fff7ed; } .scale-D .scale-letter  { color: #ea580c; }
    .scale-F  { background: #fef2f2; } .scale-F .scale-letter  { color: #dc2626; }
    .scale-INC { background: #f8fafc; } .scale-INC .scale-letter { color: #64748b; }

    /* ── Action Buttons ── */
    .action-bar {
      display: grid; grid-template-columns: 1fr 1fr 1fr;
      gap: 12px; margin-top: 24px;
    }
    .action-btn {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 15px; border-radius: 14px; font-size: 14px; font-weight: 700;
      border: none; transition: transform .2s, box-shadow .2s;
    }
    .action-btn:hover { transform: translateY(-2px); }
    .btn-pdf {
      background: linear-gradient(135deg,#d97706,#b45309); color: #fff;
      box-shadow: 0 4px 14px rgba(180,83,9,.25);
    }
    .btn-pdf:hover { box-shadow: 0 8px 20px rgba(180,83,9,.35); }
    .btn-email {
      background: #fff; color: var(--text);
      border: 1.5px solid var(--border) !important;
    }
    .btn-email:hover { border-color: #7c3aed !important; color: #7c3aed; }
    .btn-past {
      background: #fff; color: var(--text);
      border: 1.5px solid var(--border) !important;
    }
    .btn-past:hover { border-color: #7c3aed !important; color: #7c3aed; }

    /* ── Weekly View Unavailable ── */
    .weekly-unavailable {
      background: #fff; border: 1.5px dashed #cbd5e1;
      border-radius: 20px; padding: 48px 24px;
      text-align: center; display: none;
    }
    .weekly-unavailable.show { display: block; }
    .weekly-icon { font-size: 48px; margin-bottom: 16px; }
    .weekly-unavailable h3 { margin: 0 0 10px; font-size: 20px; }
    .weekly-unavailable p { margin: 0; color: var(--text-muted); font-size: 14px; line-height: 1.7; }
    .coming-soon-badge {
      display: inline-block; margin-top: 16px;
      padding: 6px 16px; border-radius: 20px;
      background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700;
    }

    /* ── Card wrapper ── */
    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 24px;
    }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 12px; padding: 16px 20px; }
      .sidebar-nav { flex-direction: row; flex-wrap: wrap; }
      .sidebar-footer { margin-top: 0; }
      .stat-grid { grid-template-columns: repeat(2,1fr); }
      .scale-grid { grid-template-columns: repeat(4,1fr); }
      .action-bar { grid-template-columns: 1fr; }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 14px; }
      .stat-grid { grid-template-columns: 1fr 1fr; }
      .scale-grid { grid-template-columns: repeat(4,1fr); }
      .table-wrap { overflow-x: auto; }
      table { min-width: 700px; }
    }
    .nav-logo img { width: 100%; height: 100%; object-fit: cover; }
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
       <a href="admin_dashboard.php"    class="nav-item"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php"       class="nav-item"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php" class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item active"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"    class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <div>
        <p class="user-greeting">Welcome back, Student!</p>
        <h1 class="page-title">Grades</h1>
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

    <!-- Stat Cards -->
    <div class="stat-grid">
      <div class="stat-card blue">
        <div class="stat-label">Current GPA</div>
        <div class="stat-value">1.50</div>
        <div class="stat-sub">Grade Point Average</div>
      </div>
      <div class="stat-card green">
        <div class="stat-label">Total Units</div>
        <div class="stat-value">16</div>
        <div class="stat-sub">Units Completed</div>
      </div>
      <div class="stat-card amber">
        <div class="stat-label">Subjects</div>
        <div class="stat-value">6</div>
        <div class="stat-sub">Total Enrolled</div>
      </div>
      <div class="stat-card purple">
        <div class="stat-label">Status</div>
        <div class="stat-value" style="font-size:26px;">All Passed</div>
        <div class="stat-sub">Semester Status</div>
      </div>
    </div>

    <!-- Grade Report -->
    <div class="card">
      <div class="report-header">
        <h2>Grade Report</h2>
        <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
          <!-- View toggle: Table | Weekly -->
          <div class="view-tabs">
          </div>
          <select class="semester-select" id="semesterSelect" onchange="changeSemester(this.value)">
            <option value="1st-2526">1st Semester 2025-2026</option>
            <option value="2nd-2526">2nd Semester 2025-2026</option>
          </select>
        </div>
      </div>

      <!-- Table View -->
      <div id="tableView">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Course</th>
                <th>Units</th>
                <th>Schedule</th>
                <th>Grade</th>
                <th>Numeric</th>
                <th>Status</th>
                <th>Professor</th>
              </tr>
            </thead>
            <tbody id="gradesBody"></tbody>
            <tfoot>
              <tr class="tfoot-row">
                <td><span class="totals-label">Semester Totals</span></td>
                <td><span class="units-val" id="totalUnits">16</span></td>
                <td></td>
                <td></td>
                <td colspan="2"><span class="totals-label">Semester GPA:</span></td>
                <td><span class="totals-gpa" id="semesterGpa">1.50</span></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Weekly View Unavailable -->
      <div class="weekly-unavailable" id="weeklyView">
        <div class="weekly-icon">🗓️</div>
        <h3>Weekly View Coming Soon</h3>
        <p>The weekly schedule view is currently under development.<br>Please use the Table View to check your grades for now.</p>
        <span class="coming-soon-badge">🚧 Under Development</span>
      </div>

      <!-- Grading Scale -->
      <div style="margin-top:24px;">
        <p class="scale-title">QCU Grading Scale</p>
        <div class="scale-grid">
          <div class="scale-item scale-A">
            <div class="scale-letter">A</div>
            <div class="scale-range">1.00 – 1.25</div>
            <div class="scale-desc">Excellent</div>
          </div>
          <div class="scale-item scale-Bp">
            <div class="scale-letter">B+</div>
            <div class="scale-range">1.50 – 1.75</div>
            <div class="scale-desc">Very Good</div>
          </div>
          <div class="scale-item scale-B">
            <div class="scale-letter">B</div>
            <div class="scale-range">2.00 – 2.25</div>
            <div class="scale-desc">Good</div>
          </div>
          <div class="scale-item scale-Cp">
            <div class="scale-letter">C+</div>
            <div class="scale-range">2.50 – 2.75</div>
            <div class="scale-desc">Satisfactory</div>
          </div>
          <div class="scale-item scale-C">
            <div class="scale-letter">C</div>
            <div class="scale-range">3.00</div>
            <div class="scale-desc">Passing</div>
          </div>
          <div class="scale-item scale-D">
            <div class="scale-letter">D</div>
            <div class="scale-range">4.00</div>
            <div class="scale-desc">Conditional</div>
          </div>
          <div class="scale-item scale-F">
            <div class="scale-letter">F</div>
            <div class="scale-range">5.00</div>
            <div class="scale-desc">Failed</div>
          </div>
          <div class="scale-item scale-INC">
            <div class="scale-letter">INC</div>
            <div class="scale-range">—</div>
            <div class="scale-desc">Incomplete</div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="action-bar">
        <button type="button" class="action-btn btn-pdf">📄 Download Grade Report (PDF)</button>
        <button type="button" class="action-btn btn-email" style="border:1.5px solid var(--border);">✉️ Email Grade Report</button>
        <button type="button" class="action-btn btn-past"  style="border:1.5px solid var(--border);">🕐 View Past Semesters</button>
      </div>
    </div>
  </main>
</div>

<script>
  /* ── Grade data per semester ── */
  const semesters = {
    "1st-2526": {
      gpa: "1.50",
      courses: [
        { code:"CS 101",   name:"Introduction to Computer Science",  units:3, sched:"MWF 9:00 AM – 10:30 AM",  grade:"A",  numeric:"1.25", status:"Passed",  prof:"Dr. Maria Santos" },
        { code:"MATH 201", name:"Calculus II",                        units:3, sched:"TTh 10:45 AM – 12:15 PM", grade:"B+", numeric:"1.75", status:"Passed",  prof:"Prof. Juan Dela Cruz" },
        { code:"ENG 103",  name:"Technical Writing",                  units:3, sched:"MWF 1:00 PM – 2:30 PM",  grade:"A",  numeric:"1.25", status:"Passed",  prof:"Dr. Ana Reyes" },
        { code:"PHYS 101", name:"Physics for Engineers",              units:4, sched:"TTh 2:45 PM – 4:45 PM",  grade:"B",  numeric:"2.00", status:"Passed",  prof:"Prof. Roberto Garcia" },
        { code:"PE 101",   name:"Physical Education – Fitness",       units:2, sched:"Sat 8:00 AM – 10:00 AM", grade:"A",  numeric:"1.00", status:"Passed",  prof:"Coach Linda Fernandez" },
        { code:"CS 102L",  name:"Computer Programming Lab",           units:1, sched:"F 3:00 PM – 5:00 PM",    grade:"A",  numeric:"1.25", status:"Passed",  prof:"Engr. Mark Villanueva" },
      ]
    },
    "2nd-2526": {
      gpa: "1.75",
      courses: [
        { code:"CS 201",   name:"Data Structures",                   units:3, sched:"MWF 8:00 AM – 9:30 AM",   grade:"A",  numeric:"1.25", status:"Passed", prof:"Dr. Maria Santos" },
        { code:"MATH 301", name:"Differential Equations",            units:3, sched:"TTh 9:00 AM – 10:30 AM",  grade:"B+", numeric:"1.75", status:"Passed", prof:"Prof. Juan Dela Cruz" },
        { code:"CS 203",   name:"Computer Organization",             units:3, sched:"MWF 1:00 PM – 2:30 PM",   grade:"B",  numeric:"2.00", status:"Passed", prof:"Engr. Mark Villanueva" },
        { code:"ENG 201",  name:"Speech Communication",              units:3, sched:"TTh 2:00 PM – 3:30 PM",   grade:"A",  numeric:"1.25", status:"Passed", prof:"Dr. Ana Reyes" },
        { code:"NSTP 101", name:"National Service Training Program",  units:3, sched:"Sat 7:00 AM – 10:00 AM", grade:"A",  numeric:"1.00", status:"Passed", prof:"Lt. Jose Bautista" },
      ]
    },
  };

  const gradeClasses = { A:"grade-A", "B+":"grade-Bp", B:"grade-B", "C+":"grade-Cp", C:"grade-C", D:"grade-D", F:"grade-F" };

  function renderGrades(semKey) {
    const data = semesters[semKey];
    const body = document.getElementById("gradesBody");
    let totalUnits = 0;
    body.innerHTML = data.courses.map(c => {
      totalUnits += c.units;
      const gc = gradeClasses[c.grade] || "grade-A";
      return `
        <tr>
          <td>
            <div class="course-name">${c.code}</div>
            <div class="course-sub">${c.name}</div>
          </td>
          <td><span class="units-val">${c.units}</span></td>
          <td style="color:var(--text-muted);font-size:12px;">${c.sched}</td>
          <td><span class="grade-pill ${gc}">${c.grade}</span></td>
          <td style="font-weight:600;">${c.numeric}</td>
          <td><span class="status-pill status-${c.status.toLowerCase()}">${c.status}</span></td>
          <td style="color:var(--text-muted);font-size:12px;">${c.prof}</td>
        </tr>`;
    }).join("");
    document.getElementById("totalUnits").textContent = totalUnits;
    document.getElementById("semesterGpa").textContent = data.gpa;
  }

  function changeSemester(val) { renderGrades(val); }

  function switchView(view) {
    const tableView  = document.getElementById("tableView");
    const weeklyView = document.getElementById("weeklyView");
    const tabTable   = document.getElementById("tabTable");
    const tabWeekly  = document.getElementById("tabWeekly");
    if (view === "table") {
      tableView.style.display = "block";
      weeklyView.classList.remove("show");
      tabTable.classList.add("active");
      tabWeekly.classList.remove("active");
    } else {
      tableView.style.display = "none";
      weeklyView.classList.add("show");
      tabTable.classList.remove("active");
      tabWeekly.classList.add("active");
    }
  }

  /* ── Init ── */
  renderGrades("1st-2526");
</script>
</body>
</html>