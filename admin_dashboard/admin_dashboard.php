<?php
session_start();
require_once '../config/config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: ../landingpage/login.php');
    exit;
}

// Handle announcement upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_announcement'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $created_by = $_SESSION['student_id'];
    $image_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $image_name = time() . '_' . uniqid() . '.' . $file_ext;
            $image_path = 'announcements/' . $image_name;
            $target_path = '../uploads/' . $image_path;

            // Create directory if it doesn't exist
            if (!is_dir('../uploads/announcements')) {
                mkdir('../uploads/announcements', 0755, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO announcements (title, description, image_path, created_by) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $image_path, $created_by);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['message'] = "Announcement posted successfully!";
                } else {
                    $_SESSION['error'] = "Failed to save announcement.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['error'] = "Failed to upload image.";
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP";
        }
    } else {
        $_SESSION['error'] = "Please select an image to upload.";
    }

    header('Location: admin_dashboard.php');
    exit;
}

// Handle delete announcement
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get image path first
    $img_query = mysqli_query($conn, "SELECT image_path FROM announcements WHERE id = $id");
    $img_data = mysqli_fetch_assoc($img_query);
    
    if ($img_data && $img_data['image_path']) {
        $file_path = '../uploads/' . $img_data['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    mysqli_query($conn, "DELETE FROM announcements WHERE id = $id");
    $_SESSION['message'] = "Announcement deleted successfully!";
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch all announcements
$announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = [];
while ($row = mysqli_fetch_assoc($announcements_result)) {
    $announcements[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Admin — Announcements</title>
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

    /* ── Announcements Section ── */
    .announcements-section {
      display: flex;
      flex-direction: column;
      gap: 32px;
    }

    /* Upload Card */
    .upload-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      box-shadow: 0 4px 24px rgba(15,23,42,.05);
      padding: 28px;
    }
    .card-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }
    .card-header h2 {
      margin: 0;
      font-size: 20px;
      font-weight: 800;
    }
    .card-header-icon {
      font-size: 24px;
    }

    .upload-form {
      display: grid;
      gap: 20px;
    }
    .form-group {
      display: grid;
      gap: 8px;
    }
    .form-label {
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
    }
    .form-input, .form-textarea {
      padding: 12px 16px;
      border: 1.5px solid var(--border);
      border-radius: 12px;
      font-size: 14px;
      font-family: inherit;
      transition: all .2s;
    }
    .form-input:focus, .form-textarea:focus {
      outline: none;
      border-color: #7c3aed;
    }
    .form-textarea {
      resize: vertical;
      min-height: 100px;
    }
    .file-upload-wrapper {
      position: relative;
    }
    .file-upload-label {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 24px;
      background: linear-gradient(135deg, #7c3aed, #2563eb);
      color: white;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
      transition: transform .2s;
    }
    .file-upload-label:hover {
      transform: translateY(-2px);
    }
    .file-upload-input {
      display: none;
    }
    .file-name {
      margin-top: 8px;
      font-size: 12px;
      color: var(--text-muted);
    }
    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 8px;
    }
    .btn-submit {
      padding: 12px 28px;
      background: linear-gradient(135deg, #7c3aed, #2563eb);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      transition: transform .2s;
    }
    .btn-submit:hover {
      transform: translateY(-2px);
    }
    .btn-secondary {
      padding: 12px 28px;
      background: #f3f4f6;
      color: var(--text);
      border: 1px solid var(--border);
      border-radius: 12px;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      transition: all .2s;
    }
    .btn-secondary:hover {
      background: #e5e7eb;
    }

    /* Announcements Grid */
    .announcements-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
      gap: 24px;
    }
    .announcement-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      transition: transform .2s, box-shadow .2s;
    }
    .announcement-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(15,23,42,.12);
    }
    .announcement-image {
      width: 100%;
      height: 240px;
      object-fit: cover;
      background: var(--surface-strong);
    }
    .announcement-image-placeholder {
      width: 100%;
      height: 240px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: white;
    }
    .announcement-content {
      padding: 20px;
    }
    .announcement-title {
      font-size: 18px;
      font-weight: 800;
      margin: 0 0 10px;
      color: var(--text);
    }
    .announcement-description {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.6;
      margin: 0 0 12px;
    }
    .announcement-meta {
      font-size: 11px;
      color: var(--text-muted);
      padding-top: 12px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .delete-btn {
      padding: 6px 12px;
      background: #ef4444;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
      transition: background .2s;
    }
    .delete-btn:hover {
      background: #dc2626;
    }

    /* Alert Messages */
    .alert {
      padding: 14px 18px;
      border-radius: 12px;
      margin-bottom: 24px;
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

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 24px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
    }
    .empty-state p {
      color: var(--text-muted);
      margin-bottom: 20px;
    }

    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .announcements-grid { grid-template-columns: 1fr; }
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
      <a href="admin_sched.php"        class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"       class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"      class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer"> 
      <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>

  <!-- Main -->
  <main class="main-area">
    <header class="topbar">
      <p class="user-greeting">📢 Manage Announcements</p>
      <div class="topbar-right">
        <button type="button" class="chatbot-btn" title="AI Assistant" onclick="alert('Chatbot coming soon!')">🤖</button>
      </div>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($_SESSION['message']) ?></div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-error">❌ <?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="announcements-section">
      <!-- Upload Form -->
      <div class="upload-card">
        <div class="card-header">
          <span class="card-header-icon">📸</span>
          <h2>Post What's Happening at QCU</h2>
        </div>
        <form method="POST" action="admin_dashboard.php" enctype="multipart/form-data" class="upload-form">
          <div class="form-group">
            <label class="form-label">Title (Optional)</label>
            <input type="text" name="title" class="form-input" placeholder="e.g., QCU Foundation Day 2026" maxlength="255">
          </div>

          <div class="form-group">
            <label class="form-label">Description (Optional)</label>
            <textarea name="description" class="form-textarea" placeholder="Describe what's happening at QCU..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Upload Image *</label>
            <div class="file-upload-wrapper">
              <label class="file-upload-label">
                📷 Choose Image
                <input type="file" name="image" class="file-upload-input" accept="image/jpeg,image/png,image/gif,image/webp" required onchange="updateFileName(this)">
              </label>
            </div>
            <div class="file-name" id="fileName"></div>
          </div>

          <div class="form-actions">
            <button type="submit" name="upload_announcement" class="btn-submit">📤 Post Announcement</button>
          </div>
        </form>
      </div>

      <!-- Announcements Display -->
      <?php if (empty($announcements)): ?>
        <div class="empty-state">
          <p>No announcements yet. Post the first announcement to notify all students!</p>
        </div>
      <?php else: ?>
        <div class="announcements-grid">
          <?php foreach ($announcements as $announcement): ?>
            <div class="announcement-card">
              <?php if ($announcement['image_path'] && file_exists('../uploads/' . $announcement['image_path'])): ?>
                <img src="../uploads/<?= htmlspecialchars($announcement['image_path']) ?>" alt="Announcement" class="announcement-image">
              <?php else: ?>
                <div class="announcement-image-placeholder">
                  📢
                </div>
              <?php endif; ?>
              <div class="announcement-content">
                <?php if ($announcement['title']): ?>
                  <h3 class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></h3>
                <?php endif; ?>
                <?php if ($announcement['description']): ?>
                  <p class="announcement-description"><?= nl2br(htmlspecialchars($announcement['description'])) ?></p>
                <?php endif; ?>
                <div class="announcement-meta">
                  <span>📅 <?= date('M d, Y \a\t h:i A', strtotime($announcement['created_at'])) ?></span>
                  <a href="?delete=<?= $announcement['id'] ?>" onclick="return confirm('Delete this announcement?')" class="delete-btn">🗑 Delete</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<script>
  function updateFileName(input) {
    const fileName = input.files[0]?.name || '';
    const fileNameDisplay = document.getElementById('fileName');
    if (fileName) {
      fileNameDisplay.textContent = '✓ Selected: ' + fileName;
      fileNameDisplay.style.color = '#10b981';
    } else {
      fileNameDisplay.textContent = '';
    }
  }
</script>
</body>
</html>