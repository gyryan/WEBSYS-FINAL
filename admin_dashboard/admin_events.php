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
  <title>QCU Student Portal — Events</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="icon" type="image/png" href="../images/QCU-logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    /* ── Base ── */
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
      --shadow: 0 20px 60px rgba(15,23,42,.07);
    }
    body {
      margin: 0;
      min-height: 100vh;
      background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%);
      color: var(--text);
    }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }

    /* ── App Shell ── */
    .app-shell {
      display: grid;
      grid-template-columns: 280px 1fr;
      min-height: 100vh;
    }

    /* ── Sidebar ── */
    .sidebar {
      background: black;
      color: #f8fafc;
      padding: 32px 24px;
      display: flex;
      flex-direction: column;
      gap: 32px;
      position: sticky;
      top: 0;
      height: 100vh;
    }
    .sidebar-brand { display: flex; align-items: center; gap: 14px; }
    .nav-logo {
      width: 58px; height: 58px;
      border-radius: 18px; overflow: hidden;
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.12);
      display: grid; place-items: center; font-size: 22px;
    }
    .nav-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-title { display: block; font-size: 16px; font-weight: 800; letter-spacing: .5px; }
    .brand-sub { display: block; color: rgba(248,250,252,.72); font-size: 12px; margin-top: 4px; }
    .sidebar-nav { display: grid; gap: 10px; }
    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 18px; border: none;
      background: transparent; color: #f8fafc;
      font-size: 14px; border-radius: 14px;
      transition: background .2s, transform .2s;
    }
    .nav-item-icon {
      width: 34px; height: 34px;
      display: grid; place-items: center;
      border-radius: 12px; background: rgba(255,255,255,.08); font-size: 16px;
    }
    .nav-item:hover, .nav-item.active { background: rgba(255,255,255,.08); transform: translateX(2px); }
    .nav-item:hover .nav-item-icon, .nav-item.active .nav-item-icon { background: rgba(255,255,255,.18); }
    .sidebar-footer { margin-top: auto; }
    .logout-button {
      width: 100%; padding: 14px 18px;
      border: 1px solid rgba(255,255,255,.18); border-radius: 14px;
      background: transparent; color: #f8fafc; font-size: 14px;
      transition: background .2s;
    }
    .logout-button:hover { background: rgba(255,255,255,.08); }

    /* ── Main ── */
    .main-area { padding: 28px 32px; }

    /* ── Topbar ── */
    .topbar {
      display: flex; flex-wrap: wrap;
      justify-content: space-between; align-items: center;
      gap: 24px; margin-bottom: 28px;
    }
    .user-greeting { margin: 0; color: #475569; font-size: 14px; }
    .page-title { margin: 8px 0 0; font-size: clamp(26px,3vw,34px); line-height: 1.05; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .chatbot-btn {
      width: 52px; height: 52px; border-radius: 16px;
      border: 1px solid var(--border); background: #fff;
      color: #7c3aed; display: grid; place-items: center;
      transition: background .2s, transform .2s, box-shadow .2s;
      box-shadow: 0 2px 8px rgba(15,23,42,.04); font-size: 22px;
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

    /* ── Section Header ── */
    .events-header {
      display: flex; justify-content: space-between; align-items: flex-start;
      flex-wrap: wrap; gap: 16px; margin-bottom: 28px;
    }
    .events-header h2 { margin: 0; font-size: 24px; font-weight: 800; }
    .events-header p { margin: 6px 0 0; color: var(--text-muted); font-size: 14px; line-height: 1.6; }

    .calendar-btn {
      display: flex; align-items: center; gap: 8px;
      padding: 13px 20px; border-radius: 14px;
      border: none; background: linear-gradient(135deg,#d97706,#b45309);
      color: #fff; font-size: 14px; font-weight: 700;
      white-space: nowrap; transition: transform .2s, box-shadow .2s;
      box-shadow: 0 4px 14px rgba(180,83,9,.25);
    }
    .calendar-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(180,83,9,.3); }

    /* ── Filter Tabs ── */
    .filter-tabs {
      display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;
    }
    .filter-tab {
      padding: 8px 18px; border-radius: 30px; font-size: 13px; font-weight: 600;
      border: 1.5px solid var(--border); background: #fff; color: var(--text-muted);
      transition: all .2s;
    }
    .filter-tab:hover { border-color: #7c3aed; color: #7c3aed; }
    .filter-tab.active { background: #7c3aed; border-color: #7c3aed; color: #fff; }

    /* ── Event Cards ── */
    .event-list { display: grid; gap: 20px; }
    .event-card {
      background: #fff; border-radius: 20px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 20px rgba(15,23,42,.04);
      display: grid; grid-template-columns: 200px 1fr auto;
      overflow: hidden; transition: box-shadow .25s, transform .25s;
    }
    .event-card:hover { box-shadow: 0 12px 40px rgba(15,23,42,.1); transform: translateY(-2px); }
    .event-img { width: 200px; height: 140px; object-fit: cover; display: block; background: #e2e8f0; }
    .event-img-placeholder {
      width: 200px; height: 140px;
      display: grid; place-items: center;
      background: linear-gradient(135deg,#e0e7ff,#ede9fe); font-size: 40px;
    }
    .event-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 10px; }
    .event-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .event-title { margin: 0; font-size: 18px; font-weight: 700; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .badge-academic   { background: #dbeafe; color: #1e40af; }
    .badge-university { background: #d1fae5; color: #065f46; }
    .badge-career     { background: #fef3c7; color: #92400e; }
    .badge-student    { background: #ede9fe; color: #5b21b6; }
    .badge-default    { background: #f1f5f9; color: #475569; }
    .event-meta { display: flex; flex-wrap: wrap; gap: 14px; font-size: 13px; color: var(--text-muted); }
    .event-meta span { display: flex; align-items: center; gap: 5px; }
    .event-desc { margin: 0; font-size: 14px; color: #475569; line-height: 1.6; }
    .event-action { padding: 20px 20px 20px 0; display: flex; align-items: center; }
    .view-btn {
      display: flex; align-items: center; gap: 8px;
      padding: 12px 20px; border-radius: 12px; border: none;
      background: linear-gradient(135deg,#1e3a8a,#1d4ed8);
      color: #fff; font-size: 14px; font-weight: 700;
      white-space: nowrap; transition: transform .2s, box-shadow .2s;
      box-shadow: 0 4px 12px rgba(29,78,216,.25);
    }
    .view-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(29,78,216,.35); }

    /* ── Modal (shared) ── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(15,23,42,.5);
      backdrop-filter: blur(4px); z-index: 1000;
      display: flex; align-items: center; justify-content: center;
      padding: 20px; opacity: 0; pointer-events: none;
      transition: opacity .25s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal {
      background: #fff; border-radius: 24px;
      width: 100%; max-width: 520px; max-height: 90vh;
      overflow-y: auto; box-shadow: 0 40px 100px rgba(15,23,42,.2);
      transform: translateY(20px) scale(.97);
      transition: transform .3s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-overlay.open .modal { transform: translateY(0) scale(1); }

    /* ── Event Modal Hero ── */
    .modal-hero {
      height: 80vh; max-height: 720px;
      position: relative; display: flex;
      align-items: center; justify-content: center;
    }
    .modal-hero img { width: 100%; height: 100%; object-fit: cover; }
    .modal-hero-placeholder {
      width: 100%; height: 100%;
      background: linear-gradient(135deg,#e0e7ff,#ede9fe);
      display: grid; place-items: center; font-size: 60px;
    }
    .modal-hero-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top,rgba(15,23,42,.5) 0%,transparent 60%);
    }
    .modal-hero-content { position: absolute; bottom: 18px; left: 22px; right: 50px; }
    .modal-hero-content .badge { margin-bottom: 8px; display: inline-block; }
    .modal-hero-content h2 { margin: 0; color: #fff; font-size: 22px; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,.3); }
    .modal-close {
      position: absolute; top: 14px; right: 14px;
      width: 36px; height: 36px; border-radius: 50%; border: none;
      background: rgba(255,255,255,.9); color: #1f2937;
      font-size: 18px; display: grid; place-items: center;
      transition: background .2s; z-index: 2;
    }
    .modal-close:hover { background: #fff; }
    .modal-body { padding: 24px; display: grid; gap: 16px; }
    .modal-info-row { display: grid; gap: 12px; }
    .modal-info-item { padding: 16px 18px; border-radius: 14px; display: flex; flex-direction: column; gap: 6px; }
    .modal-info-item.date-bg { background: #eff6ff; }
    .modal-info-item.time-bg { background: #fffbeb; }
    .modal-info-item.loc-bg  { background: #fef2f2; }
    .modal-info-label { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-muted); }
    .modal-info-value { font-size: 16px; font-weight: 600; padding-left: 26px; }
    .modal-desc-title { margin: 0 0 6px; font-size: 16px; font-weight: 700; }
    .modal-desc-text { margin: 0; font-size: 14px; color: #475569; line-height: 1.7; }
    .modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn-add-cal {
      padding: 14px; border-radius: 12px; border: none;
      background: linear-gradient(135deg,#d97706,#b45309);
      color: #fff; font-size: 14px; font-weight: 700;
      transition: transform .2s, box-shadow .2s;
    }
    .btn-add-cal:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(180,83,9,.3); }
    .btn-share {
      padding: 14px; border-radius: 12px;
      border: 1.5px solid var(--border); background: #fff;
      color: var(--text); font-size: 14px; font-weight: 600;
      transition: border-color .2s;
    }
    .btn-share:hover { border-color: #7c3aed; color: #7c3aed; }

    /* ── Calendar Modal specific ── */
    .cal-modal-header {
      padding: 20px 24px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
    }
    .cal-modal-header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    .cal-close-btn {
      width: 36px; height: 36px; border-radius: 50%; border: none;
      background: #f1f5f9; color: #1f2937;
      font-size: 18px; display: grid; place-items: center;
      flex-shrink: 0; transition: background .2s;
    }
    .cal-close-btn:hover { background: #e2e8f0; }
    .cal-image-area {
      background: #0f172a;
      display: flex; align-items: center; justify-content: center;
      min-height: 400px; max-height: 62vh; overflow: hidden;
    }
    .cal-image-area img {
      max-width: 100%; max-height: 62vh;
      object-fit: contain; display: block;
    }
    .cal-modal-footer { padding: 20px 24px; display: grid; gap: 12px; }
    .cal-toggle-row { display: flex; gap: 10px; }
    .cal-toggle-row .btn-share,
    .cal-toggle-row .btn-add-cal { flex: 1; padding: 13px; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 16px; padding: 16px 20px; }
      .sidebar-nav { flex-direction: row; flex-wrap: wrap; }
      .sidebar-footer { margin-top: 0; }
    }
    @media (max-width: 760px) {
      .main-area { padding: 20px 16px; }
      .event-card { grid-template-columns: 1fr; }
      .event-img, .event-img-placeholder { width: 100%; height: 160px; }
      .event-action { padding: 0 20px 20px; }
      .modal-actions { grid-template-columns: 1fr; }
      .cal-toggle-row { flex-direction: column; }
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
       <a href="admin_dashboard.php"    class="nav-item"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php"       class="nav-item active"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php" class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
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
        <h1 class="page-title">Events &amp; Activities</h1>
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

    <!-- Events Header -->
    <div class="events-header">
      <div>
        <h2>Upcoming Events</h2>
        <p>Stay updated with the latest university events and activities</p>
      </div>
      <button type="button" class="calendar-btn" onclick="openCalendar()">
        📅 School Calendar
      </button>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all">All Events</button>
      <button class="filter-tab" data-filter="academic">Academic</button>
      <button class="filter-tab" data-filter="university">University Event</button>
      <button class="filter-tab" data-filter="career">Career Development</button>
      <button class="filter-tab" data-filter="student">Student Activity</button>
    </div>

    <!-- Event List -->
    <div class="event-list" id="eventList"></div>
  </main>
</div>

<!-- ── Event Detail Modal ── -->
<div class="modal-overlay" id="modalOverlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal" id="modal">
    <div class="modal-hero" id="modalHero">
      <button class="modal-close" id="modalClose" aria-label="Close">✕</button>
      <div class="modal-hero-overlay"></div>
      <div class="modal-hero-content">
        <span class="badge" id="modalBadge"></span>
        <h2 id="modalTitle"></h2>
      </div>
    </div>
    <div class="modal-body">
      <div class="modal-info-row">
        <div class="modal-info-item date-bg">
          <span class="modal-info-label">📅 Date</span>
          <span class="modal-info-value" id="modalDate"></span>
        </div>
        <div class="modal-info-item time-bg">
          <span class="modal-info-label">🕐 Time</span>
          <span class="modal-info-value" id="modalTime"></span>
        </div>
        <div class="modal-info-item loc-bg">
          <span class="modal-info-label">📍 Location</span>
          <span class="modal-info-value" id="modalLocation"></span>
        </div>
      </div>
      <div>
        <p class="modal-desc-title">Event Description</p>
        <p class="modal-desc-text" id="modalDesc"></p>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-add-cal">📅 Add to Calendar</button>
        <button type="button" class="btn-share">🔗 Share Event</button>
      </div>
    </div>
  </div>
</div>

<!-- ── Calendar Modal (FIXED) ── -->
<div class="modal-overlay" id="calendarOverlay" role="dialog" aria-modal="true">
  <div class="modal" style="max-width: 640px;">

    <!-- Header -->
    <div class="cal-modal-header">
      <div>
        <span class="badge badge-university" style="margin-bottom: 6px; display: inline-block;">School Calendar</span>
        <h2 id="calendarTitle">1st Semester Calendar</h2>
      </div>
      <button class="cal-close-btn" onclick="closeCalendar()" aria-label="Close">✕</button>
    </div>

    <!-- Image display area -->
    <div class="cal-image-area">
      <img
        id="calendarImage"
        src="../images/QCU-Calendar-1stSem.png"
        alt="School Calendar"
      />
    </div>

    <!-- Toggle + description -->
    <div class="cal-modal-footer">
      <div class="cal-toggle-row">
        <button onclick="showFirstSem()" class="btn-share">1st Semester</button>
        <button onclick="showSecondSem()" class="btn-add-cal">2nd Semester</button>
      </div>
      <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
        Switch between academic calendars for each semester.
      </p>
    </div>

  </div>
</div>

<script>
  /* ── Event Data ── */
  const events = [
    {
      id: 1,
      title: "Midterm Examination",
      category: "academic",
      label: "Academic",
      date: "March 25, 2026",
      time: "9:00 AM - 12:00 PM",
      location: "Room 301, Engineering Building",
      description: "Midterm examination for Computer Science 101. Please bring your student ID and required materials.",
      image: "PLACEHOLDER"
    },
    {
      id: 2,
      title: "University Foundation Day",
      category: "university",
      label: "University Event",
      date: "March 28, 2026",
      time: "8:00 AM - 5:00 PM",
      location: "QCU Main Quadrangle",
      description: "Annual celebration of QCU's founding. Join us for a day of festivities, competitions, and performances.",
      image: "PLACEHOLDER"
    },
    {
      id: 3,
      title: "Web Development Project Submission",
      category: "academic",
      label: "Academic",
      date: "March 30, 2026",
      time: "11:59 PM",
      location: "Online - Student Portal",
      description: "Final project submission deadline for Web Development course. Submit through the student portal.",
      image: "PLACEHOLDER"
    },
    {
      id: 4,
      title: "Career Fair 2026",
      category: "career",
      label: "Career Development",
      date: "April 2, 2026",
      time: "1:00 PM - 5:00 PM",
      location: "University Auditorium",
      description: "Connect with top employers from various industries. Bring your resume and dress professionally. Over 50 companies are participating.",
      image: "PLACEHOLDER"
    },
    {
      id: 5,
      title: "Student Council Election",
      category: "student",
      label: "Student Activity",
      date: "April 10, 2026",
      time: "8:00 AM - 4:00 PM",
      location: "College Gymnasium",
      description: "Cast your vote for the next Student Council officers. Every vote counts — make your voice heard!",
      image: "PLACEHOLDER"
    },
    {
      id: 6,
      title: "Seminar on Artificial Intelligence",
      category: "academic",
      label: "Academic",
      date: "April 15, 2026",
      time: "2:00 PM - 5:00 PM",
      location: "AVR Room, Main Building",
      description: "Industry experts will discuss the latest trends in AI and machine learning. Open to all IT and CS students.",
      image: "PLACEHOLDER"
    }
  ];

  /* ── Badge class map ── */
  const badgeClass = {
    academic:   "badge-academic",
    university: "badge-university",
    career:     "badge-career",
    student:    "badge-student"
  };

  /* ── Render cards ── */
  function renderEvents(filter = "all") {
    const list = document.getElementById("eventList");
    const filtered = filter === "all" ? events : events.filter(e => e.category === filter);
    list.innerHTML = filtered.map(ev => `
      <article class="event-card" data-id="${ev.id}">
        <div class="event-img-placeholder">${ev.image}</div>
        <div class="event-body">
          <div class="event-title-row">
            <h3 class="event-title">${ev.title}</h3>
            <span class="badge ${badgeClass[ev.category] || 'badge-default'}">${ev.label}</span>
          </div>
          <div class="event-meta">
            <span>📅 ${ev.date}</span>
            <span>🕐 ${ev.time}</span>
            <span>📍 ${ev.location}</span>
          </div>
          <p class="event-desc">${ev.description}</p>
        </div>
        <div class="event-action">
          <button type="button" class="view-btn" onclick="openModal(${ev.id})">
            👁 View Details
          </button>
        </div>
      </article>
    `).join("");
  }

  /* ── Filter tabs ── */
  document.querySelectorAll(".filter-tab").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".filter-tab").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      renderEvents(btn.dataset.filter);
    });
  });

  /* ── Event Detail Modal ── */
  function openModal(id) {
    const ev = events.find(e => e.id === id);
    if (!ev) return;

    const hero = document.getElementById("modalHero");
    const existingMedia = hero.querySelector(".modal-hero-img, .modal-hero-placeholder");
    if (existingMedia) existingMedia.remove();
    const placeholder = document.createElement("div");
    placeholder.className = "modal-hero-placeholder";
    placeholder.textContent = ev.image;
    hero.insertBefore(placeholder, hero.firstChild);

    document.getElementById("modalBadge").className = `badge ${badgeClass[ev.category] || "badge-default"}`;
    document.getElementById("modalBadge").textContent = ev.label;
    document.getElementById("modalTitle").textContent = ev.title;
    document.getElementById("modalDate").textContent = ev.date;
    document.getElementById("modalTime").textContent = ev.time;
    document.getElementById("modalLocation").textContent = ev.location;
    document.getElementById("modalDesc").textContent = ev.description;

    document.getElementById("modalOverlay").classList.add("open");
    document.body.style.overflow = "hidden";
  }

  function closeModal() {
    document.getElementById("modalOverlay").classList.remove("open");
    document.body.style.overflow = "";
  }

  document.getElementById("modalClose").addEventListener("click", closeModal);
  document.getElementById("modalOverlay").addEventListener("click", e => {
    if (e.target === document.getElementById("modalOverlay")) closeModal();
  });
  document.addEventListener("keydown", e => {
    if (e.key === "Escape") { closeModal(); closeCalendar(); }
  });

  /* ── Calendar Modal ── */
  const calendarData = {
    first:  { title: "1st Semester Calendar", image: "../images/QCU-Calendar-1stSem.png" },
    second: { title: "2nd Semester Calendar", image: "../images/QCU-Calendar-2ndSem.png" }
  };

  function openCalendar() {
    document.getElementById("calendarOverlay").classList.add("open");
    document.body.style.overflow = "hidden";
  }

  function closeCalendar() {
    document.getElementById("calendarOverlay").classList.remove("open");
    document.body.style.overflow = "";
  }

  function showFirstSem() {
    document.getElementById("calendarTitle").textContent = calendarData.first.title;
    document.getElementById("calendarImage").src = calendarData.first.image;
  }

  function showSecondSem() {
    document.getElementById("calendarTitle").textContent = calendarData.second.title;
    document.getElementById("calendarImage").src = calendarData.second.image;
  }

  document.getElementById("calendarOverlay").addEventListener("click", e => {
    if (e.target.id === "calendarOverlay") closeCalendar();
  });

  /* ── Init ── */
  renderEvents();
</script>
</body>
</html>