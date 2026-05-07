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

// Fetch events from DB
$eventsResult = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
$dbEvents = [];
while ($row = mysqli_fetch_assoc($eventsResult)) {
    $dbEvents[] = $row;
}

// Category label map
$categoryLabels = [
    'academic'   => 'Academic',
    'university' => 'University Event',
    'career'     => 'Career Development',
    'student'    => 'Student Activity',
];
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
    body { margin: 0; min-height: 100vh; background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%); color: var(--text); }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }
 
    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
 
    .sidebar {
      background: linear-gradient(180deg,#3b0d51 0%,#14132b 100%);
      color: #f8fafc; padding: 32px 24px;
      display: flex; flex-direction: column; gap: 32px;
      position: sticky; top: 0; height: 100vh;
    }
    .sidebar-brand { display: flex; align-items: center; gap: 14px; }
    .nav-logo { width: 58px; height: 58px; border-radius: 18px; overflow: hidden; border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.12); display: grid; place-items: center; }
    .nav-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-title { display: block; font-size: 16px; font-weight: 800; letter-spacing: .5px; }
    .brand-sub { display: block; color: rgba(248,250,252,.72); font-size: 12px; margin-top: 4px; }
    .sidebar-nav { display: grid; gap: 10px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border: none; background: transparent; color: #f8fafc; font-size: 14px; border-radius: 14px; transition: background .2s, transform .2s; }
    .nav-item-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 12px; background: rgba(255,255,255,.08); font-size: 16px; }
    .nav-item:hover, .nav-item.active { background: rgba(255,255,255,.08); transform: translateX(2px); }
    .nav-item:hover .nav-item-icon, .nav-item.active .nav-item-icon { background: rgba(255,255,255,.18); }
    .sidebar-footer { margin-top: auto; }
    .logout-button { width: 100%; padding: 14px 18px; border: 1px solid rgba(255,255,255,.18); border-radius: 14px; background: transparent; color: #f8fafc; font-size: 14px; transition: background .2s; }
    .logout-button:hover { background: rgba(255,255,255,.08); }
 
    .main-area { padding: 28px 32px; }
 
    .topbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 24px; margin-bottom: 28px; }
    .user-greeting { margin: 0; color: #475569; font-size: 14px; }
    .page-title { margin: 8px 0 0; font-size: clamp(26px,3vw,34px); line-height: 1.05; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .chatbot-btn { width: 52px; height: 52px; border-radius: 16px; border: 1px solid var(--border); background: #fff; color: #7c3aed; display: grid; place-items: center; transition: background .2s, transform .2s, box-shadow .2s; box-shadow: 0 2px 8px rgba(15,23,42,.04); font-size: 22px; }
    .chatbot-btn:hover { background: var(--accent-soft); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(91,33,182,.12); }
    .profile-card { display: flex; align-items: center; gap: 16px; padding: 14px 18px; background: #fff; border: 1px solid var(--border); border-radius: 18px; }
    .profile-avatar { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg,#7c3aed,#2563eb); color: #fff; display: grid; place-items: center; font-weight: 800; overflow: hidden; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }
 
    .events-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 28px; }
    .events-header h2 { margin: 0; font-size: 24px; font-weight: 800; }
    .events-header p { margin: 6px 0 0; color: var(--text-muted); font-size: 14px; line-height: 1.6; }
    .calendar-btn { display: flex; align-items: center; gap: 8px; padding: 13px 20px; border-radius: 14px; border: none; background: linear-gradient(135deg,#d97706,#b45309); color: #fff; font-size: 14px; font-weight: 700; white-space: nowrap; transition: transform .2s, box-shadow .2s; box-shadow: 0 4px 14px rgba(180,83,9,.25); }
    .calendar-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(180,83,9,.3); }
 
    .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
    .filter-tab { padding: 8px 18px; border-radius: 30px; font-size: 13px; font-weight: 600; border: 1.5px solid var(--border); background: #fff; color: var(--text-muted); transition: all .2s; }
    .filter-tab:hover { border-color: #7c3aed; color: #7c3aed; }
    .filter-tab.active { background: #7c3aed; border-color: #7c3aed; color: #fff; }
 
    .event-list { display: grid; gap: 20px; }
    .event-card { background: #fff; border-radius: 20px; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(15,23,42,.04); display: grid; grid-template-columns: 200px 1fr auto; overflow: hidden; transition: box-shadow .25s, transform .25s; }
    .event-card:hover { box-shadow: 0 12px 40px rgba(15,23,42,.1); transform: translateY(-2px); }
    .event-img-placeholder { width: 200px; height: 140px; background: linear-gradient(135deg,#e0e7ff,#ede9fe); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .event-img-placeholder img { width: 100%; height: 100%; object-fit: cover; }
    .event-img-placeholder span { font-size: 40px; }
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
    .event-desc { margin: 0; font-size: 14px; color: #475569; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .event-action { padding: 20px 20px 20px 0; display: flex; align-items: center; }
    .view-btn { display: flex; align-items: center; gap: 8px; padding: 12px 20px; border-radius: 12px; border: none; background: linear-gradient(135deg,#1e3a8a,#1d4ed8); color: #fff; font-size: 14px; font-weight: 700; white-space: nowrap; transition: transform .2s, box-shadow .2s; box-shadow: 0 4px 12px rgba(29,78,216,.25); cursor: pointer; }
    .view-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(29,78,216,.35); }
 
    .empty-state { padding: 60px 24px; text-align: center; color: var(--text-muted); background: #fff; border-radius: 20px; border: 1px solid var(--border); }
    .empty-state .empty-icon { font-size: 52px; margin-bottom: 12px; }
    .empty-state p { margin: 0; font-size: 15px; }
 
    .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.8); backdrop-filter: blur(8px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px; opacity: 0; visibility: hidden; transition: opacity .3s ease, visibility .3s ease; }
    .modal-overlay.open { opacity: 1; visibility: visible; }
    .modal { background: #fff; border-radius: 28px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,.25); transform: scale(0.95); transition: transform .3s ease; }
    .modal-overlay.open .modal { transform: scale(1); }
    .modal-hero { height: 220px; position: relative; overflow: hidden; border-radius: 28px 28px 0 0; background: linear-gradient(135deg,#1e3a8a,#7c3aed); }
    .modal-hero img { width: 100%; height: 100%; object-fit: cover; }
    .modal-hero .hero-emoji { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 80px; background: linear-gradient(135deg,#e0e7ff,#ede9fe); }
    .modal-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%); }
    .modal-hero-content { position: absolute; bottom: 20px; left: 24px; right: 60px; }
    .modal-hero-content .badge { margin-bottom: 8px; display: inline-block; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); color: white; }
    .modal-hero-content h2 { margin: 0; color: #fff; font-size: 22px; font-weight: 800; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
    .modal-close { position: absolute; top: 16px; right: 16px; width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(255,255,255,0.9); color: #1f2937; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s; z-index: 10; }
    .modal-close:hover { background: #fff; transform: scale(1.05); }
    .modal-body { padding: 28px; }
    .modal-info-row { display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px; }
    .modal-info-item { padding: 14px 18px; border-radius: 16px; display: flex; align-items: center; gap: 12px; }
    .modal-info-item.date-bg { background: #eff6ff; }
    .modal-info-item.time-bg { background: #fffbeb; }
    .modal-info-item.loc-bg  { background: #fef2f2; }
    .modal-info-label { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
    .modal-info-value { font-size: 15px; font-weight: 500; color: var(--text); }
    .modal-desc-title { margin: 0 0 8px; font-size: 16px; font-weight: 700; }
    .modal-desc-text { margin: 0; font-size: 14px; color: #475569; line-height: 1.7; }
    .modal-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; }
    .btn-add-cal, .btn-share { padding: 12px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .2s; }
    .btn-add-cal { background: linear-gradient(135deg,#d97706,#b45309); border: none; color: #fff; }
    .btn-add-cal:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(180,83,9,.3); }
    .btn-share { background: #fff; border: 1.5px solid var(--border); color: var(--text); }
    .btn-share:hover { border-color: #7c3aed; color: #7c3aed; }
 
    .cal-modal-header { padding: 20px 24px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
    .cal-modal-header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    .cal-close-btn { width: 36px; height: 36px; border-radius: 50%; border: none; background: #f1f5f9; color: #1f2937; font-size: 18px; display: grid; place-items: center; flex-shrink: 0; cursor: pointer; transition: background .2s; }
    .cal-close-btn:hover { background: #e2e8f0; }
    .cal-image-area { background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 400px; max-height: 62vh; overflow: hidden; }
    .cal-image-area img { max-width: 100%; max-height: 62vh; object-fit: contain; display: block; }
    .cal-modal-footer { padding: 20px 24px; display: grid; gap: 12px; }
    .cal-toggle-row { display: flex; gap: 10px; }
    .cal-toggle-row .btn-share, .cal-toggle-row .btn-add-cal { flex: 1; padding: 13px; }
 
    @media (max-width: 1100px) { .app-shell { grid-template-columns: 1fr; } .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 16px; padding: 16px 20px; } .sidebar-nav { flex-direction: row; flex-wrap: wrap; } .sidebar-footer { margin-top: 0; } }
    @media (max-width: 760px) { .main-area { padding: 20px 16px; } .event-card { grid-template-columns: 1fr; } .event-img-placeholder { width: 100%; height: 160px; } .event-action { padding: 0 20px 20px; } .modal-actions { grid-template-columns: 1fr; } .cal-toggle-row { flex-direction: column; } }
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
      <a href="dashboard.php"    class="nav-item"><span class="nav-item-icon">🏠</span>Dashboard</a>
      <a href="events.php"       class="nav-item active"><span class="nav-item-icon">📅</span>Events</a>
      <a href="SchoolSched.php"  class="nav-item"><span class="nav-item-icon">📋</span>Schedule</a>
      <a href="grades.php"       class="nav-item"><span class="nav-item-icon">📝</span>Grades</a>
      
      <a href="account.php"      class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
      <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>
 
  <main class="main-area">
    <header class="topbar">
      <div>
        <p class="user-greeting">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</p>
        <h1 class="page-title">Events &amp; Activities</h1>
      </div>
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
 
    <div class="events-header">
      <div>
        <h2>Upcoming Events</h2>
        <p>Stay updated with the latest university events and activities</p>
      </div>
      <button type="button" class="calendar-btn" onclick="openCalendar()">
        📅 School Calendar
      </button>
    </div>
 
    <div class="filter-tabs">
      <button class="filter-tab active" data-filter="all">All Events</button>
      <button class="filter-tab" data-filter="academic">Academic</button>
      <button class="filter-tab" data-filter="university">University Event</button>
      <button class="filter-tab" data-filter="career">Career Development</button>
      <button class="filter-tab" data-filter="student">Student Activity</button>
    </div>
 
    <div class="event-list" id="eventList"></div>
  </main>
</div>
 
<!-- Event Detail Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-hero" id="modalHero">
      <button class="modal-close" id="modalClose">✕</button>
      <div class="modal-hero-overlay"></div>
      <div class="modal-hero-content">
        <span class="badge" id="modalBadge"></span>
        <h2 id="modalTitle"></h2>
      </div>
    </div>
    <div class="modal-body">
      <div class="modal-info-row">
        <div class="modal-info-item date-bg">
          <div class="modal-info-label">📅 Date</div>
          <div class="modal-info-value" id="modalDate"></div>
        </div>
        <div class="modal-info-item time-bg">
          <div class="modal-info-label">🕐 Time</div>
          <div class="modal-info-value" id="modalTime"></div>
        </div>
        <div class="modal-info-item loc-bg">
          <div class="modal-info-label">📍 Location</div>
          <div class="modal-info-value" id="modalLocation"></div>
        </div>
      </div>
      <div>
        <p class="modal-desc-title">Description</p>
        <p class="modal-desc-text" id="modalDesc"></p>
      </div>
      <div class="modal-actions">
        <button class="btn-add-cal" onclick="alert('Added to calendar!')">📅 Add to Calendar</button>
        <button class="btn-share" onclick="alert('Share feature coming soon!')">🔗 Share Event</button>
      </div>
    </div>
  </div>
</div>
 
<!-- Calendar Modal -->
<div class="modal-overlay" id="calendarOverlay">
  <div class="modal" style="max-width: 640px;">
    <div class="cal-modal-header">
      <div>
        <span class="badge badge-university" style="margin-bottom: 6px; display: inline-block;">School Calendar</span>
        <h2 id="calendarTitle">1st Semester Calendar</h2>
      </div>
      <button class="cal-close-btn" onclick="closeCalendar()">✕</button>
    </div>
    <div class="cal-image-area">
      <img id="calendarImage" src="../images/QCU-Calendar-1stSem.png" alt="School Calendar" />
    </div>
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
  const events = <?php
    $jsEvents = [];
    foreach ($dbEvents as $ev) {
        $jsEvents[] = [
            'id'          => (int) $ev['id'],
            'title'       => $ev['title'],
            'category'    => $ev['category'],
            'label'       => $categoryLabels[$ev['category']] ?? ucfirst($ev['category']),
            'date'        => date('F j, Y', strtotime($ev['event_date'])),
            'time'        => $ev['event_time'],
            'location'    => $ev['location'],
            'description' => $ev['description'],
            'image'       => isset($ev['event_image']) && $ev['event_image'] && file_exists('../uploads/events/' . $ev['event_image']) 
                            ? '../uploads/events/' . $ev['event_image'] 
                            : null,
        ];
    }
    echo json_encode($jsEvents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
  ?>;
 
  const badgeClass = {
    academic:   'badge-academic',
    university: 'badge-university',
    career:     'badge-career',
    student:    'badge-student'
  };
 
  function renderEvents(filter = 'all') {
    const list = document.getElementById('eventList');
    const filtered = filter === 'all' ? events : events.filter(e => e.category === filter);
 
    if (filtered.length === 0) {
      list.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">📭</div>
          <p>No events found${filter !== 'all' ? ' in this category' : ''}.</p>
        </div>`;
      return;
    }
 
    list.innerHTML = filtered.map(ev => `
      <article class="event-card" data-id="${ev.id}">
        <div class="event-img-placeholder">
          ${ev.image ? `<img src="${ev.image}" alt="${ev.title}" />` : `<span>📅</span>`}
        </div>
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
          <p class="event-desc">${ev.description.substring(0, 120)}${ev.description.length > 120 ? '...' : ''}</p>
        </div>
        <div class="event-action">
          <button type="button" class="view-btn" onclick="openModal(${ev.id})">
            👁 View Details
          </button>
        </div>
      </article>
    `).join('');
  }
 
  function openModal(id) {
    const ev = events.find(e => e.id === id);
    if (!ev) return;
 
    const hero = document.getElementById('modalHero');
    // Clear existing content
    const existingImg = hero.querySelector('img');
    const existingEmoji = hero.querySelector('.hero-emoji');
    if (existingImg) existingImg.remove();
    if (existingEmoji) existingEmoji.remove();
 
    if (ev.image) {
      const img = document.createElement('img');
      img.src = ev.image;
      img.alt = ev.title;
      hero.insertBefore(img, hero.firstChild);
    } else {
      const emojiDiv = document.createElement('div');
      emojiDiv.className = 'hero-emoji';
      emojiDiv.textContent = '📅';
      hero.insertBefore(emojiDiv, hero.firstChild);
    }
 
    document.getElementById('modalBadge').className = `badge ${badgeClass[ev.category] || 'badge-default'}`;
    document.getElementById('modalBadge').textContent = ev.label;
    document.getElementById('modalTitle').textContent = ev.title;
    document.getElementById('modalDate').textContent = ev.date;
    document.getElementById('modalTime').textContent = ev.time;
    document.getElementById('modalLocation').textContent = ev.location;
    document.getElementById('modalDesc').textContent = ev.description;
 
    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
 
  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }
 
  document.getElementById('modalClose').addEventListener('click', closeModal);
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeCalendar(); }
  });
 
  document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderEvents(btn.dataset.filter);
    });
  });
 
  const calendarData = {
    first:  { title: '1st Semester Calendar', image: '../images/QCU-Calendar-1stSem.png' },
    second: { title: '2nd Semester Calendar', image: '../images/QCU-Calendar-2ndSem.png' }
  };
 
  function openCalendar() { 
    document.getElementById('calendarOverlay').classList.add('open'); 
    document.body.style.overflow = 'hidden'; 
  }
  function closeCalendar() { 
    document.getElementById('calendarOverlay').classList.remove('open'); 
    document.body.style.overflow = ''; 
  }
  function showFirstSem() { 
    document.getElementById('calendarTitle').textContent = calendarData.first.title; 
    document.getElementById('calendarImage').src = calendarData.first.image; 
  }
  function showSecondSem() { 
    document.getElementById('calendarTitle').textContent = calendarData.second.title; 
    document.getElementById('calendarImage').src = calendarData.second.image; 
  }
 
  document.getElementById('calendarOverlay').addEventListener('click', function(e) {
    if (e.target.id === 'calendarOverlay') closeCalendar();
  });
 
  renderEvents();
</script>
</body>
</html>