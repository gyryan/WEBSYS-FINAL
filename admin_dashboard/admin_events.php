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
$initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
$fullName  = $user['first_name'] . ' ' . $user['last_name'];

// Create events upload directory if it doesn't exist
$eventsDir = '../uploads/events/';
if (!is_dir($eventsDir)) {
    mkdir($eventsDir, 0755, true);
}

// Check if event_image column exists and add it if not
$checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM events LIKE 'event_image'");
if (mysqli_num_rows($checkColumn) == 0) {
    mysqli_query($conn, "ALTER TABLE events ADD COLUMN event_image VARCHAR(255) DEFAULT NULL");
}

// ── Handle POST: Add Event ──
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $title       = trim($_POST['title'] ?? '');
        $category    = $_POST['category'] ?? 'academic';
        $event_date  = $_POST['event_date'] ?? '';
        $event_time  = trim($_POST['event_time'] ?? '');
        $location    = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $event_image = null;
        
        // Handle file upload
        if (isset($_FILES['event_photo']) && $_FILES['event_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['event_photo'];
            $filename = basename($file['name']);
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($ext, $allowed)) {
                if ($file['size'] <= 5 * 1024 * 1024) {
                    $newFilename = time() . '_' . uniqid() . '.' . $ext;
                    $filepath = $eventsDir . $newFilename;
                    
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $event_image = $newFilename;
                    } else {
                        $error = 'Failed to upload photo. Please check folder permissions.';
                    }
                } else {
                    $error = 'Photo must be under 5MB.';
                }
            } else {
                $error = 'Photo must be JPG, PNG, GIF, or WEBP.';
            }
        }

        if ($title && $category && $event_date && $event_time && $location && $description && !$error) {
            $sql = "INSERT INTO events (title, category, event_date, event_time, location, description, event_image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssssss', $title, $category, $event_date, $event_time, $location, $description, $event_image);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = '✅ Event "' . htmlspecialchars($title) . '" published successfully!';
                    if ($event_image) {
                        $success .= ' Image uploaded!';
                    }
                } else {
                    $error = 'Database error: ' . mysqli_error($conn);
                }
            } else {
                $error = 'Database error: ' . mysqli_error($conn);
            }
        } elseif (!$error) {
            $error = 'Please fill in all required fields.';
        }
    }

    if ($_POST['action'] === 'delete' && isset($_POST['event_id'])) {
        $eid = (int) $_POST['event_id'];
        
        // Get image filename to delete
        $imgQuery = mysqli_query($conn, "SELECT event_image FROM events WHERE id = $eid");
        if ($imgQuery && $imgRow = mysqli_fetch_assoc($imgQuery)) {
            if ($imgRow['event_image'] && file_exists($eventsDir . $imgRow['event_image'])) {
                unlink($eventsDir . $imgRow['event_image']);
            }
        }
        
        $del = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
        if ($del) {
            mysqli_stmt_bind_param($del, 'i', $eid);
            if (mysqli_stmt_execute($del)) {
                $success = '✅ Event deleted successfully.';
            } else {
                $error = 'Could not delete event.';
            }
        } else {
            $error = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// ── Fetch all events ──
$allEvents = [];
$eventsResult = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
if ($eventsResult) {
    while ($row = mysqli_fetch_assoc($eventsResult)) {
        $allEvents[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Admin — Manage Events</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="icon" type="image/png" href="../images/QCU-logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    :root {
      font-family: 'Montserrat', sans-serif;
      --bg: #f8fafc;
      --surface: #ffffff;
      --accent: #5b21b6;
      --accent-soft: #eef2ff;
      --text: #1f2937;
      --text-muted: #64748b;
      --border: rgba(148,163,184,.15);
      --success: #065f46;
      --success-bg: #d1fae5;
      --error: #991b1b;
      --error-bg: #fee2e2;
    }
    body { margin: 0; min-height: 100vh; background: linear-gradient(180deg,#eef2f8 0%,#f8fafc 100%); color: var(--text); }
    button { font-family: inherit; cursor: pointer; }
    a { text-decoration: none; color: inherit; }
 
    .app-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
 
    .sidebar {
      background: black; color: #f8fafc;
      padding: 32px 24px; display: flex; flex-direction: column; gap: 32px;
      position: sticky; top: 0; height: 100vh;
    }
    .sidebar-brand { display: flex; align-items: center; gap: 14px; }
    .nav-logo {
      width: 58px; height: 58px; border-radius: 18px; overflow: hidden;
      border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.12);
      display: grid; place-items: center;
    }
    .nav-logo img { width: 100%; height: 100%; object-fit: cover; }
    .brand-title { display: block; font-size: 16px; font-weight: 800; letter-spacing: .5px; }
    .brand-sub { display: block; color: rgba(248,250,252,.72); font-size: 12px; margin-top: 4px; }
    .sidebar-nav { display: grid; gap: 10px; }
    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 18px; border: none;
      background: transparent; color: #f8fafc; font-size: 14px; border-radius: 14px;
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
      background: transparent; color: #f8fafc; font-size: 14px; transition: background .2s;
    }
    .logout-button:hover { background: rgba(255,255,255,.08); }
 
    .main-area { padding: 28px 32px; }
 
    .topbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 24px; margin-bottom: 28px; }
    .user-greeting { margin: 0; color: #475569; font-size: 14px; }
    .page-title { margin: 8px 0 0; font-size: clamp(26px,3vw,34px); line-height: 1.05; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .profile-card { display: flex; align-items: center; gap: 16px; padding: 14px 18px; background: #fff; border: 1px solid var(--border); border-radius: 18px; }
    .profile-avatar { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg,#7c3aed,#2563eb); color: #fff; display: grid; place-items: center; font-weight: 800; overflow: hidden; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-name { margin: 0; font-weight: 700; }
    .profile-email { margin: 0; color: var(--text-muted); font-size: 13px; }
 
    .alert { padding: 14px 18px; border-radius: 14px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
    .alert-success { background: var(--success-bg); color: var(--success); }
    .alert-error   { background: var(--error-bg);   color: var(--error); }
 
    .content-grid { display: grid; grid-template-columns: 420px 1fr; gap: 28px; align-items: start; }
 
    .form-card {
      background: #fff; border-radius: 20px; padding: 28px;
      border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(15,23,42,.04);
      position: sticky; top: 28px;
    }
    .form-card h2 { margin: 0 0 4px; font-size: 20px; font-weight: 800; }
    .form-card p  { margin: 0 0 24px; color: var(--text-muted); font-size: 13px; }
 
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 700; color: var(--text-muted); }
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 12px 14px; border-radius: 12px;
      border: 1.5px solid #e2e8f0; font-family: inherit; font-size: 14px;
      color: var(--text); background: #f8fafc;
      transition: border-color .2s, box-shadow .2s; outline: none;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); background: #fff; }
    .form-group textarea { resize: vertical; min-height: 90px; }
 
    .photo-upload {
      margin-top: 8px;
    }
    .photo-preview {
      width: 120px;
      height: 120px;
      border-radius: 12px;
      background: #f1f5f9;
      border: 2px dashed #cbd5e1;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      overflow: hidden;
      background-size: cover;
      background-position: center;
    }
    .photo-preview span {
      color: #94a3b8;
      font-size: 12px;
      text-align: center;
    }
    .photo-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .file-input-label {
      display: inline-block;
      padding: 10px 20px;
      background: #eef2ff;
      color: #5b21b6;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .file-input-label:hover {
      background: #e0e7ff;
    }
    input[type="file"] {
      display: none;
    }
 
    .submit-btn {
      width: 100%; padding: 15px; border-radius: 14px; border: none;
      background: linear-gradient(135deg,#5b21b6,#7c3aed);
      color: #fff; font-size: 15px; font-weight: 700;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 4px 14px rgba(91,33,182,.3);
      margin-top: 8px;
    }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(91,33,182,.4); }
 
    .table-card {
      background: #fff; border-radius: 20px;
      border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(15,23,42,.04);
      overflow: hidden;
    }
    .table-header { padding: 22px 26px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .table-header h2 { margin: 0; font-size: 20px; font-weight: 800; }
    .event-count { font-size: 13px; color: var(--text-muted); font-weight: 600; }
 
    .events-table { width: 100%; border-collapse: collapse; }
    .events-table th {
      text-align: left; padding: 13px 20px;
      font-size: 12px; font-weight: 700; color: var(--text-muted);
      text-transform: uppercase; letter-spacing: .5px;
      background: #f8fafc; border-bottom: 1px solid var(--border);
    }
    .events-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .events-table tr:last-child td { border-bottom: none; }
    .events-table tr:hover td { background: #fafafa; }
 
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .badge-academic   { background: #dbeafe; color: #1e40af; }
    .badge-university { background: #d1fae5; color: #065f46; }
    .badge-career     { background: #fef3c7; color: #92400e; }
    .badge-student    { background: #ede9fe; color: #5b21b6; }
 
    .event-title-cell { font-weight: 700; }
    .event-image-cell { width: 60px; text-align: center; }
    .event-image-cell img { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; }
    .event-image-cell span { font-size: 28px; }
 
    .delete-btn {
      padding: 8px 14px; border-radius: 10px; border: none;
      background: #fee2e2; color: #991b1b; font-size: 13px; font-weight: 700;
      transition: background .2s, transform .2s;
    }
    .delete-btn:hover { background: #fca5a5; transform: scale(1.04); }
 
    .empty-state { padding: 48px 24px; text-align: center; color: var(--text-muted); }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; }
    .empty-state p { margin: 0; font-size: 15px; }
 
    @media (max-width: 1200px) { .content-grid { grid-template-columns: 1fr; } .form-card { position: static; } }
    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; flex-direction: row; flex-wrap: wrap; gap: 16px; padding: 16px 20px; }
      .sidebar-nav { flex-direction: row; flex-wrap: wrap; }
      .sidebar-footer { margin-top: 0; }
    }
    @media (max-width: 760px) { .main-area { padding: 20px 16px; } }
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
        <span class="brand-sub">Admin Dashboard</span>
      </div>
    </div>
    <nav class="sidebar-nav">
      <a href="admin_dashboard.php" class="nav-item"><span class="nav-item-icon">🏠</span>Admin Dashboard</a>
      <a href="admin_events.php"    class="nav-item active"><span class="nav-item-icon">📅</span>Add Events</a>
      <a href="admin_sched.php"     class="nav-item"><span class="nav-item-icon">📋</span>Add Schedule</a>
      <a href="admin_grades.php"    class="nav-item"><span class="nav-item-icon">📝</span>Add Grades</a>
      <a href="admin_account.php"   class="nav-item"><span class="nav-item-icon">👤</span>Account</a>
    </nav>
    <div class="sidebar-footer">
      <button type="button" class="logout-button" onclick="window.location.href='../landingpage/logout.php'">Logout</button>
    </div>
  </aside>
 
  <main class="main-area">
    <header class="topbar">
      <div>
        <p class="user-greeting">Admin Panel</p>
        <h1 class="page-title">Manage Events</h1>
      </div>
      <div class="topbar-right">
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
 
    <?php if ($success): ?>
      <div class="alert alert-success">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error">❌ <?= $error ?></div>
    <?php endif; ?>
 
    <div class="content-grid">
 
      <!-- Add Event Form with Image Upload -->
      <div class="form-card">
        <h2>➕ Add New Event</h2>
        <p>Fill in the details below. Upload an image to replace the event icon.</p>
 
        <form method="POST" action="admin_events.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add" />
 
          <div class="form-group">
            <label for="title">Event Title *</label>
            <input type="text" id="title" name="title" placeholder="e.g. Career Fair 2026" required />
          </div>
 
          <div class="form-group">
            <label for="category">Category *</label>
            <select id="category" name="category" required>
              <option value="academic">Academic</option>
              <option value="university">University Event</option>
              <option value="career">Career Development</option>
              <option value="student">Student Activity</option>
            </select>
          </div>
 
          <div class="form-group">
            <label for="event_date">Date *</label>
            <input type="date" id="event_date" name="event_date" required />
          </div>
 
          <div class="form-group">
            <label for="event_time">Time *</label>
            <input type="text" id="event_time" name="event_time" placeholder="e.g. 9:00 AM – 12:00 PM" required />
          </div>
 
          <div class="form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" placeholder="e.g. University Auditorium" required />
          </div>
 
          <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" placeholder="Briefly describe the event..." required></textarea>
          </div>
 
          <div class="form-group">
            <label>Event Image (Upload photo - this will replace the emoji icon)</label>
            <div class="photo-upload">
              <div class="photo-preview" id="photoPreview">
                <span>📷 Click to upload image</span>
              </div>
              <label class="file-input-label" for="event_photo">
                📸 Choose Image (JPG, PNG, GIF, WEBP, max 5MB)
              </label>
              <input type="file" id="event_photo" name="event_photo" accept="image/jpeg,image/png,image/gif,image/webp" 
                     onchange="previewImage(this)" />
            </div>
            <small style="font-size: 11px; color: #64748b; margin-top: 5px;">Recommended size: 400x300px. Images will be displayed on the student events page.</small>
          </div>
 
          <button type="submit" class="submit-btn">Publish Event</button>
        </form>
      </div>
 
      <!-- Events Table -->
      <div class="table-card">
        <div class="table-header">
          <h2>All Events</h2>
          <span class="event-count"><?= count($allEvents) ?> event<?= count($allEvents) !== 1 ? 's' : '' ?></span>
        </div>
 
        <?php if (empty($allEvents)): ?>
          <div class="empty-state">
            <div class="empty-icon">📭</div>
            <p>No events yet. Add one using the form on the left!</p>
          </div>
        <?php else: ?>
          <table class="events-table">
            <thead>
              <tr>
                <th style="width:60px">Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>Location</th>
                <th style="width:80px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($allEvents as $ev): ?>
                <tr>
                  <td class="event-image-cell">
                    <?php if (isset($ev['event_image']) && $ev['event_image'] && file_exists('../uploads/events/' . $ev['event_image'])): ?>
                      <img src="../uploads/events/<?= htmlspecialchars($ev['event_image']) ?>" alt="<?= htmlspecialchars($ev['title']) ?>" />
                    <?php else: ?>
                      <span>📅</span>
                    <?php endif; ?>
                  </td>
                  <td class="event-title-cell"><?= htmlspecialchars($ev['title']) ?></td>
                  <td>
                    <span class="badge badge-<?= htmlspecialchars($ev['category']) ?>">
                      <?= ucfirst(str_replace('_', ' ', $ev['category'])) ?>
                    </span>
                  </td>
                  <td><?= date('M j, Y', strtotime($ev['event_date'])) ?></td>
                  <td style="color:var(--text-muted)"><?= htmlspecialchars($ev['location']) ?></td>
                  <td>
                    <form method="POST" action="admin_events.php" onsubmit="return confirm('Delete this event?')">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="event_id" value="<?= $ev['id'] ?>" />
                      <button type="submit" class="delete-btn">🗑 Delete</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          可有
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" />';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<span>📷 Click to upload image</span>';
    }
}
</script>
</body>
</html>