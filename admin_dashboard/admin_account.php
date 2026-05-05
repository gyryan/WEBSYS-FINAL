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
  <title>QCU Student Portal — Account</title>
  <link rel="icon" type="image/png" href="../images/QCU-logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    :root {
      font-family: 'Montserrat', sans-serif;
      --surface: #ffffff;
      --surface-strong: #f1f5f9;
      --text: #1f2937;
      --text-muted: #64748b;
      --border: rgba(148,163,184,.2);
      --accent-soft: #eef2ff;
    }
    body {
      margin: 0; min-height: 100vh;
      background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%);
      color: var(--text);
    }
      .profile-avatar {
      width: 52px; height: 52px; border-radius: 16px;
      background: linear-gradient(135deg,#7c3aed,#2563eb);
      color: #fff; place-items: center; font-weight: 800; display: grid;
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
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }

    /* ── Account Card ── */
    .account-card {
      background: #fff; border: 1px solid var(--border);
      border-radius: 24px; box-shadow: 0 4px 24px rgba(15,23,42,.06);
      max-width: 860px; margin: 0 auto; overflow: hidden;
    }

    /* ── Profile Banner ── */
    .profile-banner {
      background: linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#3b0d51 100%);
      padding: 32px 36px; display: flex; align-items: center; gap: 24px;
    }
    .banner-avatar {
      width: 150px; height: 150px; border-radius: 50%;
     background: linear-gradient(135deg,#d97706,#b45309);
     color: #fff; display: grid; place-items: center;
       font-size: 28px; font-weight: 800; flex-shrink: 0;
      border: 3px solid rgba(255,255,255,.2);
       overflow: hidden;
      }
    .banner-info h2 { margin: 0 0 6px; color: #fff; font-size: 22px; font-weight: 800; }
    .banner-info p  { margin: 0 0 4px; color: rgba(255,255,255,.8); font-size: 14px; }
    .banner-info span { color: rgba(255,255,255,.6); font-size: 13px; }

    /* ── Form Section ── */
    .form-section { padding: 32px 36px; }
    .form-section h3 { margin: 0 0 24px; font-size: 18px; font-weight: 800; }

    .form-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }

    label {
      font-size: 13px; font-weight: 600; color: var(--text-muted);
    }
    input, textarea {
      padding: 12px 16px; border-radius: 12px;
      border: 1.5px solid var(--border);
      font-family: inherit; font-size: 14px; color: var(--text);
      background: #f8fafc; outline: none;
      transition: border-color .2s, background .2s;
    }
    input:focus, textarea:focus {
      border-color: #1d4ed8; background: #fff;
    }
    textarea { resize: vertical; min-height: 56px; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
    }
    @media (max-width: 700px) {
      .main-area { padding: 20px 14px; }
      .form-grid { grid-template-columns: 1fr; }
      .profile-banner { flex-direction: column; text-align: center; }
      .form-section { padding: 24px 20px; }
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
      <a href="admin_events.php"       class="nav-item"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php" class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"    class="nav-item" active><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
        <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'"> Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <h1 class="page-title">Account Settings</h1>
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
    <div class="account-card">

      <!-- Profile Banner -->
      <div class="profile-banner">
        <div class="banner-avatar">
  <?php if ($picPath): ?>
    <img src="<?= htmlspecialchars($picPath) ?>" alt="Profile" 
         style="width:100%; height:100%; object-fit:cover; display:block;" />
  <?php else: ?>
    <?= $initials ?>
  <?php endif; ?>
</div>
        <div class="banner-info">
          <h2><?= htmlspecialchars($fullName) ?></h2>
          <p><?= htmlspecialchars($user['course']) ?></p>
          <span>Student ID: <?= htmlspecialchars($user['student_id']) ?></span>
        </div>
      </div>
      <!-- Profile Picture Upload -->
    <div style="padding: 20px 36px; border-bottom: 1px solid var(--border); display:flex; align-items:center; gap:16px;">
          <form action="upload-profile-pic.php" method="POST" enctype="multipart/form-data"
          style="display:flex; align-items:center; gap:12px;">
         <label for="profile_pic" style="
          padding: 10px 20px; background: #1d4ed8; color: white;
            border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600;">
             📷 Choose Photo
          </label>
         <input type="file" id="profile_pic" name="profile_pic" accept="image/*"
           style="display:none;" onchange="this.form.submit()" />
             <span style="font-size:13px; color:var(--text-muted);">JPG, PNG, WEBP · Max 2MB</span>
                    </form>
                    <?php if ($user['profile_pic']): ?>
  <a href="remove-profile-pic.php" 
     onclick="return confirm('Are you sure you want to remove your profile picture?')"
     style="padding: 10px 20px; background: #ef4444; color: white;
            border-radius: 10px; font-size: 14px; font-weight: 600;">
    🗑️ Remove Photo
  </a>
<?php endif; ?>

<?php if (isset($_GET['removed'])): ?>
  <span style="color:red; font-size:13px; font-weight:600;">🗑️ Photo removed!</span>
<?php endif; ?>

  <?php if (isset($_GET['success'])): ?>
    <span style="color:green; font-size:13px; font-weight:600;">✅ Photo updated!</span>
  <?php endif; ?>
</div>

      <!-- Personal Information Form -->
      <div class="form-section">
        <h3>Personal Information</h3>
        <div class="form-grid">

          <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" value="<?= htmlspecialchars($user['first_name']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" value="<?= htmlspecialchars($user['last_name']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" value="<?= htmlspecialchars($user['phone_number']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="studentId">Student ID</label>
            <input type="text" id="studentId" value="<?= htmlspecialchars($user['student_id']) ?>" readonly
              style="background:#f1f5f9;color:#64748b;cursor:not-allowed;" />
          </div>

          <div class="form-group">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" value="<?= htmlspecialchars($user['birthday']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="gender">Gender</label>
            <input type="text" id="gender" value="<?= htmlspecialchars($user['gender']) ?>"  readonly/>
          </div>

          <div class="form-group">
            <label for="yearLevel">Year Level</label>
            <input type="text" id="yearLevel" value="<?= htmlspecialchars($user['year_level']) ?>" readonly
              style="background:#f1f5f9;color:#64748b;cursor:not-allowed;" />
          </div>

          <div class="form-group full">
            <label for="program">Program</label>
            <input type="text" id="program" value="<?= htmlspecialchars($user['course']) ?>" readonly
              style="background:#f1f5f9;color:#64748b;cursor:not-allowed;" />
          </div>

          <div class="form-group full">
            <label for="address">Address</label>
            <textarea id="address"  readonly><?= htmlspecialchars($user['address']) ?></textarea>
          </div>

        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>