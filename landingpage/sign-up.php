<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../styles/signup-style.css">
    <link rel="icon" type="image/png" href="../images/QCU-logo.png">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Student Portal — Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --orange-dark:  #7a2800;
      --orange-mid:   #c04a00;
      --orange-light: #e06020;
      --gold:         #c9991f;
      --gold-hover:   #b38518;
      --white:        #ffffff;
      --gray-200:     #e5e7eb;
      --gray-300:     #d1d5db;
      --gray-400:     #9ca3af;
      --gray-500:     #6b7280;
      --gray-700:     #374151;
      --gray-900:     #111827;
      --radius-panel: 14px;
      --radius-input: 8px;
    }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f0fa;
      font-family: 'Inter', sans-serif;
      padding: 2rem 1rem;
    }
 
    .wrapper {
      display: flex;
      align-items: stretch;
      gap: 20px;
      width: 100%;
      max-width: 950px;
    }
 
    /* ── LEFT — FORM PANEL ── */
    .left {
      flex: 1;
      background: var(--white);
      border-radius: var(--radius-panel);
      padding: 2.6rem 2.8rem;
      box-shadow: 0 8px 32px rgba(0,0,0,0.10);
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
 
    .form-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.7rem;
      font-weight: 800;
      color: var(--gray-900);
      margin-bottom: 4px;
    }
    .form-subtitle { font-size: 0.82rem; color: var(--gray-500); margin-bottom: 1.5rem; }
 
    .field-group { margin-bottom: 1rem; }
    .field-label {
      display: flex;
      align-items: center;
      gap: 3px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 5px;
    }
    .req { color: #e53e3e; font-size: 0.75rem; }
 
    .field-hint { font-size: 0.72rem; color: var(--gray-400); margin-top: 4px; }
 
    .input-wrap { position: relative; display: flex; align-items: center; }
    .field-input {
      width: 100%;
      padding: 9px 36px 9px 12px;
      border: 1.5px solid var(--gray-300);
      border-radius: var(--radius-input);
      font-size: 0.855rem;
      color: var(--gray-900);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
      font-family: 'Inter', sans-serif;
      background: var(--white);
    }
    .field-input::placeholder { color: #c0c4cc; }
    .field-input:focus { border-color: var(--orange-mid); box-shadow: 0 0 0 3px rgba(192,74,0,0.10); }
 
    .eye-btn {
      position: absolute; right: 10px;
      background: none; border: none; cursor: pointer;
      color: var(--gray-400); display: flex; padding: 0;
    }
    .eye-btn svg { width: 15px; height: 15px; }
 
    /* Two-col row for first/last name */
    .row-two { display: flex; gap: 12px; }
    .row-two .field-group { flex: 1; }
 
    /* Terms row */
    .terms-row {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      margin-bottom: 1.2rem;
    }
    .terms-row input[type="checkbox"] {
      margin-top: 2px;
      width: 14px; height: 14px;
      accent-color: var(--orange-mid);
      cursor: pointer;
      flex-shrink: 0;
    }
    .terms-row span { font-size: 0.78rem; color: var(--gray-700); line-height: 1.5; }
    .terms-row a { color: var(--orange-mid); text-decoration: none; font-weight: 600; }
    .terms-row a:hover { text-decoration: underline; }
 
    .btn-create {
      width: 100%;
      padding: 11px;
      background: var(--gold);
      color: var(--white);
      border: none;
      border-radius: var(--radius-input);
      font-size: 0.92rem;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
      letter-spacing: 0.4px;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
    }
    .btn-create:hover { background: var(--gold-hover); }
    .btn-create:active { transform: scale(0.98); }
 
    .divider {
      display: flex; align-items: center; gap: 10px;
      margin: 1rem 0; color: var(--gray-400); font-size: 0.76rem;
    }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--gray-200); }
 
    .btn-login {
      width: 100%;
      padding: 10px;
      background: transparent;
      color: var(--gray-900);
      border: 1.5px solid var(--gray-900);
      border-radius: var(--radius-input);
      font-size: 0.92rem;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    .btn-login:hover { background: var(--gray-900); color: var(--white); }
 
    .back-home {
      display: block; text-align: center; margin-top: 0.9rem;
      font-size: 0.78rem; color: var(--gray-500); text-decoration: none;
    }
    .back-home:hover { color: var(--orange-mid); }
    .back-home::before { content: '← '; }
 
    /* ── RIGHT — INFO PANEL ── */
    .right {
      flex: 0 0 38%;
      border-radius: var(--radius-panel);
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.8rem;
      overflow: hidden;
      color: #fff;
      box-shadow: 0 16px 48px rgba(120,40,0,0.30);
      /* Fallback solid bg */
      background: linear-gradient(160deg, #c04a00 0%, #e06020 45%, #a83a00 100%);
    }
 
    /* Background image placeholder */
    .right-bg {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
      /* Replace src="YOUR_BACKGROUND_IMAGE_HERE.png" */
    }
 
    /* Orange overlay on top of bg image */
    .right-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(160,50,0,0.88) 0%, rgba(210,80,10,0.82) 50%, rgba(140,36,0,0.90) 100%);
      z-index: 1;
    }
    
 
    .year-mark {
      position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%);
      font-family:'Montserrat',sans-serif; font-size:38px; font-weight:800;
      color:rgba(255,255,255,0.06); letter-spacing:4px; white-space:nowrap;
      pointer-events:none; user-select:none; z-index:2;
    }
 
    /* Logo */
    .logo-wrap { position:relative; z-index:3; margin-bottom:1rem; }
    .logo-circle {
      width: 88px; height: 88px; border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.6);
      background: rgba(255,255,255,0.1);
      overflow: hidden; display: flex; align-items: center; justify-content: center;
    }
    .logo-circle img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
 
    .right-content { position: relative; z-index: 3; text-align: center; width: 100%; }
 
    .join-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.55rem;
      font-weight: 800;
      margin-bottom: 4px;
    }
    .univ-name {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      opacity: 0.85;
      margin-bottom: 1px;
    }
    .portal-label {
      font-size: 0.65rem;
      letter-spacing: 3px;
      text-transform: uppercase;
      opacity: 0.6;
      margin-bottom: 1.6rem;
    }
 
    .steps-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 0.85rem;
      font-weight: 700;
      margin-bottom: 1rem;
      opacity: 0.95;
    }
 
    .steps { list-style: none; text-align: left; display: flex; flex-direction: column; gap: 10px; }
    .steps li { display: flex; align-items: flex-start; gap: 10px; }
 
    .step-num {
      flex-shrink: 0;
      width: 22px; height: 22px;
      border-radius: 50%;
      background: rgba(255,255,255,0.2);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Montserrat', sans-serif;
      font-size: 0.68rem;
      font-weight: 800;
    }
    .step-text { display: flex; flex-direction: column; }
    .step-text strong { font-size: 0.8rem; font-weight: 700; line-height: 1.3; }
    .step-text span { font-size: 0.7rem; opacity: 0.65; line-height: 1.3; }
 
    @media (max-width: 700px) {
      .wrapper { flex-direction: column; gap: 14px; max-width: 440px; }
      .right { flex: none; min-height: 280px; }
      .left { padding: 2rem 1.5rem; }
    }
  </style>
</head>
<body>

<div class="wrapper">

  <!-- ── LEFT — FORM ── -->
  <div class="left">
    <h2 class="form-title">Create Account</h2>
    <p class="form-subtitle">Fill in your details to get started</p>

    <!-- START PHP-READY FORM -->
    <!-- action points to your PHP processing script (customize path as needed) -->
    <!-- method="POST" keeps sensitive data like passwords out of the URL -->
    <form action="signup-process.php" method="POST" id="signupForm" class="signup-form">
      <!-- Student ID -->
      <div class="field-group">
        <label class="field-label" for="studentId">Student ID Number <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="studentId" for PHP access -->
          <input class="field-input" type="text" id="studentId" name="studentId" placeholder="2X-XXXX" autocomplete="off" />
        </div>
        <p class="field-hint">Check your admission documents or visit the Registrar's Office</p>
      </div>

      <!-- First & Last Name -->
      <div class="row-two">
        <div class="field-group">
          <label class="field-label" for="firstName">First Name <span class="req">*</span></label>
          <div class="input-wrap">
            <!-- Added name="firstName" -->
            <input class="field-input" type="text" id="firstName" name="firstName" placeholder="Juan" autocomplete="given-name" required/>
          </div>
        </div>
        <div class="field-group">
          <label class="field-label" for="lastName">Last Name <span class="req">*</span></label>
          <div class="input-wrap">
            <!-- Added name="lastName" -->
            <input class="field-input" type="text" id="lastName" name="lastName" placeholder="Dela Cruz" autocomplete="family-name" required/>
          </div>
        </div>
      </div>

      <!-- Middle Name -->
      <div class="field-group">
        <label class="field-label" for="middleName">Middle Name</label>
        <div class="input-wrap">
          <!-- Added name="middleName" -->
          <input class="field-input" type="text" id="middleName" name="middleName" placeholder="Santos" autocomplete="additional-name" required/>
        </div>
      </div>

      <!-- QCU Email -->
      <div class="field-group">
        <label class="field-label" for="email">QCU Email Address <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="email" -->
          <input class="field-input" type="email" id="email" name="email" placeholder="your.name@qcu.edu.ph" autocomplete="email" />
        </div>
        <p class="field-hint">Use your official school-issued email address</p>
      </div>

      <!-- Phone Number -->
      <div class="field-group">
        <label class="field-label" for="phoneNumber">Phone Number <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="phoneNumber" -->
          <input class="field-input" type="tel" id="phoneNumber" name="phoneNumber" placeholder="+63 912 345 6789" autocomplete="tel" required/>
        </div>
      </div>

      <!-- Address -->
      <div class="field-group">
        <label class="field-label" for="address">Address <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="address" -->
          <input class="field-input" type="text" id="address" name="address" placeholder="123 Main Street, Quezon City, Philippines" autocomplete="street-address" required/>
        </div>
      </div>

      <!-- Birthday -->
      <div class="field-group">
        <label class="field-label" for="birthday">Birthday <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="birthday" -->
          <input class="field-input" type="date" id="birthday" name="birthday" autocomplete="bday" required/>
        </div>
      </div>
      <div class="form-group">
        <label for="gender">Gender</label>
        <select class="field-input" id="gender" name="gender">
      <option value="" disabled selected>Select your gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Non-binary">Non-binary</option>
      <option value="Prefer not to say">Prefer not to say</option>
      </select>
        </div>

      <!-- Course Dropdown -->
      <div class="field-group">
        <label class="field-label" for="course">Course <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="course" -->
          <select class="field-input" id="course" name="course" autocomplete="off">
            <option value="">Select your course</option>
                  <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                  <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                  <option value="Bachelor of Science in Accountancy">Bachelor of Science in Accountancy</option>
                  <option value="Bachelor of Science in Engineering">Bachelor of Science in Engineering</option>       
          </select>
        </div>
      </div>

      <!-- Year Level Dropdown -->
      <div class="field-group">
        <label class="field-label" for="yearLevel">Year Level <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="yearLevel" -->
          <select class="field-input" id="yearLevel" name="yearLevel" autocomplete="off">
            <option value="">Select your year level</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
          </select>
        </div>
      </div>

      <!-- Section Dropdown -->
      <div class="field-group">
        <label class="field-label" for="section">Section <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="section" -->
          <select class="field-input" id="section" name="section" autocomplete="off" required>
            <option value="">Select your section</option>
            <option value="SBIT-1A">SBIT-1A</option>
            <option value="SBIT-1B">SBIT-1B</option>
            <option value="SBIT-1C">SBIT-1C</option>
            <option value="SBIT-1D">SBIT-1D</option>
            <option value="SBIT-1E">SBIT-1E</option>
            <option value="SBIT-1F">SBIT-1F</option>
          </select>
        </div>
      </div>

      <!-- Password -->
      <div class="field-group">
        <label class="field-label" for="password">Password <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="password" -->
          <input class="field-input" type="password" id="password" name="password" placeholder="Create a strong password" autocomplete="new-password" required/>
          <button class="eye-btn" id="eyeBtn1" type="button" aria-label="Toggle password">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2"/></svg>
          </button>
        </div>
        <p class="field-hint">At least 8 characters with numbers and symbols</p>
      </div>

      <!-- Confirm Password -->
      <div class="field-group">
        <label class="field-label" for="confirmPassword">Confirm Password <span class="req">*</span></label>
        <div class="input-wrap">
          <!-- Added name="confirmPassword" -->
          <input class="field-input" type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" autocomplete="new-password" required />
          <button class="eye-btn" id="eyeBtn2" type="button" aria-label="Toggle confirm password">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2"/></svg>
          </button>
        </div>
      </div>

      <!-- Terms -->
      <div class="terms-row">
        <!-- Added name="terms" and value for PHP access -->
        <input type="checkbox" id="terms" name="terms" value="agreed" required />
        <span>I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></span>
      </div>

      <!-- Changed type from "button" to "submit" to trigger form submission -->
      <button class="btn-create" type="submit">Create Account</button>
    </form>
    <!-- END PHP-READY FORM -->

    <div class="divider">Already have an account?</div>

    <a href="login.php">
        <button class="btn-login" type="button">Log In</button>
    </a>

    <a href="home.php" class="back-home">Back to Home</a>
  </div>

  <!-- ── RIGHT — INFO ── -->
  <div class="right">
    <!-- Replace src below with your background image path -->
    <img class="right-bg" src="../images/QCU-background.png" alt="" />
    <div class="right-overlay"></div>
    <div class="ring ring-1"></div>
    <div class="ring ring-2"></div>

    <!-- Replace src below with your logo path -->
    <div class="logo-wrap">
      <div class="logo-circle">
        <img src="../images/QCU-logo.png" alt="University Logo" />
      </div>
    </div>

    <div class="right-content">
      <h1 class="join-title">Join QCU Portal!</h1>
      <p class="univ-name">Quezon City University</p>
      <p class="portal-label">Student Portal</p>

      <p class="steps-title">Get Started in 4 Steps:</p>
      <ul class="steps">
        <li>
          <span class="step-num">1</span>
          <div class="step-text">
            <strong>Get Your Student ID</strong>
            <span>From admission documents</span>
          </div>
        </li>
        <li>
          <span class="step-num">2</span>
          <div class="step-text">
            <strong>Fill the Sign-Up Form</strong>
            <span>Enter your complete details</span>
          </div>
        </li>
        <li>
          <span class="step-num">3</span>
          <div class="step-text">
            <strong>Verify Your Email</strong>
            <span>Check your QCU inbox</span>
          </div>
        </li>
        <li>
          <span class="step-num">4</span>
          <div class="step-text">
            <strong>Log In &amp; Explore</strong>
            <span>Access all features</span>
          </div>
        </li>
      </ul>
    </div>
  </div>

</div>

<!-- Fixed typo in filename: singup → signup -->
<script>
    function toggleEye(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    const open = `<path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/><circle cx="8" cy="8" r="2"/>`;
    const shut = `<line x1="1" y1="1" x2="15" y2="15"/><path d="M6.5 6.5A2 2 0 0010 10M4.2 4.2A7 7 0 001 8s2.5 5 7 5a6.9 6.9 0 003.8-1.2M9.9 3.2A7 7 0 0115 8s-.5 1-1.5 2.2"/>`;
    let vis = false;
    btn.addEventListener('click', () => {
      vis = !vis;
      inp.type = vis ? 'text' : 'password';
      btn.querySelector('svg').innerHTML = vis ? shut : open;
    });
  }
  toggleEye('eyeBtn1', 'password');
  toggleEye('eyeBtn2', 'confirmPassword');
</script>

</body>
</html>