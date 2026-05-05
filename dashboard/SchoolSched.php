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

// Fetch schedule from DB based on student's section
$scheduleRows = [];
$totalUnits   = 0;
$studentSection = $user['section'] ?? null;

if ($studentSection) {
    $sched_stmt = mysqli_prepare($conn, "SELECT * FROM schedules WHERE section = ? ORDER BY course_code");
    mysqli_stmt_bind_param($sched_stmt, 's', $studentSection);
    mysqli_stmt_execute($sched_stmt);
    $sched_result = mysqli_stmt_get_result($sched_stmt);
    while ($row = mysqli_fetch_assoc($sched_result)) {
        $scheduleRows[] = $row;
        $totalUnits += (int)$row['units'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Class Schedule</title>
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
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 16px; margin-bottom: 28px;
    }
    .stat-card {
      border-radius: 18px; padding: 24px 28px;
      color: #fff; position: relative; overflow: hidden;
      display: flex; justify-content: space-between; align-items: center;
    }
    .stat-card::after {
      content: ''; position: absolute; right: -20px; bottom: -20px;
      width: 100px; height: 100px; border-radius: 50%;
      background: rgba(255,255,255,.08);
    }
    .stat-card.blue   { background: linear-gradient(135deg,#1e3a8a,#1d4ed8); }
    .stat-card.amber  { background: linear-gradient(135deg,#92400e,#d97706); }
    .stat-card.green  { background: linear-gradient(135deg,#065f46,#059669); }
    .stat-info { display: flex; flex-direction: column; gap: 6px; }
    .stat-label { font-size: 12px; font-weight: 600; opacity: .8; }
    .stat-value { font-size: 38px; font-weight: 800; line-height: 1; }
    .stat-value.large-text { font-size: 24px; }
    .stat-icon {
      width: 48px; height: 48px; border-radius: 14px;
      background: rgba(255,255,255,.15);
      display: grid; place-items: center; font-size: 22px;
      flex-shrink: 0; z-index: 1;
    }

    /* ── Card ── */
    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 24px;
    }

    /* ── Schedule Header ── */
    .sched-header {
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 16px; margin-bottom: 20px;
    }
    .sched-header h2 { margin: 0; font-size: 22px; font-weight: 800; }

    /* ── View Tabs ── */
    .view-tabs {
      display: flex; gap: 4px; background: var(--surface-strong);
      border-radius: 12px; padding: 4px;
    }
    .view-tab {
      padding: 9px 18px; border-radius: 8px; border: none;
      font-size: 13px; font-weight: 600; color: var(--text-muted);
      background: transparent; display: flex; align-items: center; gap: 6px;
      transition: all .2s;
    }
    .view-tab.active {
      background: #1e3a8a; color: #fff;
      box-shadow: 0 2px 8px rgba(30,58,138,.25);
    }
    .view-tab:hover:not(.active) { color: var(--text); }

    /* ── Table ── */
    .table-wrap { border-radius: 16px; overflow: hidden; border: 1px solid var(--border); }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1e3a8a; color: #fff; }
    thead th { padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 700; white-space: nowrap; }
    tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }

    .course-code { color: #1d4ed8; font-weight: 700; font-size: 14px; }
    .course-name-cell { font-weight: 600; }
    .section-pill {
      display: inline-grid; place-items: center;
      width: 32px; height: 32px; border-radius: 50%;
      background: #e0e7ff; color: #3730a3;
      font-size: 12px; font-weight: 700;
    }
    .units-val { color: #d97706; font-weight: 700; font-size: 15px; }
    .day-cell { font-weight: 600; }
    .time-cell, .room-cell, .prof-cell { color: var(--text-muted); }

    /* Total row */
    .total-row { background: #f8fafc; }
    .total-row td { font-weight: 700; font-size: 14px; color: #475569; }
    .total-units-val { color: #1d4ed8; font-size: 18px; font-weight: 800; }

    /* ── Weekly View Unavailable ── */
    .weekly-unavailable {
      background: #fff; border: 1.5px dashed #cbd5e1;
      border-radius: 20px; padding: 60px 24px;
      text-align: center; display: none;
    }
    .weekly-unavailable.show { display: block; }
    .weekly-icon { font-size: 52px; margin-bottom: 16px; }
    .weekly-unavailable h3 { margin: 0 0 10px; font-size: 20px; font-weight: 800; }
    .weekly-unavailable p { margin: 0; color: var(--text-muted); font-size: 14px; line-height: 1.7; }
    .coming-soon-badge {
      display: inline-block; margin-top: 16px;
      padding: 6px 16px; border-radius: 20px;
      background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700;
    }

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
    .btn-share {
      background: #fff; color: var(--text);
      border: 1.5px solid var(--border) !important;
    }
    .btn-share:hover { border-color: #7c3aed !important; color: #7c3aed; }
    .btn-edit {
      background: #fff; color: var(--text);
      border: 1.5px solid var(--border) !important;
    }
    .btn-edit:hover { border-color: #7c3aed !important; color: #7c3aed; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .stat-grid { grid-template-columns: 1fr 1fr; }
      .action-bar { grid-template-columns: 1fr; }
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
      <a href="dashboard.php"  class="nav-item"><span class="nav-item-icon">🏠</span>Dashboard</a>
      <a href="events.php"     class="nav-item"><span class="nav-item-icon">📅</span>Events</a>
      <a href="SchoolSched.php" class="nav-item active"><span class="nav-item-icon">📋</span>Schedule</a>
      <a href="grades.php"     class="nav-item"><span class="nav-item-icon">📝</span>Grades</a>
      <a href="digital-id.php" class="nav-item"><span class="nav-item-icon">🪪</span>Digital ID</a>
      <a href="account.php"    class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'"> Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <h1 class="page-title">Class Schedule</h1>
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
        <div class="stat-info">
          <span class="stat-label">Total Units Enrolled</span>
          <span class="stat-value"><?= $totalUnits > 0 ? $totalUnits : '—' ?></span>
        </div>
        <div class="stat-icon">📚</div>
      </div>
      <div class="stat-card amber">
        <div class="stat-info">
          <span class="stat-label">Program</span>
          <span class="stat-value" style="font-size:15px; line-height:1.3;"><?= htmlspecialchars($user['course'] ?? 'Not Set') ?></span>
        </div>
        <div class="stat-icon">📋</div>
      </div>
      <div class="stat-card green">
        <div class="stat-info">
          <span class="stat-label">Section</span>
          <span class="stat-value large-text"><?= htmlspecialchars($studentSection ?? 'Not Assigned') ?></span>
        </div>
        <div class="stat-icon">🏷️</div>
      </div>
    </div>

    <!-- Schedule Card -->
    <div class="card">
      <div class="sched-header">
        <h2>My Schedule</h2>
        <div class="view-tabs">
          <button class="view-tab active" id="tabList"   onclick="switchView('list')">≡ List View</button>
          <button class="view-tab"        id="tabWeekly" onclick="switchView('weekly')">▦ Weekly View</button>
        </div>
      </div>

      <!-- List View -->
      <div id="listView">
        <div class="table-wrap">
          <?php if (!$studentSection): ?>
            <div style="padding: 48px 24px; text-align: center; color: #64748b;">
              <div style="font-size: 48px; margin-bottom: 12px;">🏷️</div>
              <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 800; color: #1f2937;">No Section Assigned</h3>
              <p style="margin: 0; font-size: 14px; line-height: 1.7;">
                You have not been assigned to a section yet.<br>
                Please wait for your admin to assign your section, or contact the Registrar's Office.
              </p>
            </div>
          <?php elseif (empty($scheduleRows)): ?>
            <div style="padding: 48px 24px; text-align: center; color: #64748b;">
              <div style="font-size: 48px; margin-bottom: 12px;">📋</div>
              <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 800; color: #1f2937;">No Schedule Yet for <?= htmlspecialchars($studentSection) ?></h3>
              <p style="margin: 0; font-size: 14px; line-height: 1.7;">
                Your Grades has not been uploaded yet.<br>
                Please check back later or contact your department.
              </p>
            </div>
          <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Section</th>
                <th>Units</th>
                <th>Day</th>
                <th>Time</th>
                <th>Room</th>
                <th>Instructor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($scheduleRows as $course): ?>
              <tr>
                <td><span class="course-code"><?= htmlspecialchars($course['course_code']) ?></span></td>
                <td class="course-name-cell"><?= htmlspecialchars($course['course_name']) ?></td>
                <td><span class="section-pill" style="font-size:<?= strlen($course['section']) > 2 ? '9px' : '12px' ?>;"><?= htmlspecialchars($course['section']) ?></span></td>
                <td><span class="units-val"><?= htmlspecialchars($course['units']) ?></span></td>
                <td class="day-cell"><?= htmlspecialchars($course['day']) ?></td>
                <td class="time-cell"><?= htmlspecialchars($course['time']) ?></td>
                <td class="room-cell"><?= htmlspecialchars($course['room']) ?></td>
                <td class="prof-cell"><?= htmlspecialchars($course['professor']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="total-row">
                <td colspan="3" style="text-align:right;color:#475569;">Total Units:</td>
                <td><span class="total-units-val"><?= $totalUnits ?></span></td>
                <td colspan="4"></td>
              </tr>
            </tfoot>
          </table>
          <?php endif; ?>
        </div>
      </div>

      <!-- Weekly View — Temporarily Unavailable -->
      <div class="weekly-unavailable" id="weeklyView">
        <div class="weekly-icon">🗓️</div>
        <h3>REGESTRATION FORM</h3>
        <p>The weekly schedule view is currently under development.<br>Please use the List View to see your class schedule for now.</p>
        <span class="coming-soon-badge">🚧 Under Development</span>
      </div>

      <!-- Action Buttons -->
      <div class="action-bar">
        <button type="button" class="action-btn btn-pdf">📄 Download Schedule (PDF)</button>
        <button type="button" class="action-btn btn-share" style="border:1.5px solid var(--border);">🔗 Share Schedule</button>
       
      </div>
    </div>
  </main>
</div>

<script>
  function switchView(view) {
    const listView   = document.getElementById("listView");
    const weeklyView = document.getElementById("weeklyView");
    const tabList    = document.getElementById("tabList");
    const tabWeekly  = document.getElementById("tabWeekly");

    if (view === "list") {
      listView.style.display = "block";
      weeklyView.classList.remove("show");
      tabList.classList.add("active");
      tabWeekly.classList.remove("active");
    } else {
      listView.style.display = "none";
      weeklyView.classList.add("show");
      tabList.classList.remove("active");
      tabWeekly.classList.add("active");
    }
  }
</script>
</body>
</html>