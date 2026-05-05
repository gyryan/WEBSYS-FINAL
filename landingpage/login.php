<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = trim($_POST['studentId'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) {
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['name']       = $user['first_name'] . ' ' . $user['last_name'];
        
        // Redirect admin to admin dashboard
        if ($user['student_id'] === 'admin') {
            header('Location: ../admin_dashboard/admin_dashboard.php');
        } else {
            header('Location: ../dashboard/dashboard.php');
        }
        exit;
    } else {
        $loginError = "Invalid Student ID or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="../images/QCU-logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Log In</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
   *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --purple-dark:   #1a0a4e;
      --purple-mid:    #2d1480;
      --purple-light:  #4a22b8;
      --gold:          #c9991f;
      --gold-hover:    #b38518;
      --gold-light:    #e8b82a;
      --white:         #ffffff;
      --gray-100:      #f4f4f6;
      --gray-300:      #d1d5db;
      --gray-500:      #6b7280;
      --gray-700:      #374151;
      --gray-900:      #111827;
      --radius:        10px;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f0eef8;
      font-family: 'Inter', sans-serif;
      padding: 2rem 1rem;
    }
    
    .card {
  display:flex;
  width: 100%;
  max-width: 950px;
  min-height: 560px;
  gap: 22px; /* 👈 THIS creates separation */
  box-shadow: none;
    }

    /* ── LEFT PANEL ──────────────────────────────── */
   .left {
  flex: 0 0 42%;
  background: linear-gradient(160deg, #2d1480 0%, #4a22b8 45%, #3b18a0 100%);
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(30, 10, 80, 0.25);
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4.5rem 3rem;
  overflow: hidden;
  color: #fff;
}

    
    /* Decorative arrowhead shapes */
    .deco-arrows {
      position: absolute;
      left: 50%;
      transform: translateX(-40%);
      opacity: 0.50;
      pointer-events: none;
    }
    .deco-arrows img { 
        object-fit: cover; /* makes image fill nicely */
    }

    /* Year watermark text */
    .year-mark {
      position: absolute;
      bottom: 18px;
      left: 50%;
      transform: translateX(-50%);
      font-family: 'Montserrat', sans-serif;
      font-size: 42px;
      font-weight: 800;
      color: rgba(255,255,255,0.07);
      letter-spacing: 4px;
      pointer-events: none;
      user-select: none;
    }

    /* Logo placeholder */
    .logo-wrap {
      position: relative;
      z-index: 2;
      margin-bottom: 1.2rem;
    }
    .logo-circle {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.6);
      background: rgba(255,255,255,0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .logo-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .left-content { position: relative; z-index: 2; text-align: center; width: 100%; }
    .welcome-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.75rem;
      font-weight: 800;
      margin-bottom: 4px;
      letter-spacing: 0.5px;
    }
    .univ-name {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      opacity: 0.9;
      margin-bottom: 2px;
    }
    .portal-label {
      font-size: 0.7rem;
      letter-spacing: 3px;
      text-transform: uppercase;
      opacity: 0.65;
      margin-bottom: 2rem;
    }

    .features { list-style: none; text-align: left; display: flex; flex-direction: column; gap: 12px; }
    .features li { display: flex; align-items: center; gap: 10px; font-size: 0.82rem; opacity: 0.92; }
    .check-circle {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .check-circle svg { width: 12px; height: 12px; }

    /* ── RIGHT PANEL ─────────────────────────────── */
   .right {
  flex: 1;
  background: var(--white);
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 2.8rem;
}

    .form-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.9rem;
      font-weight: 800;
      color: var(--gray-900);
      margin-bottom: 6px;
    }
    .form-subtitle { font-size: 0.85rem; color: var(--gray-500); margin-bottom: 1.8rem; }

    .field-group { margin-bottom: 1.1rem; }
    .field-label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--gray-700); margin-bottom: 6px; }

    .input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .input-icon {
      position: absolute;
      left: 12px;
      color: var(--gray-500);
      display: flex;
    }
    .input-icon svg { width: 16px; height: 16px; }

    .field-input {
      width: 100%;
      padding: 10px 40px 10px 38px;
      border: 1.5px solid var(--gray-300);
      border-radius: var(--radius);
      font-size: 0.875rem;
      color: var(--gray-900);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      font-family: 'Inter', sans-serif;
      background: var(--white);
    }
    .field-input::placeholder { color: var(--gray-300); }
    .field-input:focus { border-color: var(--purple-light); box-shadow: 0 0 0 3px rgba(74,34,184,0.1); }

    .eye-btn {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray-500);
      display: flex;
      padding: 0;
    }
    .eye-btn svg { width: 16px; height: 16px; }

    .row-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.4rem;
    }
    .remember-label {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: 0.8rem;
      color: var(--gray-700);
      cursor: pointer;
    }
    .remember-label input[type="checkbox"] {
      width: 14px;
      height: 14px;
      accent-color: var(--purple-light);
      cursor: pointer;
    }
    .forgot-link {
      font-size: 0.8rem;
      color: var(--gold);
      text-decoration: none;
      font-weight: 600;
    }
    .forgot-link:hover { color: var(--gold-hover); text-decoration: underline; }

    .btn-login {
      width: 100%;
      padding: 12px;
      background: var(--gold);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      font-size: 0.95rem;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
    }
    .btn-login:hover { background: var(--gold-hover); }
    .btn-login:active { transform: scale(0.98); }

    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 1.2rem 0;
      color: var(--gray-300);
      font-size: 0.78rem;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--gray-300);
    }

    .btn-signup {
      width: 100%;
      padding: 11px;
      background: transparent;
      color: var(--gray-900);
      border: 1.5px solid var(--gray-900);
      border-radius: var(--radius);
      font-size: 0.95rem;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .btn-signup:hover { background: var(--gray-900); color: var(--white); }

    .back-home {
      display: block;
      text-align: center;
      margin-top: 1rem;
      font-size: 0.8rem;
      color: var(--gray-500);
      text-decoration: none;
    }
    .back-home:hover { color: var(--purple-light); }
    .back-home::before { content: '← '; }

    /* Responsive */
    @media (max-width: 680px) {
      .card { flex-direction: column; max-width: 420px; }
      .left { flex: none; padding: 2rem 1.5rem; }
      .right { padding: 2rem 1.5rem; }
      .features { display: none; }
    }
  </style>
</head>
<body>
<form method="POST" action="">
<div class="card">

  <!-- LEFT -->
  <div class="left">

    <!-- Decorative arrows (QCU tower silhouette) -->
    <div class="deco-arrows">
      <img src="../images/QCU-background.png" alt="">
    </div>

    <!-- Logo placeholder -->
    <div class="logo-wrap">
      <div class="logo-circle">
        <img src="../images/QCU-logo.png" alt="University Logo" />
      </div>
    </div>

    <div class="left-content">
      <h1 class="welcome-title">Welcome Back!</h1>
      <p class="univ-name">Quezon City University</p>
      <p class="portal-label">Student Portal</p>

      <ul class="features">
        <li>
          <span class="check-circle">
            <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          Access your grades &amp; schedules
        </li>
        <li>
          <span class="check-circle">
            <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          Request documents online
        </li>
        <li>
          <span class="check-circle">
            <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          Secure &amp; transparent transactions
        </li>
      </ul>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right">
    <h2 class="form-title">Log In</h2>
    <p class="form-subtitle">Enter your credentials to access your account</p>

    <div class="field-group">
      <label class="field-label" for="studentId">Student ID Number</label>
      <div class="input-wrap">
        <span class="input-icon">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
            <circle cx="8" cy="5" r="3"/>
            <path d="M2 14c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke-linecap="round"/>
          </svg>
        </span>
        <input class="field-input" type="text" id="studentId" name="studentId" placeholder="2X-XXXX" autocomplete="username" required />
      </div>
    </div>

    <div class="field-group">
      <label class="field-label" for="password">Password</label>
      <div class="input-wrap">
        <span class="input-icon">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="7" width="10" height="8" rx="2"/>
            <path d="M5.5 7V5a2.5 2.5 0 015 0v2" stroke-linecap="round"/>
          </svg>
        </span>
        <input class="field-input" type="password" id="password" placeholder="Enter your password" autocomplete="current-password" name="password" required/>
        <button class="eye-btn" id="eyeBtn" type="button" aria-label="Toggle password visibility">
          <svg id="eyeIcon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
            <circle cx="8" cy="8" r="2"/>
          </svg>
        </button>
      </div>
    </div>

    <div class="row-options">
      <label class="remember-label">
        <input type="checkbox" id="remember" />
        Remember me
      </label>
      <a href="#" class="forgot-link">Forgot password?</a>
    </div>
      <button class="btn-login" type="submit">Log In</button>
  

    <div class="divider">Don't have an account?</div>

    <a href="sign-up.php">
      <button class="btn-signup" type="button">Sign Up</button>
    </a>

    <a href="home.php" class="back-home">Back to Home</a>
  </div>

</div>
<?php if (!empty($loginError)) echo "<p style='color:red;'> <br>$loginError</p>"; ?>
<script>
  /* Eye toggle */
  const pwInput = document.getElementById('password');
  const eyeBtn  = document.getElementById('eyeBtn');
  const loginBtn = document.querySelector('.btn-login');
  const studentIdInput = document.getElementById('studentId');
  const eyeOpen = `<path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2"/>`;
  const eyeShut = `<line x1="1" y1="1" x2="15" y2="15"/><path d="M6.5 6.5A2 2 0 0010 10M4.2 4.2A7 7 0 001 8s2.5 5 7 5a6.9 6.9 0 003.8-1.2M9.9 3.2A7 7 0 0115 8s-.5 1-1.5 2.2"/>`;
  let visible = false;
  eyeBtn.addEventListener('click', () => {
    visible = !visible;
    pwInput.type = visible ? 'text' : 'password';
    eyeBtn.querySelector('svg').innerHTML = visible ? eyeShut : eyeOpen;
  });
</script>

</body>
</html>