<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Login</title>
  <link rel="icon" type="image/png" href="../images/QCU-logo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .auth-container {
      width: 100%;
      max-width: 1400px;
      min-height: 700px;
      background: #fff;
      border-radius: 32px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      display: flex;
      overflow: hidden;
    }

    .brand-panel {
      flex: 1;
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: white;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }

    .brand-panel::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
      pointer-events: none;
    }

    .logo-area {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 80px;
      position: relative;
      z-index: 1;
    }

    .logo-area img {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      object-fit: cover;
    }

    .logo-area h2 {
      font-size: 20px;
      font-weight: 800;
      letter-spacing: 1px;
    }

    .logo-area p {
      font-size: 11px;
      opacity: 0.7;
      margin-top: 4px;
    }

    .brand-content {
      position: relative;
      z-index: 1;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .brand-content h1 {
      font-size: 42px;
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 20px;
      background: linear-gradient(135deg, #fff, #a78bfa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .brand-content p {
      font-size: 16px;
      line-height: 1.6;
      opacity: 0.8;
      margin-bottom: 40px;
      max-width: 80%;
    }

    .features-list {
      list-style: none;
    }

    .features-list li {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .features-list li span:first-child {
      width: 32px;
      height: 32px;
      background: rgba(255,255,255,0.15);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .form-panel {
      flex: 1.2;
      padding: 60px 50px;
      background: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .form-header h2 {
      font-size: 32px;
      font-weight: 800;
      color: #1f2937;
      margin-bottom: 10px;
    }

    .form-header p {
      color: #6b7280;
      font-size: 14px;
    }

    .form-tabs {
      display: flex;
      gap: 12px;
      background: #f3f4f6;
      padding: 6px;
      border-radius: 60px;
      margin-bottom: 35px;
    }

    .tab-btn {
      flex: 1;
      padding: 12px 24px;
      border: none;
      background: transparent;
      font-size: 14px;
      font-weight: 600;
      font-family: inherit;
      border-radius: 60px;
      cursor: pointer;
      transition: all 0.3s ease;
      color: #6b7280;
    }

    .tab-btn.active {
      background: white;
      color: #5b21b6;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-group {
      margin-bottom: 25px;
    }

    label {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: #374151;
      margin-bottom: 8px;
    }

    input {
      width: 100%;
      padding: 14px 16px;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      font-size: 15px;
      font-family: inherit;
      transition: all 0.2s;
    }

    input:focus {
      outline: none;
      border-color: #5b21b6;
      box-shadow: 0 0 0 3px rgba(91,33,182,0.1);
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #5b21b6, #7c3aed);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(91,33,182,0.3);
    }

    .form-footer {
      text-align: center;
      margin-top: 30px;
      padding-top: 25px;
      border-top: 1px solid #e5e7eb;
    }

    .form-footer p {
      font-size: 14px;
      color: #6b7280;
    }

    .form-footer a {
      color: #5b21b6;
      text-decoration: none;
      font-weight: 600;
      cursor: pointer;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 12px;
      margin-bottom: 25px;
      font-size: 13px;
    }

    .alert-error {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .form-wrapper {
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-wrapper.hidden {
      display: none;
    }

    .btn-back-home {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 20px;
      background: #f3f4f6;
      color: #4b5563;
      text-decoration: none;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: 1px solid #e5e7eb;
      width: 100%;
      margin-top: 20px;
    }

    .btn-back-home:hover {
      background: #5b21b6;
      color: white;
      border-color: #5b21b6;
      transform: translateX(-3px);
    }

    .btn-back-home:active {
      transform: translateX(0);
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(30px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .form-wrapper.active {
      animation: slideInRight 0.4s ease forwards;
    }

    @media (max-width: 1024px) {
      .auth-container {
        flex-direction: column;
        max-width: 550px;
      }
      
      .brand-panel {
        padding: 40px 30px;
      }
      
      .brand-content h1 {
        font-size: 32px;
      }
      
      .brand-content p {
        max-width: 100%;
      }
      
      .form-panel {
        padding: 40px 30px;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 20px 15px;
      }
      
      .form-panel {
        padding: 30px 20px;
      }
      
      .form-header h2 {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>
<div class="auth-container">
  <div class="brand-panel">
    <div class="logo-area">
      <img src="../images/QCU-logo.png" alt="QCU Logo" />
      <div>
        <h2>QCUS-PORTAL</h2>
        <p>Quezon City University</p>
      </div>
    </div>
    <div class="brand-content">
      <h1>Welcome Back</h1>
      <p>Access your academic dashboard, view your grades, schedule, and manage your student account.</p>
      <ul class="features-list">
        <li><span>📊</span> Real-time grade tracking</li>
        <li><span>📅</span> Class schedule management</li>
        <li><span>🪪</span> Digital student ID card</li>
        <li><span>🔔</span> Event notifications</li>
      </ul>
    </div>
  </div>

  <div class="form-panel">
    <div class="form-header">
      <h2>Login to Portal</h2>
      <p>Enter your credentials to access your account</p>
    </div>

    <div class="form-tabs">
      <button class="tab-btn active" id="showLoginBtn">Login</button>
      <button class="tab-btn" id="showSignupBtn">Create Account</button>
    </div>

    <!-- Login Form -->
    <div id="loginForm" class="form-wrapper active">
      <form action="../landingpage/login-process.php" method="POST">
        <?php if (isset($_SESSION['login_error'])): ?>
          <div class="alert alert-error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
          <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['signup_success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_SESSION['signup_success']) ?></div>
          <?php unset($_SESSION['signup_success']); ?>
        <?php endif; ?>

        <div class="form-group">
          <label>Student ID Number</label>
          <input type="text" name="student_id" placeholder="Enter your student ID" required>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit">Login to Portal</button>

        <div class="form-footer">
          <p>Don't have an account? <a id="switchToSignup">Create an account</a></p>
          <p style="margin-top: 15px;"><a href="#" style="font-size: 12px;">Forgot password? Contact IT Support</a></p>
        </div>
      </form>
      <a href="home.php" class="btn-back-home">← Go Back to Home Page</a>
    </div>

    <!-- Signup Form (hidden initially) -->
    <div id="signupForm" class="form-wrapper hidden">
      <div style="text-align: center; padding: 20px 0;">
        <p style="margin-bottom: 20px;">Click the button below to create your account</p>
        <a href="sign-up.php" class="btn-submit" style="display: inline-block; width: auto; padding: 12px 30px; text-decoration: none;">Create New Account →</a>
      </div>
      <div class="form-footer">
        <p>Already have an account? <a id="switchToLogin">Login here</a></p>
      </div>
      <a href="home.php" class="btn-back-home">← Go Back to Home Page</a>
    </div>
  </div>
</div>

<script>
  const loginForm = document.getElementById('loginForm');
  const signupForm = document.getElementById('signupForm');
  const showLoginBtn = document.getElementById('showLoginBtn');
  const showSignupBtn = document.getElementById('showSignupBtn');
  const switchToSignup = document.getElementById('switchToSignup');
  const switchToLogin = document.getElementById('switchToLogin');

  function showForm(formType) {
    if (formType === 'login') {
      signupForm.classList.add('hidden');
      loginForm.classList.remove('hidden');
      signupForm.classList.remove('active');
      loginForm.classList.add('active');
      showLoginBtn.classList.add('active');
      showSignupBtn.classList.remove('active');
    } else {
      loginForm.classList.add('hidden');
      signupForm.classList.remove('hidden');
      loginForm.classList.remove('active');
      signupForm.classList.add('active');
      showSignupBtn.classList.add('active');
      showLoginBtn.classList.remove('active');
    }
  }

  showLoginBtn.addEventListener('click', () => showForm('login'));
  showSignupBtn.addEventListener('click', () => showForm('signup'));
  if (switchToSignup) switchToSignup.addEventListener('click', () => showForm('signup'));
  if (switchToLogin) switchToLogin.addEventListener('click', () => showForm('login'));
</script>
</body>
</html>