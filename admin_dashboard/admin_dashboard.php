<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Dashboard</title>
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
      color: #cdd0d3; padding: 32px 24px;
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
    .user-greeting { margin: 0; font-size: 22px; font-weight: 800; }
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
      color: #fff; place-items: center; font-weight: 800; display: grid;
    }
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }

    /* ── Dashboard Grid ── */
    .dashboard-grid {
      display: grid;
      grid-template-columns: minmax(320px, 1.6fr) minmax(280px, 1fr);
      gap: 24px;
    }

    /* ── Card ── */
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

    /* ── Event List ── */
    .event-list { display: grid; gap: 14px; }
    .event-item {
      display: grid; grid-template-columns: 72px 1fr;
      gap: 16px; align-items: center;
      padding: 14px 16px; border-radius: 16px;
      border: 1px solid var(--border);
      background: #fafafa;
      transition: box-shadow .2s, transform .2s;
    }
    .event-item:hover { box-shadow: 0 4px 16px rgba(15,23,42,.08); transform: translateY(-1px); }
    .event-thumb {
      width: 72px; height: 56px; border-radius: 12px;
      background: linear-gradient(135deg,#eef2ff,#ede9fe);
      display: grid; place-items: center; font-size: 26px;
      flex-shrink: 0;
    }
    .event-info h3 { margin: 0 0 4px; font-size: 14px; font-weight: 700; }
    .event-info p  { margin: 0 0 2px; font-size: 12px; color: var(--text-muted); }
    .event-link {
      display: inline-flex; align-items: center; gap: 4px;
      margin-top: 6px; font-size: 12px; font-weight: 700; color: #2563eb;
    }
    .event-link:hover { text-decoration: underline; }

    /* ── Academic Overview ── */
    .overview-grid { display: grid; gap: 14px; }
    .overview-item {
      border-radius: 16px; padding: 18px 20px;
      border-left: 4px solid transparent;
      background: var(--surface-strong);
    }
    .overview-item.amber  { border-color: #d97706; background: #fffbeb; }
    .overview-item.blue   { border-color: #2563eb; background: #eff6ff; }
    .overview-item.purple { border-color: #7c3aed; background: #f5f3ff; }
    .overview-label { display: block; font-size: 12px; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
    .overview-value {
      display: block; font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 4px;
    }
    .overview-item.amber  .overview-value { color: #d97706; }
    .overview-item.blue   .overview-value { color: #1d4ed8; }
    .overview-item.purple .overview-value { color: #7c3aed; }
    .overview-note { display: block; font-size: 12px; color: var(--text-muted); }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .dashboard-grid { grid-template-columns: 1fr; }
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
      <a href="admin_dashboard.php"    class="nav-item active"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php"       class="nav-item"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php" class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"    class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer"> 
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">  Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <p class="user-greeting">Hello ! , Admin </p>
      <div class="topbar-right">
        
      
    </header>

    <div class="dashboard-grid">

      <!-- Upcoming Events -->
      <div class="card">
        <div class="card-header">
          <span class="card-header-icon">📅</span>
          <h2>Upcoming Events</h2>
        </div>
        <div class="event-list" id="eventList"></div>
      </div>

      <!-- Academic Overview -->
      <div class="card">
        <div class="card-header">
          <h2>Academic Overview</h2>
        </div>
        <div class="overview-grid">
          <div class="overview-item amber">
            <span class="overview-label">Current GPA</span>
            <span class="overview-value">3.75</span>
            <span class="overview-note">Excellent standing</span>
          </div>
          <div class="overview-item blue">
            <span class="overview-label">Enrolled Units</span>
            <span class="overview-value">21</span>
            <span class="overview-note">This semester</span>
          </div>
          <div class="overview-item purple">
            <span class="overview-label">Year Level</span>
            <span class="overview-value" style="font-size:24px;">2nd Year</span>
            <span class="overview-note">Bachelor of Science in Computer Science</span>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script>
  const dashEvents = [
    { emoji: "✍️", title: "Midterm Examination",      location: "Room 301, Engineering Building", time: "9:00 AM - 12:00 PM" },
    { emoji: "💻", title: "Project Submission",         location: "Online - Student Portal",        time: "11:59 PM" },
    { emoji: "🏢", title: "Career Fair",                location: "University Auditorium",          time: "1:00 PM - 5:00 PM" },
    { emoji: "🎉", title: "University Foundation Day",  location: "QCU Campus",                     time: "All Day Event" },
  ];

  document.getElementById("eventList").innerHTML = dashEvents.map(e => `
    <div class="event-item">
      <div class="event-thumb">${e.emoji}</div>
      <div class="event-info">
        <h3>${e.title}</h3>
        <p>${e.location}</p>
        <p>${e.time}</p>
        <a class="event-link" href="events.html">👁 View Details</a>
      </div>
    </div>
  `).join("");
</script>
</body>
</html>