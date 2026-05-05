<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Create Account</title>
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

    /* Container with animation */
    .auth-container {
      width: 100%;
      max-width: 1400px;
      min-height: 800px;
      background: #fff;
      border-radius: 32px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      display: flex;
      overflow: hidden;
      position: relative;
    }

    /* Left Panel - Branding */
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

    /* Right Panel - Forms */
    .form-panel {
      flex: 1.2;
      padding: 40px 50px;
      overflow-y: auto;
      background: #fff;
    }

    .form-panel::-webkit-scrollbar {
      width: 6px;
    }

    .form-panel::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .form-panel::-webkit-scrollbar-thumb {
      background: #c7d2fe;
      border-radius: 3px;
    }

    /* Form Header */
    .form-header {
      text-align: center;
      margin-bottom: 35px;
    }

    .form-header h2 {
      font-size: 28px;
      font-weight: 800;
      color: #1f2937;
      margin-bottom: 8px;
    }

    .form-header p {
      color: #6b7280;
      font-size: 14px;
    }

    /* Form Tabs (Login/Signup switcher) */
    .form-tabs {
      display: flex;
      gap: 12px;
      background: #f3f4f6;
      padding: 6px;
      border-radius: 60px;
      margin-bottom: 30px;
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

    .tab-btn:hover:not(.active) {
      color: #5b21b6;
    }

    /* Form Container for animation */
    .form-container {
      position: relative;
      min-height: 500px;
    }

    .form-wrapper {
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .form-wrapper.hidden {
      display: none;
    }

    /* Form Styles */
    .form-group {
      margin-bottom: 20px;
    }
    /* Back to Home Button */
/* Back to Home Button */
.btn-back-home {
  display: inline-flex;
  align-items: center;
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
  margin-bottom: 20px;
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
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .form-row-3 {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 20px;
    }

    label {
      display: block;
      font-size: 12px;
      font-weight: 700;
      color: #374151;
      margin-bottom: 6px;
      letter-spacing: 0.3px;
    }

    label .required {
      color: #ef4444;
      margin-left: 3px;
    }

    input, select, textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      font-size: 14px;
      font-family: inherit;
      transition: all 0.2s;
      background: #fff;
    }

    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: #5b21b6;
      box-shadow: 0 0 0 3px rgba(91,33,182,0.1);
    }

    input.error {
      border-color: #ef4444;
    }

    .error-message {
      font-size: 11px;
      color: #ef4444;
      margin-top: 5px;
      display: block;
    }

    .input-hint {
      font-size: 11px;
      color: #9ca3af;
      margin-top: 5px;
    }

    /* Password strength */
    .password-strength {
      margin-top: 8px;
    }

    .strength-bar {
      height: 4px;
      background: #e5e7eb;
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 5px;
    }

    .strength-fill {
      height: 100%;
      width: 0%;
      transition: width 0.3s, background 0.3s;
    }

    .strength-text {
      font-size: 11px;
      color: #6b7280;
    }

    /* Submit Button */
    .btn-submit {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #5b21b6, #7c3aed);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 15px;
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

    .btn-submit:active {
      transform: translateY(0);
    }

    /* Footer Links */
    .form-footer {
      text-align: center;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid #e5e7eb;
    }

    .form-footer p {
      font-size: 13px;
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

    /* Alert Messages */
    .alert {
      padding: 14px 18px;
      border-radius: 12px;
      margin-bottom: 20px;
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

    /* Responsive */
    @media (max-width: 1024px) {
      .auth-container {
        flex-direction: column;
        max-width: 600px;
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
        padding: 30px;
      }
      
      .form-row, .form-row-3 {
        grid-template-columns: 1fr;
        gap: 15px;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 20px 15px;
      }
      
      .form-panel {
        padding: 25px 20px;
      }
      
      .form-header h2 {
        font-size: 24px;
      }
    }

    /* Animation for form switching */
    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .form-wrapper.active {
      animation: slideInRight 0.4s ease forwards;
    }
  </style>
</head>
<body>
<div class="auth-container">
  <!-- Left Panel - Branding -->
  <div class="brand-panel">
    <div class="logo-area">
      <img src="../images/QCU-logo.png" alt="QCU Logo" />
      <div>
        <h2>QCUS-PORTAL</h2>
        <p>Quezon City University</p>
      </div>
    </div>
    <div class="brand-content">
      <h1>Create Account</h1>
      <p>Join the QCU student portal to access your academic records, schedule, grades, and more.</p>
      <ul class="features-list">
        <li><span>📊</span> View your grades and GPA</li>
        <li><span>📅</span> Access class schedule</li>
        <li><span>🪪</span> Digital Student ID</li>
        <li><span>📝</span> Track academic progress</li>
        <li><span>🔔</span> Stay updated with events</li>
      </ul>
    </div>
  </div>

  <!-- Right Panel - Forms -->
  <div class="form-panel">
    <div class="form-header">
      <h2>Get Started</h2>
      <p>Fill in your details to create your account</p>
    </div>

    <!-- Tab Switcher -->
    <div class="form-tabs">
      <button class="tab-btn" id="showLoginBtn">Login</button>
      <button class="tab-btn active" id="showSignupBtn">Create Account</button>
    </div>

    <!-- Signup Form -->
    <div id="signupForm" class="form-wrapper active">
      <form action="signup-process.php" method="POST" onsubmit="return validateSignup()">
        <!-- Student ID -->
        <div class="form-group">
          <label>Student ID Number <span class="required">*</span></label>
          <input type="text" name="student_id" id="student_id" placeholder="25-****" required>
          <div class="input-hint">Format: 2X-XXXX (e.g., 24-1234)</div>
        </div>

        <!-- Name Fields -->
        <div class="form-row">
          <div class="form-group">
            <label>First Name <span class="required">*</span></label>
            <input type="text" name="first_name" id="first_name" required>
          </div>
          <div class="form-group">
            <label>Last Name <span class="required">*</span></label>
            <input type="text" name="last_name" id="last_name" required>
          </div>
        </div>

        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" name="middle_name" id="middle_name">
        </div>

        <!-- Email -->
        <div class="form-group">
          <label>QCU Email Address <span class="required">*</span></label>
          <input type="email" name="email" id="email" placeholder="your.name@qcu.edu.ph" required>
          <div class="input-hint">Use your official school-issued email address</div>
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label>Phone Number <span class="required">*</span></label>
          <input type="tel" name="phone_number" id="phone_number" placeholder="+63 912 345 6789" required>
        </div>

        <!-- Address -->
        <div class="form-group">
          <label>Address <span class="required">*</span></label>
          <textarea name="address" id="address" rows="2" placeholder="123 Main Street, Quezon City, Philippines" required></textarea>
        </div>

        <!-- Birthday and Gender -->
        <div class="form-row">
          <div class="form-group">
            <label>Birthday <span class="required">*</span></label>
            <input type="date" name="birthday" id="birthday" required>
          </div>
          <div class="form-group">
            <label>Gender <span class="required">*</span></label>
            <select name="gender" id="gender" required>
              <option value="">Select your gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
              <option value="Prefer not to say">Prefer not to say</option>
            </select>
          </div>
        </div>

        <!-- Course and Year Level -->
        <div class="form-row">
          <div class="form-group">
            <label>Course <span class="required">*</span></label>
            <select name="course" id="course" required>
              <option value="">Select your course</option>
              <option value="Bachelor of Science in Computer Science">BS Computer Science</option>
              <option value="Bachelor of Science in Information Technology">BS Information Technology</option>
              <option value="Bachelor of Science in Business Administration">BS Business Administration</option>
              <option value="Bachelor of Science in Accountancy">BS Accountancy</option>
              <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
              <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
              <option value="Bachelor of Science in Psychology">BS Psychology</option>
              <option value="Bachelor of Science in Civil Engineering">BS Civil Engineering</option>
              <option value="Bachelor of Science in Electrical Engineering">BS Electrical Engineering</option>
            </select>
          </div>
          <div class="form-group">
            <label>Year Level <span class="required">*</span></label>
            <select name="year_level" id="year_level" required>
              <option value="">Select your year level</option>
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="4th Year">4th Year</option>
            </select>
          </div>
        </div>

        <!-- Section -->
        <div class="form-group">
          <label>Section <span class="required">*</span></label>
          <select name="section" id="section" required>
            <option value="">Select your section</option>
            <option value="SBIT-1A">SBIT-1A</option>
            <option value="SBIT-1B">SBIT-1B</option>
            <option value="SBIT-1C">SBIT-1C</option>
            <option value="SBIT-1D">SBIT-1D</option>
            <option value="SBIT-1E">SBIT-1E</option>
            <option value="SBIT-1F">SBIT-1F</option>
          </select>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password <span class="required">*</span></label>
          <input type="password" name="password" id="password" required onkeyup="checkPasswordStrength()">
          <div class="password-strength">
            <div class="strength-bar">
              <div class="strength-fill" id="strengthFill"></div>
            </div>
            <span class="strength-text" id="strengthText"></span>
          </div>
          <div class="input-hint">At least 8 characters with numbers and symbols</div>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <label>Confirm Password <span class="required">*</span></label>
          <input type="password" name="confirm_password" id="confirm_password" required>
        </div>

        <button type="submit" class="btn-submit">Create Account</button>

        <div class="form-footer">
          <p>Already have an account? <a id="switchToLogin">Login here</a></p>

        </div>
         </div>
  <div style="margin-top: 15px;">
    <a href="home.php" class="btn-back-home" style="justify-content: center; width: 100%;">
      ← Go Back to Home Page
    </a>
  </div>
</div>
</div>
      </form>
    </div>

    <!-- Login Form (hidden initially) -->
    <div id="loginForm" class="form-wrapper hidden">
      <form action="../landingpage/login.php" method="POST" onsubmit="return validateLogin()">
        <div class="form-group">
          <label>Student ID Number <span class="required">*</span></label>
          <input type="text" name="student_id" id="login_student_id" placeholder="Enter your student ID" required>
        </div>

        <div class="form-group">
          <label>Password <span class="required">*</span></label>
          <input type="password" name="password" id="login_password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit">Login to Portal</button>

        <div class="form-footer">
          <p>Don't have an account? <a id="switchToSignup">Create an account</a></p>
          <p style="margin-top: 12px;"><a href="#" style="font-size: 12px;">Forgot password?</a></p>
        </div>

        <!-- Add this button anywhere inside the form-panel div, e.g., above the form-header -->
    <div class="form-footer">
  <p>Don't have an account? <a id="switchToSignup">Create an account</a></p>
  <div style="margin-top: 15px;">
    <a href="home.php" class="btn-back-home" style="justify-content: center; width: 100%;">
      ← Go Back to Home Page
    </a>
  </div>
</div>

      </form>
    </div>
  </div>
</div>

<script>
  // Form switching with animation
  const signupForm = document.getElementById('signupForm');
  const loginForm = document.getElementById('loginForm');
  const showLoginBtn = document.getElementById('showLoginBtn');
  const showSignupBtn = document.getElementById('showSignupBtn');
  const switchToLogin = document.getElementById('switchToLogin');
  const switchToSignup = document.getElementById('switchToSignup');

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
  if (switchToLogin) switchToLogin.addEventListener('click', () => showForm('login'));
  if (switchToSignup) switchToSignup.addEventListener('click', () => showForm('signup'));

  // Password strength checker
  function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    let message = '';
    let color = '#ef4444';
    let width = '0%';
    
    if (password.length > 0) {
      if (password.length >= 8) strength++;
      if (password.match(/[a-z]/)) strength++;
      if (password.match(/[A-Z]/)) strength++;
      if (password.match(/[0-9]/)) strength++;
      if (password.match(/[^a-zA-Z0-9]/)) strength++;
    }
    
    switch(strength) {
      case 0:
      case 1:
        message = 'Weak password';
        color = '#ef4444';
        width = '25%';
        break;
      case 2:
        message = 'Fair password';
        color = '#f59e0b';
        width = '50%';
        break;
      case 3:
        message = 'Good password';
        color = '#3b82f6';
        width = '75%';
        break;
      case 4:
      case 5:
        message = 'Strong password';
        color = '#10b981';
        width = '100%';
        break;
      default:
        message = '';
        width = '0%';
    }
    
    strengthFill.style.width = width;
    strengthFill.style.background = color;
    strengthText.textContent = message;
  }

  // Validate Signup Form
  function validateSignup() {
    let isValid = true;
    const errors = [];
    
    // Student ID validation
    const studentId = document.getElementById('student_id').value;
    const studentIdPattern = /^\d{2}-\d{4}$/;
    if (!studentIdPattern.test(studentId)) {
      errors.push('Student ID must be in format: XX-XXXX (e.g., 24-1234)');
      isValid = false;
    }
    
    // Name validation
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    if (firstName.length < 2) {
      errors.push('First name must be at least 2 characters');
      isValid = false;
    }
    if (lastName.length < 2) {
      errors.push('Last name must be at least 2 characters');
      isValid = false;
    }
    
    // Email validation
    const email = document.getElementById('email').value;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@qcu\.edu\.ph$/;
    if (!emailPattern.test(email)) {
      errors.push('Email must be a valid @qcu.edu.ph address');
      isValid = false;
    }
    
    // Phone validation
    const phone = document.getElementById('phone_number').value;
    const phonePattern = /^(\+63|0)[0-9]{10}$/;
    if (!phonePattern.test(phone.replace(/[\s-]/g, ''))) {
      errors.push('Phone number must be valid Philippine number');
      isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('password').value;
    if (password.length < 8) {
      errors.push('Password must be at least 8 characters');
      isValid = false;
    }
    if (!password.match(/[0-9]/)) {
      errors.push('Password must contain at least one number');
      isValid = false;
    }
    if (!password.match(/[^a-zA-Z0-9]/)) {
      errors.push('Password must contain at least one special character');
      isValid = false;
    }
    
    // Confirm password
    const confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) {
      errors.push('Passwords do not match');
      isValid = false;
    }
    
    // Course and Section validation
    const course = document.getElementById('course').value;
    const section = document.getElementById('section').value;
    if (!course || course === '') {
      errors.push('Please select a course');
      isValid = false;
    }
    if (!section || section === '') {
      errors.push('Please select a section');
      isValid = false;
    }
    
    if (!isValid) {
      alert(errors.join('\n'));
    }
    
    return isValid;
  }
  
  // Validate Login Form
  function validateLogin() {
    const studentId = document.getElementById('login_student_id').value;
    const password = document.getElementById('login_password').value;
    
    if (!studentId || studentId === '') {
      alert('Please enter your Student ID');
      return false;
    }
    if (!password || password === '') {
      alert('Please enter your password');
      return false;
    }
    return true;
  }
  
  // Student ID auto-format
  const studentIdInput = document.getElementById('student_id');
  if (studentIdInput) {
    studentIdInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/[^\d]/g, '');
      if (value.length > 2) {
        value = value.slice(0, 2) + '-' + value.slice(2, 6);
      }
      e.target.value = value;
    });
  }
  
  // Phone number auto-format
  const phoneInput = document.getElementById('phone_number');
  if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/[^\d+]/g, '');
      if (value.startsWith('63')) {
        value = '+' + value;
      }
      e.target.value = value;
    });
  }
</script>
</body>
</html>