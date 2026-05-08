<?php
session_start();
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Fetch student data
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

// Fetch all announcements
$announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = [];
while ($row = mysqli_fetch_assoc($announcements_result)) {
    $announcements[] = $row;
}

// Fetch upcoming events
$events_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date LIMIT 5";
$events_result = mysqli_query($conn, $events_query);
$events = [];
while ($row = mysqli_fetch_assoc($events_result)) {
    $events[] = $row;
}
?>
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

    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }

    .sidebar {
      background: linear-gradient(180deg,#150edb 0%, #1f2937 100%);
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
    .user-greeting { margin: 0; font-size: 22px; font-weight: 800; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .chatbot-btn {
      width: 52px; height: 52px; border-radius: 16px;
      border: 1px solid var(--border); background: #fff; color: #7c3aed;
      display: grid; place-items: center; font-size: 22px;
      transition: background .2s, transform .2s, box-shadow .2s;
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

    .dashboard-grid {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 24px;
    }

    /* Left Column - Announcements */
    .announcements-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(15,23,42,.05);
    }
    .card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
    }
    .card-header h2 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
    }
    .card-header-icon { font-size: 20px; }

    .announcements-container {
      display: flex;
      flex-direction: column;
    }
    .announcement-item {
      padding: 24px;
      border-bottom: 1px solid var(--border);
      transition: background .2s;
    }
    .announcement-item:last-child {
      border-bottom: none;
    }
    .announcement-item:hover {
      background: #fafafa;
    }
   .announcement-image {
      width: 20%;
      height: auto;
      max-height: 500px;
      object-fit: cover;
      object-position: center;
      border-radius: 16px;
      margin-bottom: 16px;
      background: #f1f5f9;
    }

    .announcement-image:hover {
      transform: scale(1.02);
    }
    .announcement-title {
      font-size: 18px;
      font-weight: 800;
      margin: 0 0 10px;
      color: var(--text);
    }
    .announcement-description {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
      margin: 0 0 12px;
    }
    .announcement-date {
      font-size: 12px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .no-announcements {
      text-align: center;
      padding: 60px 24px;
      color: var(--text-muted);
    }

    /* Right Column - Events */
    .events-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(15,23,42,.05);
    }
    .event-list {
      display: flex;
      flex-direction: column;
    }
    .event-item {
      display: flex;
      gap: 16px;
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      transition: background .2s;
    }
    .event-item:last-child {
      border-bottom: none;
    }
    .event-item:hover {
      background: #fafafa;
    }
    .event-date {
      min-width: 65px;
      text-align: center;
    }
    .event-day {
      font-size: 28px;
      font-weight: 800;
      color: var(--accent);
      line-height: 1;
    }
    .event-month {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
    }
    .event-info h4 {
      margin: 0 0 6px;
      font-size: 15px;
      font-weight: 700;
    }
    .event-info p {
      margin: 0;
      font-size: 12px;
      color: var(--text-muted);
    }
    .no-events {
      text-align: center;
      padding: 40px 24px;
      color: var(--text-muted);
    }
    .view-all-link {
      display: block;
      padding: 14px 24px;
      text-align: center;
      background: var(--surface-strong);
      color: var(--accent);
      font-weight: 600;
      font-size: 13px;
      text-decoration: none;
      transition: background .2s;
    }
    .view-all-link:hover {
      background: var(--accent-soft);
    }

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
    <nav class="sidebar-nav">
      <a href="dashboard.php"   class="nav-item active"><span class="nav-item-icon">🏠</span>Dashboard</a>
      <a href="events.php"      class="nav-item"><span class="nav-item-icon">📅</span>Events</a>
      <a href="SchoolSched.php" class="nav-item"><span class="nav-item-icon">📋</span>Schedule</a>
      <a href="grades.php"      class="nav-item"><span class="nav-item-icon">📝</span>Grades</a>
      <a href="account.php"     class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
      <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <main class="main-area">
    <header class="topbar">
      <p class="user-greeting">Welcome back, <?= htmlspecialchars($user['first_name']) ?>! 👋</p>
      <div class="topbar-right">
        <button type="button" class="chatbot-btn" title="AI Assistant" onclick="alert('Chatbot coming soon!')">🤖</button>
        <div class="profile-card">
          <div class="profile-avatar">
            <?php if ($picPath && file_exists($picPath)): ?>
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

    <div class="dashboard-grid">
      <!-- Left Column: Announcements -->
      <div class="announcements-card">
        <div class="card-header">
          <span class="card-header-icon">📢</span>
          <h2>What's Happening at QCU</h2>
        </div>
        <div class="announcements-container">
          <?php if (empty($announcements)): ?>
            <div class="no-announcements">
              <p>No announcements yet. Check back later for updates!</p>
            </div>
          <?php else: ?>
            <?php foreach ($announcements as $announcement): ?>
              <div class="announcement-item">
                <?php if ($announcement['image_path'] && file_exists('../uploads/' . $announcement['image_path'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($announcement['image_path']) ?>" alt="Announcement" class="announcement-image">
                <?php endif; ?>
                <?php if ($announcement['title']): ?>
                  <h3 class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                <?php endif; ?>
                <?php if ($announcement['description']): ?>
                  <p class="announcement-description"><?= nl2br(htmlspecialchars($announcement['description'])) ?></p>
                <?php endif; ?>
                <div class="announcement-date">
                  📅 <?= date('F d, Y \a\t h:i A', strtotime($announcement['created_at'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right Column: Upcoming Events -->
      <div class="events-card">
        <div class="card-header">
          <span class="card-header-icon">📅</span>
          <h2>Upcoming Events</h2>
        </div>
        <div class="event-list">
          <?php if (empty($events)): ?>
            <div class="no-events">
              <p>No upcoming events scheduled.</p>
            </div>
          <?php else: ?>
            <?php foreach ($events as $event): ?>
              <div class="event-item">
                <div class="event-date">
                  <div class="event-day"><?= date('d', strtotime($event['event_date'])) ?></div>
                  <div class="event-month"><?= date('M', strtotime($event['event_date'])) ?></div>
                </div>
                <div class="event-info">
                  <h4><?= htmlspecialchars($event['title']) ?></h4>
                  <p><?= htmlspecialchars($event['location'] ?? 'Location TBA') ?></p>
                  <p><?= date('g:i A', strtotime($event['event_time'] ?? '00:00:00')) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <a href="events.php" class="view-all-link">View All Events →</a>
      </div>
    </div>
  </main>
</div>
</body>
</html>