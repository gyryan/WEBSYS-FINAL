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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&family=Lora:ital,wght@0,400;1,600&display=swap" rel="stylesheet" />
  <style>
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
      min-height: 100vh;
    }

    /* ── BACKGROUND PAGE (home.php content) ── */
    .background-page {
      position: relative;
      min-height: 100vh;
      transition: filter 0.3s ease;
    }

    .background-page.blurred {
      filter: blur(8px);
      pointer-events: none;
      user-select: none;
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
      z-index: 99;
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
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: none;
    }
    .nav-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
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
      line-height: 1;
      margin-bottom: 8px;
      color: blue;
    }
    .stat-num-2 {
      font-size: 48px;
      font-weight: 900;
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
      padding-bottom: 0;
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
      overflow: hidden;
    }
    .section-logo img{
      width: 100%;
      height: 100%;
      object-fit: cover;
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

    /* ── MODAL OVERLAY (same pattern as events.php) ── */
    .modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-overlay.open {
      opacity: 1;
      visibility: visible;
    }

    .modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.80);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    /* ── MODAL CARD ── */
    .auth-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 1100px;
      max-height: 92vh;
      background: #fff;
      border-radius: 28px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      display: flex;
      overflow: hidden;
      transform: scale(0.95);
      transition: transform 0.3s ease;
    }

    .modal-overlay.open .auth-container {
      transform: scale(1);
    }

    /* ── BRAND PANEL ── */
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

    /* ── FORM PANEL ── */
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

    /* ── MODAL CLOSE (same as events.php) ── */
    .modal-close {
      position: absolute;
      top: 16px;
      right: 16px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: none;
      background: rgba(255, 255, 255, 0.9);
      color: #1f2937;
      font-size: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      z-index: 10;
    }

    .modal-close:hover {
      background: #fff;
      transform: scale(1.05);
    }

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

    .form-wrapper {
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .form-wrapper.hidden {
      display: none;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
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

    .input-hint {
      font-size: 11px;
      color: #9ca3af;
      margin-top: 5px;
    }

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

      .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
      }
    }

    @media (max-width: 480px) {
      .form-panel {
        padding: 25px 20px;
      }

      .form-header h2 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>

<!-- BACKGROUND PAGE (home.php content) -->
<div class="background-page" id="backgroundPage">
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
      <a href="login.php" class="btn-home">Access QCUS-PORTAL</a>
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

    <a href="sign-up.php" class="btn-gold btn-gold-center">Get Started Now</a>
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
</div>

<!-- MODAL OVERLAY (signup form on top of blurred home) -->
<div class="modal-overlay open" id="signupModalOverlay">
  <div class="modal-backdrop" onclick="closeModal()"></div>
  <div class="auth-container">

    <!-- Close button (same style as events.php modal-close) -->
    <button class="modal-close" onclick="closeModal()" title="Go back to home">✕</button>

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
          <li><span>📢</span> Keep up with the University Announcements</li>
          <li><span>📝</span> Track academic progress</li>
          <li><span>🔔</span> Stay updated with events</li>
        </ul>
      </div>
    </div>

    <div class="form-panel">
      <div class="form-header">
        <h2>Get Started</h2>
        <p>Fill in your details to create your account</p>
      </div>

      <div class="form-tabs">
        <button class="tab-btn" onclick="window.location.href='login.php'">Login</button>
        <button class="tab-btn active">Create Account</button>
      </div>

      <!-- Signup Form -->
      <div id="signupForm" class="form-wrapper active">
        <form action="signup-process.php" method="POST" onsubmit="return validateSignup()">
          <div class="form-group">
            <label>Student ID Number <span class="required">*</span></label>
            <input type="text" name="student_id" id="student_id" placeholder="25-****" required>
            <div class="input-hint">Format: 2X-XXXX (e.g., 24-1234)</div>
          </div>

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

          <div class="form-group">
            <label>QCU Email Address <span class="required">*</span></label>
            <input type="email" name="email" id="email" placeholder="your.name@qcu.edu.ph" required>
            <div class="input-hint">Use your official school-issued email address</div>
          </div>

          <div class="form-group">
            <label>Phone Number <span class="required">*</span></label>
            <input type="tel" name="phone_number" id="phone_number" placeholder="+63 912 345 6789" required>
          </div>

          <div class="form-group">
            <label>Address <span class="required">*</span></label>
            <textarea name="address" id="address" rows="2" placeholder="123 Main Street, Quezon City, Philippines" required></textarea>
          </div>

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

          <div class="form-group">
            <label>Confirm Password <span class="required">*</span></label>
            <input type="password" name="confirm_password" id="confirm_password" required>
          </div>

          <button type="submit" class="btn-submit">Create Account</button>

          <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Blur background on page load
  document.getElementById('backgroundPage').classList.add('blurred');

  function closeModal() {
    window.location.href = 'home.php';
  }

  // Close on Escape key (same as events.php)
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal();
    }
  });

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
        message = 'Weak password'; color = '#ef4444'; width = '25%'; break;
      case 2:
        message = 'Fair password'; color = '#f59e0b'; width = '50%'; break;
      case 3:
        message = 'Good password'; color = '#3b82f6'; width = '75%'; break;
      case 4:
      case 5:
        message = 'Strong password'; color = '#10b981'; width = '100%'; break;
      default:
        message = ''; width = '0%';
    }

    strengthFill.style.width = width;
    strengthFill.style.background = color;
    strengthText.textContent = message;
  }

  function validateSignup() {
    let isValid = true;
    const errors = [];

    const studentId = document.getElementById('student_id').value;
    const studentIdPattern = /^\d{2}-\d{4}$/;
    if (!studentIdPattern.test(studentId)) {
      errors.push('Student ID must be in format: XX-XXXX (e.g., 24-1234)');
      isValid = false;
    }

    const firstName = document.getElementById('first_name').value.trim();
    const lastName  = document.getElementById('last_name').value.trim();
    if (firstName.length < 2) { errors.push('First name must be at least 2 characters'); isValid = false; }
    if (lastName.length < 2)  { errors.push('Last name must be at least 2 characters');  isValid = false; }

    const email = document.getElementById('email').value;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
      errors.push('Email must be a valid email address');
      isValid = false;
    }

    const phone = document.getElementById('phone_number').value;
    const phonePattern = /^(\+63|0)[0-9]{10}$/;
    if (!phonePattern.test(phone.replace(/[\s-]/g, ''))) {
      errors.push('Phone number must be valid Philippine number');
      isValid = false;
    }

    const password = document.getElementById('password').value;
    if (password.length < 8)              { errors.push('Password must be at least 8 characters');          isValid = false; }
    if (!password.match(/[0-9]/))         { errors.push('Password must contain at least one number');        isValid = false; }
    if (!password.match(/[^a-zA-Z0-9]/)) { errors.push('Password must contain at least one special character'); isValid = false; }

    const confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) { errors.push('Passwords do not match'); isValid = false; }

    const course  = document.getElementById('course').value;
    const section = document.getElementById('section').value;
    if (!course  || course  === '') { errors.push('Please select a course');  isValid = false; }
    if (!section || section === '') { errors.push('Please select a section'); isValid = false; }

    if (!isValid) alert(errors.join('\n'));
    return isValid;
  }

  const studentIdInput = document.getElementById('student_id');
  if (studentIdInput) {
    studentIdInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/[^\d]/g, '');
      if (value.length > 2) value = value.slice(0, 2) + '-' + value.slice(2, 6);
      e.target.value = value;
    });
  }

  const phoneInput = document.getElementById('phone_number');
  if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/[^\d+]/g, '');
      if (value.startsWith('63')) value = '+' + value;
      e.target.value = value;
    });
  }
</script>
</body>
</html>