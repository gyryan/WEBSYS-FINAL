<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/png" href="../images/QCU-logo.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>QCU Student Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Lora:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
<style>
  html {
  scroll-behavior: smooth;
}
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
  :root {
    --maroon: #6b0f1a;
    --gold: #b8962e;
    --gold-light: #c9a84c;
    --purple: #4a2060;
    --dark: #111;
    --gray: #555;
    --light-gray: #f5f5f5;
    --white: #fff;
  }
 
  body {
    font-family: 'Montserrat', sans-serif;
    color: var(--dark);
    background: var(--white);
  }
 
  /* ── NAVBAR ── */
  nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 60px;
    background: var(--white);
    border-bottom: 1px solid #e8e8e8;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
  }
 
  .nav-brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }
 
  .nav-logo {
 width: 52px;
  height: 52px;
  border-radius: 50%;
  overflow: hidden; /* IMPORTANT: keeps image inside circle */
  display: flex;
  align-items: center;
  justify-content: center;
  background: none; /* remove gradient if you want pure image */
  }
  .nav-logo img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* makes image fill nicely */
}
  .nav-brand-text {
    display: flex;
    flex-direction: column;
  }
 
  .nav-brand-text .uni {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .5px;
    color: var(--dark);
  }
 
  .nav-brand-text .sub {
    font-size: 10px;
    letter-spacing: 1.5px;
    color: var(--gray);
    text-transform: uppercase;
  }
 
  .nav-links {
    display: flex;
    gap: 36px;
    list-style: none;
  }
 
  .nav-links a {
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .4px;
    color: var(--dark);
    transition: color .2s;
  }
 
  .nav-links a:hover { color: var(--gold); }
 
  .nav-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }
 
  .btn-outline {
    padding: 9px 22px;
    border: 2px solid var(--dark);
    background: transparent;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s;
  }
 
  .btn-outline:hover { background: var(--dark); color: var(--white); }
 
  .btn-solid {
    padding: 9px 22px;
    border: none;
    background: var(--dark);
    color: var(--white);
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background .2s;
  }
 
  .btn-solid:hover { background: #333; }
 
  /* ── HERO ── */
  .hero {
    position: relative;
    min-height: 560px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background: #1a1a2e;
  }
 
  .hero-bg {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(to right, rgba(0,0,0,.65) 50%, rgba(0,0,0,.2) 100%),
      url('../images/QCU-background.png') center/cover no-repeat;
  }
 
  .hero-seal {
    position: absolute;
    right: -30px;
    top: 50%;
    transform: translateY(-50%);
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle at 40% 40%,
      #6b0f1a 0%, #4a2060 35%, #1a237e 60%, #1b5e20 80%, #f9a825 100%);
    opacity: .55;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 100px;
    filter: blur(1px);
  }
 
  .hero-content {
    position: relative;
    padding: 80px 60px;
    max-width: 720px;
    z-index: 2;
  }
 
  .hero-content h1 {
    font-size: clamp(38px, 5vw, 62px);
    font-weight: 900;
    color: var(--white);
    line-height: 1.1;
    letter-spacing: -1px;
    text-transform: uppercase;
    margin-bottom: 22px;
  }
 
  .hero-content p {
    font-size: 17px;
    color: rgba(255,255,255,.85);
    line-height: 1.6;
    max-width: 480px;
    margin-bottom: 36px;
  }
 
  .btn-gold {
    display: inline-block;
    padding: 16px 36px;
    background: var(--gold);
    color: var(--white);
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .2s, transform .15s;
    text-decoration: none;
  }
  .btn-home{
    display: inline-block;
    padding: 16px 36px;
    color: var(--white);
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .2s, transform .15s;
    text-decoration: none;
    border: 2px solid white;
  }
  .btn-home:hover{
   background-color: white;
   color: black;
  }
 
  .btn-gold:hover { background: var(--gold-light); transform: translateY(-2px); }
 
  /* ── STATS BAR ── */
  .stats-bar {
    background: rgb(238, 230, 187);
    display: flex;
    justify-content: center;
    gap: 0;
    overflow: hidden;
  }
 
  .stat-item {
    flex: 1;
    max-width: 320px;
    padding: 48px 40px;
    text-align: center;
    border-right: 1px solid rgba(0,0,0,.08);
  }
 
  .stat-item:last-child { border-right: none; }
 
  .stat-num-1 {
    font-size: 48px;
    font-weight: 900;
    color: var(--gold);
    line-height: 1;
    margin-bottom: 8px;
    color: blue;
  }
   .stat-num-2 {
    font-size: 48px;
    font-weight: 900;
    color: var(--gold);
    line-height: 1;
    margin-bottom: 8px;
    color: red;
  }
 
  .stat-label {
    font-size: 13px;
    color: var(--gray);
    font-weight: 500;
  }
 
  /* ── ABOUT ── */
  .about {
    padding: 90px 60px;
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
  }
 
  .section-tag {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 12px;
  }
 
  .section-title {
    font-size: clamp(28px, 3vw, 42px);
    font-weight: 900;
    line-height: 1.15;
    margin-bottom: 20px;
  }
 
  .section-body {
    font-size: 15px;
    color: var(--gray);
    line-height: 1.8;
  }
 
  .about-visual {
    background: #f3f3f3;
    border-radius: 16px;
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
    color: #ccc;
    border: 2px dashed #ddd;
    flex-direction: column;
    gap: 12px;
  }
 
  .about-visual span {
    font-size: 13px;
    color: #bbb;
    font-weight: 600;
    letter-spacing: 1px;
  }
 
  /* ── ABOUT SUBSECTIONS ── */
  .about {
    padding-bottom: 0;
  }
 
  .about-sub {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    min-height: 200px;
  }
 
  .about-sub--purple {
    background: linear-gradient(135deg, #f0eaf8 0%, #fce4ec 100%);
  }
 
  .about-sub--white {
    background: var(--white);
  }
 
  .about-sub-text {
    padding: 48px 60px;
    font-size: 15px;
    color: var(--gray);
    line-height: 1.8;
    max-width: 480px;
  }
 
  .about-sub-visual {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    height: 100%;
    min-height: 220px;
  }
 
  /* Purple mockup card */
  .purple-card {
    background: linear-gradient(135deg, #7b1fa2, #e91e63);
    position: relative;
  }
 
  .mockup-card {
    background: white;
    border-radius: 12px;
    padding: 20px 24px;
    width: 260px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
  }
 
  .mockup-avatar {
    width: 36px;
    height: 36px;
    background: #e0e0e0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }
 
  .mockup-lines {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 4px;
  }
 
  .mockup-line {
    height: 10px;
    border-radius: 5px;
    background: #ddd;
  }
  .mockup-line.wide  { width: 100%; }
  .mockup-line.med   { width: 70%; }
  .mockup-line.short { width: 45%; }
 
  /* Document icon side */
  .doc-card {
    background: var(--white);
    justify-content: center;
  }
 
  .doc-icon {
    font-size: 120px;
    filter: grayscale(1) contrast(0.6);
    line-height: 1;
  }
 
  /* ── SERVICES ── */
  .services {
    padding: 80px 60px;
    background: var(--white);
  }
 
  .services h2 {
    font-size: 32px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 40px;
  }
 
  .services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    max-width: 1100px;
    margin: 0 auto;
  }
 
  .service-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 48px 24px 36px;
    text-align: center;
    transition: box-shadow .2s, transform .2s;
    cursor: pointer;
    background: var(--white);
  }
  .service-card img {
  width: 70px;
  height: auto;
  padding-bottom: 20px;
}

 
  .service-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,.1);
    transform: translateY(-4px);
  }
 
  .service-icon svg{
    
    margin-bottom: 20px;
    display: block;

  }
 
  .service-card h3 {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
  }
 
  /* ── HOW TO ACCESS ── */
  .how-to {
    padding: 90px 60px;
    background: var(--light-gray);
    text-align: center;
  }
 
  .how-to .section-tag { color: var(--gold); }
 
  .how-to .section-title {
    margin-bottom: 8px;
  }
 
  .how-to .section-title em {
    font-style: italic;
    color: var(--gold);
    font-family: 'Lora', serif;
  }
 
  .how-to > p {
    color: var(--gray);
    font-size: 14px;
    max-width: 500px;
    margin: 0 auto 60px;
    line-height: 1.7;
  }
 
  .steps {
    display: flex;
    justify-content: center;
    gap: 0;
    max-width: 1000px;
    margin: 0 auto 50px;
    position: relative;
  }
 
  .steps::before {
    content: '';
    position: absolute;
    top: 34px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(to right, #111 0%, #4a2060 33%, #b8962e 66%, #c0392b 100%);
    z-index: 0;
  }
 
  .step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    position: relative;
    z-index: 1;
  }
 
  .step-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: 900;
    color: var(--white);
  }
 
  .step:nth-child(1) .step-circle { background: #111; }
  .step:nth-child(2) .step-circle { background: linear-gradient(135deg, #111, #4a2060); }
  .step:nth-child(3) .step-circle { background: var(--gold); }
  .step:nth-child(4) .step-circle { background: #c0392b; }
 
  .step h4 {
    font-size: 13px;
    font-weight: 800;
  }
 
  .step p {
    font-size: 12px;
    color: var(--gray);
    line-height: 1.6;
    max-width: 180px;
    text-align: center;
  }
 
  .btn-gold-center {
    margin-top: 10px;
  }
 
  /* ── CONTACTS ── */
  .contacts {
    padding: 90px 60px;
    background: var(--white);
  }
 
  .contacts-inner {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
  }
 
  .contacts-inner .section-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6b0f1a, #4a2060, #1a237e);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 16px;
  }
  .section-logo img{
    width: 100%;
  height: 100%;
  object-fit: cover; /* makes image fill nicely */

  }
  
  .contacts-inner h2 {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 4px;
  }
 
  .contacts-inner .portal-sub {
    font-size: 11px;
    letter-spacing: 2.5px;
    color: var(--gray);
    text-transform: uppercase;
    margin-bottom: 24px;
  }
 
  .contacts-inner > p {
    font-size: 15px;
    color: var(--gray);
    max-width: 620px;
    margin: 0 auto 48px;
    line-height: 1.8;
  }
 
  .contact-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
 
  .contact-card {
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 36px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    background: var(--white);
  }
  .contact-card img{
    height: auto;
    width:50px;
  }
 
 
  .contact-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }
 
  .contact-icon.phone { background: var(--gold); }
  .contact-icon.email { background: var(--purple); }
  .contact-icon.loc   { background: #c0392b; }
 
  .contact-card h4 {
    font-size: 14px;
    font-weight: 800;
  }
 
  .contact-card p {
    font-size: 13px;
    color: var(--gray);
  }
 
  /* ── FOOTER ── */
  footer {
    background: var(--light-gray);
    padding: 24px;
    text-align: center;
    font-size: 12px;
    color: var(--gray);
    border-top: 1px solid #e0e0e0;
  }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <div class="nav-brand">
    <div class="nav-logo">
      <img src="../images/QCU-logo.png" alt="QCU Logo">
    </div>
    <div class="nav-brand-text">
      <span class="uni">QUEZON CITY UNIVERSITY</span>
      <span class="sub">Student Portal</span>
    </div>
  </div>
  <ul class="nav-links">
    <li><a href="#home">HOME</a></li>
    <li><a href="#about">ABOUT</a></li>
    <li><a href="#features">FEATURES</a></li>
    <li><a href="#how-to">HOW TO ACCESS</a></li>
    <li><a href="#contacts">CONTACTS</a></li>
  </ul>
  <div class="nav-actions">
    <a href="login.php">
      <button class="btn-outline">Log in</button>
    </a>
    <a href="sign-up.php">
      <button class="btn-solid">SIGN UP</button>
    </a>
  </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-bg"></div>
  <div class="hero-content">
    <h1>QCU Student Portal</h1>
    <p>Serves as the #1 International University of Employable Graduates</p>
    <a href="login.html" class="btn-home">Access QCUS-PORTAL</a>
  </div>
</section>

<!-- STATS -->
<section class="stats-bar">
  <div class="stat-item">
    <div class="stat-num-1">#1</div>
    <div class="stat-label">International University of The Philippines</div>
  </div>
  <div class="stat-item">
    <div class="stat-num-2">30+</div>
    <div class="stat-label">Years of Excellence</div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="about">
    <div>
      <div class="section-tag">ABOUT THIS WEBSITE</div>
      <h2 class="section-title">Serves Student as Manual for Qcu Students</h2>
    </div>
    <div></div>
  </div>

  <!-- Sub-section 1: text left, UI mockup right -->
  <div class="about-sub about-sub--purple">
    <div class="about-sub-text">
      <p>QCUS-PORTAL was built to change that. It is QCU's integrated online services portal. A single, secure platform where students can access everything they need without leaving their seats.</p>
    </div>
    <div class="about-sub-visual purple-card">
      <div class="mockup-card">
        <div class="mockup-avatar">👤</div>
        <div class="mockup-lines">
          <div class="mockup-line wide"></div>
          <div class="mockup-line med"></div>
          <div class="mockup-line wide"></div>
          <div class="mockup-line short"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sub-section 2: document icon left, text right -->
  <div class="about-sub about-sub--white">
    <div class="about-sub-visual doc-card">
      <div class="doc-icon">📄</div>
    </div>
    <div class="about-sub-text">
      <p>With QCUS-PORTAL, students log in using their official Student Number, and instantly gain access to their digital ID, class schedules, schedule, and a full registrar request module all in one place.</p>
      <br>
      <p>Every transaction is processed online, generates a digital receipt, and is logged in a tamper-evident audit trail, ensuring full transparency and accountability across the university.</p>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="services" id="features">
  <div style="max-width:1100px;margin:0 auto;">
    <h2>SERVICES OFFERED</h2>
    <div class="services-grid">
      <div class="service-card">
        <img src="../images/calendar-days-svgrepo-com.svg" alt="Class Schedules">
        <h3>View Class Schedules</h3>
      </div>
      <div class="service-card">
        <img src="../images/calendar-week-svgrepo-com.svg" alt="Events">
        <h3>Discover Events</h3>
      </div>
      <div class="service-card">
        <img src="../images/grades-svgrepo-com.svg" alt="Grades">
        <h3>View Grades</h3>
      </div>
      <div class="service-card">
        <img src="../images/id-card-svgrepo-com.svg" alt="Digital Student ID">
        <h3>Digital Student ID</h3>
      </div>
      <div class="service-card">
        <img src="../images/form-svgrepo-com.svg" alt="">
        <h3>Schedule</h3>
      </div>
      <div class="service-card">
        <img src="../images/customer-service-svgrepo-com.svg" alt="FAQs">
        <h3>FAQs</h3>
      </div>
    </div>
  </div>
</section>  

<!-- HOW TO ACCESS -->
<section class="how-to" id="how-to">
  <div class="section-tag">HOW TO ACCESS THE PORTAL</div>
  <h2 class="section-title">Up and Running in <em>4 Simple Steps</em></h2>
  <p>First-time users can set up their account in minutes. Follow this guide to activate your QCU Student Portal access.</p>

  <div class="steps">
    <div class="step">
      <div class="step-circle">1</div>
      <h4>Get Your Student ID</h4>
      <p>Your Student ID is issued upon enrollment confirmation. Check your admission documents or visit the Registrar's Office.</p>
    </div>
    <div class="step">
      <div class="step-circle">2</div>
      <h4>Register an Account</h4>
      <p>Click "Sign Up" and enter your Student ID, full name, and school-issued email address.</p>
    </div>
    <div class="step">
      <div class="step-circle">3</div>
      <h4>Verify Your Email</h4>
      <p>Check your QCU email for a verification link. Click it within 24 hours to activate your portal account.</p>
    </div>
    <div class="step">
      <div class="step-circle">4</div>
      <h4>Log In & Explore</h4>
      <p>Sign in and complete your profile. Your academic data syncs automatically within the hour.</p>
    </div>
  </div>

  <a href="sign-up.html" class="btn-gold btn-gold-center">Get Started Now</a>
</section>

<!-- CONTACTS -->
<section class="contacts" id="contacts">
  <div class="contacts-inner">
    <div class="section-logo">
      <img src="../images/QCU-logo.png" alt="QCU Logo">
    </div>
    <h2>QUEZON CITY UNIVERSITY</h2>
    <div class="portal-sub">STUDENT PORTAL</div>
    <p>Have questions or concerns about the University? We're here to help. Reach out to us through any of our available contact channels — whether mobile numbers, landlines, or email, there's always someone ready to assist you.</p>

    <div class="contact-cards">
      <div class="contact-card">
       <img src="../images/phone-svgrepo-com.svg" alt="">
        <h4>Phone</h4>
        <p>+63 123 456 7890</p>
      </div>
      <div class="contact-card">
        <img src="../images/email-1573-svgrepo-com.svg" alt="">
        <h4>Email</h4>
        <p>info@qcu.edu.ph</p>
      </div>
      <div class="contact-card">
        <img src="../images/location-pin-svgrepo-com.svg" alt="">
        <h4>Location</h4>
        <p>Quezon City, Philippines</p>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  © 2026. Quezon City University QCUS-PORTAL. All rights reserved.
</footer>

</body>
</html>