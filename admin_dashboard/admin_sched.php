<?php
session_start();
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Fetch admin data from DB
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

// Handle delete schedule
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM schedules WHERE id = $id");
    $_SESSION['message'] = "Schedule deleted successfully!";
    header('Location: admin_sched.php');
    exit;
}

// Handle edit schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_schedule'])) {
    $id = (int)$_POST['schedule_id'];
    $courseCode = trim($_POST['course_code'] ?? '');
    $courseName = trim($_POST['course_name'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $units = (int)($_POST['units'] ?? 0);
    $day = trim($_POST['day'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $room = trim($_POST['room'] ?? '');
    $professor = trim($_POST['professor'] ?? '');

    if ($courseCode && $courseName && $section && $units > 0 && $day && $time && $room && $professor) {
        $stmt = mysqli_prepare($conn, "UPDATE schedules SET 
            course_code = ?, 
            course_name = ?, 
            section = ?, 
            units = ?, 
            day = ?, 
            time = ?, 
            room = ?, 
            professor = ? 
            WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssiisssi", $courseCode, $courseName, $section, $units, $day, $time, $room, $professor, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Schedule updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update schedule.";
        }
        mysqli_stmt_close($stmt);
        header('Location: admin_sched.php');
        exit;
    }
}

// Handle add schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $courseCode = trim($_POST['course_code'] ?? '');
    $courseName = trim($_POST['course_name'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $units = (int)($_POST['units'] ?? 0);
    $day = trim($_POST['day'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $room = trim($_POST['room'] ?? '');
    $professor = trim($_POST['professor'] ?? '');

    if ($courseCode && $courseName && $section && $units > 0 && $day && $time && $room && $professor) {
        $stmt = mysqli_prepare($conn, "INSERT INTO schedules (course_code, course_name, section, units, day, time, room, professor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssiisss", $courseCode, $courseName, $section, $units, $day, $time, $room, $professor);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Schedule added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add schedule.";
        }
        mysqli_stmt_close($stmt);
        header('Location: admin_sched.php');
        exit;
    }
}

// Fetch schedules grouped by section
$schedules = [];
$result = mysqli_query($conn, "SELECT * FROM schedules ORDER BY section, course_code");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $schedules[$row['section']][] = $row;
    }
}

// Calculate total units per section
$sectionTotals = [];
foreach ($schedules as $section => $courses) {
    $sectionTotals[$section] = array_reduce($courses, fn($sum, $course) => $sum + $course['units'], 0);
}

// Get course data for editing
$edit_course = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM schedules WHERE id = $edit_id");
    $edit_course = mysqli_fetch_assoc($edit_query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Admin — Manage Schedules</title>
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
      --error: #ef4444;
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

    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 24px;
    }

    .section-header {
      display: flex; justify-content: space-between;
      align-items: center; margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 16px;
    }
    .section-header h3 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
    }

    .table-wrap {
      border-radius: 16px;
      overflow-x: auto;
      border: 1px solid var(--border);
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
    }
    thead tr {
      background: #1e3a8a;
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
    td { padding: 14px 16px; font-size: 13px; vertical-align: middle; }

    .course-code { color: #1d4ed8; font-weight: 700; font-size: 14px; }
    .units-val { color: #d97706; font-weight: 700; }

    .action-buttons {
      display: flex;
      gap: 8px;
    }
    .btn-edit {
      padding: 6px 12px;
      background: #3b82f6;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    .btn-edit:hover {
      background: #2563eb;
      transform: translateY(-1px);
    }
    .btn-delete {
      padding: 6px 12px;
      background: #ef4444;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-delete:hover {
      background: #dc2626;
      transform: translateY(-1px);
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--accent), #6d28d9);
      color: #fff;
      padding: 12px 24px;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      transition: transform .2s, box-shadow .2s;
      cursor: pointer;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 14px rgba(91,33,182,.25);
    }
    .btn-secondary {
      background: #fff;
      color: var(--text);
      border: 1.5px solid var(--border);
      padding: 12px 24px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      transition: all .2s;
      cursor: pointer;
    }
    .btn-secondary:hover {
      border-color: var(--accent);
      color: var(--accent);
    }

    /* Centered Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15,23,42,.6);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    .modal-overlay.show {
      display: flex;
    }
    .modal-dialog {
      background: var(--surface);
      border-radius: 20px;
      padding: 28px;
      max-width: 600px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(15,23,42,.2);
      margin: auto;
    }
    .modal-title {
      margin: 0 0 20px;
      font-size: 20px;
      font-weight: 800;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .modal-close {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: var(--text-muted);
    }
    .form-group {
      margin-bottom: 18px;
    }
    .form-label {
      display: block;
      margin-bottom: 8px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
    }
    .form-input, .form-select {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      font-size: 13px;
      font-family: inherit;
    }
    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: #7c3aed;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .action-bar {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      justify-content: flex-end;
    }

    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .stat-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 14px; }
      .stat-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
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
    <nav class="sidebar-nav">
      <a href="admin_dashboard.php" class="nav-item"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php" class="nav-item"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php" class="nav-item active"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php" class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php" class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
      <button class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <h1 class="page-title">Manage Class Schedules</h1>
      <div class="topbar-right">
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

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($_SESSION['message']) ?></div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-error">❌ <?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-grid">
      <div class="stat-card blue">
        <div class="stat-info">
          <span class="stat-label">Sections</span>
          <span class="stat-value"><?= count($schedules) ?></span>
        </div>
        <div class="stat-icon">📚</div>
      </div>
      <div class="stat-card amber">
        <div class="stat-info">
          <span class="stat-label">Total Courses</span>
          <span class="stat-value"><?= array_reduce($schedules, fn($sum, $s) => $sum + count($s), 0) ?></span>
        </div>
        <div class="stat-icon">📋</div>
      </div>
      <div class="stat-card green">
        <div class="stat-info">
          <span class="stat-label">Schedule Manager</span>
          <span class="stat-value" style="font-size: 24px;">Add / Edit / Delete</span>
        </div>
        <div class="stat-icon">⚙️</div>
      </div>
    </div>

    <!-- Schedules Display -->
    <div class="card">
      <div class="section-header">
        <h2>📅 Class Schedules by Section</h2>
        <button class="btn-primary" onclick="openAddModal()">➕ Add New Schedule</button>
      </div>

      <?php if (empty($schedules)): ?>
        <div style="text-align: center; padding: 60px 20px;">
          <p style="margin-bottom: 20px;">No schedules added yet.</p>
          <button class="btn-primary" onclick="openAddModal()">➕ Add First Schedule</button>
        </div>
      <?php else: ?>
        <?php foreach ($schedules as $section => $courses): ?>
          <div style="margin-bottom: 40px;">
            <div class="section-header">
              <h3>📌 Section: <?= htmlspecialchars($section) ?></h3>
              <button class="btn-primary" style="padding: 8px 16px; font-size: 13px;" onclick="openAddModal('<?= htmlspecialchars($section) ?>')">+ Add Course to <?= htmlspecialchars($section) ?></button>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Units</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Room</th>
                    <th>Instructor</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($courses as $course): ?>
                    <tr>
                      <td class="course-code"><?= htmlspecialchars($course['course_code']) ?></td>
                      <td><?= htmlspecialchars($course['course_name']) ?></td>
                      <td class="units-val"><?= $course['units'] ?></td>
                      <td><?= htmlspecialchars($course['day']) ?></td>
                      <td><?= htmlspecialchars($course['time']) ?></td>
                      <td><?= htmlspecialchars($course['room']) ?></td>
                      <td><?= htmlspecialchars($course['professor']) ?></td>
                      <td class="action-buttons">
                        <a href="?edit=<?= $course['id'] ?>" class="btn-edit">✏️ Edit</a>
                        <a href="?delete=<?= $course['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this course?')">🗑 Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot style="background: #f8fafc; font-weight: 700;">
                  <tr>
                    <td colspan="2" style="text-align: right;">Total Units:</td>
                    <td class="units-val"><?= $sectionTotals[$section] ?? 0 ?></td>
                    <td colspan="5"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

<!-- Centered Add/Edit Schedule Modal -->
<div class="modal-overlay" id="scheduleModal">
  <div class="modal-dialog">
    <div class="modal-title">
      <span id="modalTitle">Add New Schedule</span>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <form method="POST" action="admin_sched.php" id="scheduleForm">
      <input type="hidden" name="schedule_id" id="schedule_id" value="">
      
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Course Code *</label>
          <input type="text" name="course_code" id="course_code" class="form-input" placeholder="e.g., CS 101" required>
        </div>
        <div class="form-group">
          <label class="form-label">Course Name *</label>
          <input type="text" name="course_name" id="course_name" class="form-input" placeholder="e.g., Introduction to CS" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Section *</label>
          <select name="section" id="section" class="form-select" required>
            <option value="">Select section...</option>
            <option value="SBIT-1A">SBIT-1A</option>
            <option value="SBIT-1B">SBIT-1B</option>
            <option value="SBIT-1C">SBIT-1C</option>
            <option value="SBIT-1D">SBIT-1D</option>
            <option value="SBIT-1E">SBIT-1E</option>
            <option value="SBIT-1F">SBIT-1F</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Units *</label>
          <input type="number" name="units" id="units" class="form-input" min="1" max="5" placeholder="1-5" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Day/Schedule *</label>
          <input type="text" name="day" id="day" class="form-input" placeholder="e.g., Monday, Wednesday, Friday" required>
        </div>
        <div class="form-group">
          <label class="form-label">Time *</label>
          <input type="text" name="time" id="time" class="form-input" placeholder="e.g., 9:00 AM - 10:30 AM" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Room/Location *</label>
          <input type="text" name="room" id="room" class="form-input" placeholder="e.g., Room 301, Engineering Bldg" required>
        </div>
        <div class="form-group">
          <label class="form-label">Instructor/Professor *</label>
          <input type="text" name="professor" id="professor" class="form-input" placeholder="e.g., Dr. Maria Santos" required>
        </div>
      </div>

      <div class="action-bar">
        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" name="add_schedule" id="submitBtn" class="btn-primary">✅ Add Schedule</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openAddModal(section = '') {
    document.getElementById('modalTitle').innerText = 'Add New Schedule';
    document.getElementById('submitBtn').name = 'add_schedule';
    document.getElementById('scheduleForm').reset();
    document.getElementById('schedule_id').value = '';
    if (section) {
      document.getElementById('section').value = section;
    }
    document.getElementById('scheduleModal').classList.add('show');
  }

  function openEditModal(id, courseCode, courseName, section, units, day, time, room, professor) {
    document.getElementById('modalTitle').innerText = 'Edit Schedule';
    document.getElementById('submitBtn').name = 'edit_schedule';
    document.getElementById('schedule_id').value = id;
    document.getElementById('course_code').value = courseCode;
    document.getElementById('course_name').value = courseName;
    document.getElementById('section').value = section;
    document.getElementById('units').value = units;
    document.getElementById('day').value = day;
    document.getElementById('time').value = time;
    document.getElementById('room').value = room;
    document.getElementById('professor').value = professor;
    document.getElementById('scheduleModal').classList.add('show');
  }

  function closeModal() {
    document.getElementById('scheduleModal').classList.remove('show');
  }

  // Close modal when clicking outside
  document.getElementById('scheduleModal').addEventListener('click', function(event) {
    if (event.target === this) {
      closeModal();
    }
  });

  // Check if we need to open edit modal from URL parameter
  <?php if ($edit_course): ?>
  window.addEventListener('load', function() {
    openEditModal(
      <?= $edit_course['id'] ?>,
      '<?= addslashes($edit_course['course_code']) ?>',
      '<?= addslashes($edit_course['course_name']) ?>',
      '<?= addslashes($edit_course['section']) ?>',
      <?= $edit_course['units'] ?>,
      '<?= addslashes($edit_course['day']) ?>',
      '<?= addslashes($edit_course['time']) ?>',
      '<?= addslashes($edit_course['room']) ?>',
      '<?= addslashes($edit_course['professor']) ?>'
    );
  });
  <?php endif; ?>
</script>
</body>
</html>