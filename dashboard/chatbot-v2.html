<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>QCU Assistant — AI Chatbot</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --purple-dark: #3b0d51;
      --purple-mid: #5b21b6;
      --purple-light: #7c3aed;
      --purple-soft: #ede9fe;
      --indigo: #3730a3;
      --bg: #f0f2f8;
      --surface: #ffffff;
      --surface2: #f8fafc;
      --border: rgba(148,163,184,.15);
      --text: #1e293b;
      --muted: #64748b;
      --sidebar-w: 280px;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--bg);
      color: var(--text);
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ── Setup screen ── */
    #setupScreen {
      position: fixed; inset: 0;
      background: linear-gradient(135deg, #1a0530 0%, #0f0c29 50%, #1a1060 100%);
      display: flex; align-items: center; justify-content: center;
      z-index: 9999;
      animation: fadeIn .4s ease;
    }

    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

    .setup-card {
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 28px;
      padding: 48px;
      max-width: 560px;
      width: 90%;
      backdrop-filter: blur(20px);
      animation: slideUp .5s cubic-bezier(.34,1.56,.64,1) both;
    }

    @keyframes slideUp {
      from { opacity:0; transform:translateY(30px); }
      to   { opacity:1; transform:translateY(0); }
    }

    .setup-logo {
      width: 64px; height: 64px;
      background: linear-gradient(135deg, #7c3aed, #4f46e5);
      border-radius: 20px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 28px;
    }

    .setup-logo svg { width: 32px; height: 32px; }

    .setup-card h1 {
      color: #fff;
      font-size: 26px;
      font-weight: 800;
      margin-bottom: 8px;
    }

    .setup-card > p {
      color: rgba(255,255,255,.5);
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 36px;
    }

    .tutorial-steps {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 32px;
    }

    .step {
      display: flex;
      gap: 14px;
      align-items: flex-start;
    }

    .step-num {
      width: 28px; height: 28px;
      border-radius: 8px;
      background: rgba(124,58,237,.3);
      border: 1px solid rgba(124,58,237,.4);
      color: #c4b5fd;
      font-size: 12px;
      font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .step-body { flex: 1; }

    .step-body strong {
      display: block;
      color: #e2e8f0;
      font-size: 13px;
      font-weight: 700;
      margin-bottom: 3px;
    }

    .step-body p {
      color: rgba(255,255,255,.45);
      font-size: 12px;
      line-height: 1.6;
    }

    .step-body a {
      color: #a78bfa;
      text-decoration: none;
    }

    .step-body a:hover { text-decoration: underline; }

    .step-body code {
      background: rgba(255,255,255,.08);
      border-radius: 4px;
      padding: 1px 6px;
      font-family: monospace;
      font-size: 11px;
      color: #c4b5fd;
    }

    .api-input-wrap {
      position: relative;
      margin-bottom: 16px;
    }

    .api-input {
      width: 100%;
      padding: 14px 48px 14px 16px;
      border-radius: 14px;
      border: 1.5px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      color: #fff;
      font-size: 13px;
      font-family: 'Montserrat', sans-serif;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      letter-spacing: .5px;
    }

    .api-input:focus {
      border-color: rgba(124,58,237,.6);
      box-shadow: 0 0 0 3px rgba(124,58,237,.15);
    }

    .api-input::placeholder { color: rgba(255,255,255,.25); letter-spacing: 0; }

    .toggle-key {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: rgba(255,255,255,.35);
      display: flex; align-items: center;
      transition: color .2s;
    }

    .toggle-key:hover { color: rgba(255,255,255,.7); }

    .setup-error {
      color: #fca5a5;
      font-size: 12px;
      margin-bottom: 12px;
      display: none;
    }

    .btn-start {
      width: 100%;
      padding: 15px;
      border-radius: 14px;
      border: none;
      background: linear-gradient(135deg, #5b21b6, #3730a3);
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      font-family: 'Montserrat', sans-serif;
      cursor: pointer;
      transition: transform .15s, box-shadow .2s, opacity .2s;
      box-shadow: 0 4px 20px rgba(91,33,182,.5);
    }

    .btn-start:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(91,33,182,.6); }
    .btn-start:active { transform: scale(.98); }
    .btn-start:disabled { opacity: .5; cursor: not-allowed; transform: none; }

    .skip-link {
      display: block; text-align: center;
      margin-top: 14px;
      color: rgba(255,255,255,.3);
      font-size: 12px;
      cursor: pointer;
      transition: color .2s;
    }
    .skip-link:hover { color: rgba(255,255,255,.6); }

    /* ── Main chat app ── */
    #chatApp {
      display: none;
      width: 100vw; height: 100vh;
      flex-direction: row;
    }

    #chatApp.visible { display: flex; }

    /* ── Sidebar ── */
    .sidebar {
      width: var(--sidebar-w);
      background: linear-gradient(180deg, #2d0845 0%, #110f2a 100%);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      border-right: 1px solid rgba(255,255,255,.06);
      overflow: hidden;
    }

    .sidebar-header {
      padding: 20px 18px 14px;
      border-bottom: 1px solid rgba(255,255,255,.06);
      flex-shrink: 0;
    }

    .sidebar-brand {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 14px;
    }

    .brand-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, #7c3aed, #4f46e5);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
    }

    .brand-icon svg { width: 18px; height: 18px; }

    .brand-text strong {
      display: block; color: #fff; font-size: 13px; font-weight: 800;
    }

    .brand-text span {
      color: rgba(255,255,255,.4); font-size: 10px;
    }

    .btn-new-chat {
      width: 100%;
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.05);
      color: rgba(255,255,255,.8);
      font-size: 12px; font-weight: 600;
      font-family: 'Montserrat', sans-serif;
      cursor: pointer;
      display: flex; align-items: center; gap: 8px;
      transition: background .2s, border-color .2s;
    }

    .btn-new-chat:hover {
      background: rgba(255,255,255,.1);
      border-color: rgba(255,255,255,.2);
    }

    .btn-new-chat svg { width: 14px; height: 14px; flex-shrink: 0; }

    .history-section {
      flex: 1;
      overflow-y: auto;
      padding: 14px 10px;
    }

    .history-section::-webkit-scrollbar { width: 3px; }
    .history-section::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

    .history-label {
      color: rgba(255,255,255,.25);
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      padding: 0 8px;
      margin-bottom: 6px;
    }

    .history-group { margin-bottom: 18px; }

    .history-item {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 10px;
      border-radius: 10px;
      cursor: pointer;
      transition: background .15s;
      position: relative;
    }

    .history-item:hover { background: rgba(255,255,255,.06); }
    .history-item.active { background: rgba(124,58,237,.2); }

    .history-item-icon {
      width: 28px; height: 28px;
      border-radius: 8px;
      background: rgba(255,255,255,.06);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }

    .history-item-icon svg { width: 13px; height: 13px; }

    .history-item-body { flex: 1; min-width: 0; }

    .history-item-title {
      color: rgba(255,255,255,.75);
      font-size: 12px;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .history-item.active .history-item-title { color: #c4b5fd; }

    .history-item-time {
      color: rgba(255,255,255,.25);
      font-size: 10px;
      margin-top: 1px;
    }

    .history-item-del {
      opacity: 0;
      background: none; border: none;
      color: rgba(255,255,255,.35);
      cursor: pointer;
      padding: 4px;
      border-radius: 6px;
      display: flex; align-items: center;
      transition: opacity .15s, color .15s, background .15s;
      flex-shrink: 0;
    }

    .history-item:hover .history-item-del { opacity: 1; }
    .history-item-del:hover { color: #fca5a5; background: rgba(239,68,68,.1); }

    .history-empty {
      text-align: center;
      padding: 32px 16px;
      color: rgba(255,255,255,.2);
      font-size: 12px;
      line-height: 1.7;
    }

    .history-empty svg {
      width: 32px; height: 32px;
      margin-bottom: 10px;
      opacity: .2;
    }

    .sidebar-footer {
      padding: 14px 10px;
      border-top: 1px solid rgba(255,255,255,.06);
      flex-shrink: 0;
    }

    .key-badge {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 10px;
      border-radius: 10px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
    }

    .key-badge svg { width: 13px; height: 13px; color: rgba(255,255,255,.3); flex-shrink: 0; }

    .key-badge-text { flex: 1; min-width: 0; }
    .key-badge-text strong { display: block; color: rgba(255,255,255,.5); font-size: 10px; }
    .key-badge-text span { color: rgba(255,255,255,.2); font-size: 10px; font-family: monospace; }

    .btn-change-key {
      background: none; border: none;
      color: rgba(255,255,255,.3); font-size: 10px;
      cursor: pointer; font-family: 'Montserrat', sans-serif;
      transition: color .2s;
    }
    .btn-change-key:hover { color: rgba(255,255,255,.7); }

    /* ── Main chat area ── */
    .chat-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: #f0f2f8;
    }

    .chat-topbar {
      padding: 16px 24px;
      background: #fff;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 14px;
      flex-shrink: 0;
    }

    .chat-topbar-avatar {
      width: 38px; height: 38px;
      border-radius: 12px;
      background: linear-gradient(135deg, #7c3aed, #4f46e5);
      display: flex; align-items: center; justify-content: center;
    }

    .chat-topbar-avatar svg { width: 18px; height: 18px; }

    .chat-topbar-info { flex: 1; }
    .chat-topbar-info strong { display: block; font-size: 14px; color: var(--text); }
    .chat-topbar-info span { font-size: 11px; color: var(--muted); }

    .online-pill {
      display: flex; align-items: center; gap: 6px;
      padding: 5px 12px;
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 20px;
    }

    .online-dot {
      width: 7px; height: 7px; border-radius: 50%; background: #22c55e;
      animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
      0%,100% { opacity:1; transform:scale(1); }
      50% { opacity:.5; transform:scale(.8); }
    }

    .online-pill span { font-size: 11px; color: #16a34a; font-weight: 600; }

    /* ── Messages ── */
    .messages-area {
      flex: 1;
      overflow-y: auto;
      padding: 28px 24px;
      display: flex;
      flex-direction: column;
      gap: 18px;
      scroll-behavior: smooth;
    }

    .messages-area::-webkit-scrollbar { width: 4px; }
    .messages-area::-webkit-scrollbar-thumb { background: rgba(148,163,184,.25); border-radius: 4px; }

    /* Welcome state */
    .welcome-state {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 40px 24px;
      animation: fadeIn .4s ease;
    }

    .welcome-icon {
      width: 72px; height: 72px;
      background: linear-gradient(135deg, #7c3aed, #4f46e5);
      border-radius: 22px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 20px;
      box-shadow: 0 12px 32px rgba(91,33,182,.25);
    }

    .welcome-icon svg { width: 36px; height: 36px; }

    .welcome-state h2 { font-size: 22px; font-weight: 800; margin-bottom: 8px; }
    .welcome-state p { color: var(--muted); font-size: 14px; line-height: 1.6; max-width: 360px; margin-bottom: 28px; }

    .welcome-chips {
      display: flex; flex-wrap: wrap; gap: 8px;
      justify-content: center; max-width: 480px;
    }

    .w-chip {
      padding: 9px 16px;
      border-radius: 20px;
      border: 1px solid rgba(91,33,182,.2);
      background: #fff;
      color: var(--purple-mid);
      font-size: 12px; font-weight: 600;
      cursor: pointer; font-family: 'Montserrat', sans-serif;
      transition: background .2s, transform .15s, box-shadow .2s;
      box-shadow: 0 2px 8px rgba(0,0,0,.04);
    }

    .w-chip:hover {
      background: var(--purple-soft);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(91,33,182,.15);
    }

    /* Message rows */
    .msg-row {
      display: flex; gap: 10px;
      animation: msg-in .35s cubic-bezier(.34,1.56,.64,1) both;
    }

    .msg-row.user { flex-direction: row-reverse; }

    @keyframes msg-in {
      from { opacity:0; transform:translateY(12px) scale(.97); }
      to   { opacity:1; transform:translateY(0) scale(1); }
    }

    .msg-avatar {
      width: 34px; height: 34px;
      border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700;
      flex-shrink: 0; align-self: flex-end;
    }

    .msg-avatar.bot { background: linear-gradient(135deg,#7c3aed,#4f46e5); color:#fff; }
    .msg-avatar.user { background: linear-gradient(135deg,#7c3aed,#2563eb); color:#fff; }

    .bubble {
      max-width: 68%;
      padding: 12px 16px;
      border-radius: 20px;
      font-size: 13.5px;
      line-height: 1.65;
    }

    .bot .bubble {
      background: #fff;
      color: var(--text);
      border: 1px solid var(--border);
      border-bottom-left-radius: 5px;
      box-shadow: 0 2px 12px rgba(15,23,42,.05);
    }

    .user .bubble {
      background: linear-gradient(135deg, #5b21b6, #3730a3);
      color: #fff;
      border-bottom-right-radius: 5px;
    }

    .bubble-time {
      display: block; font-size: 10px;
      margin-top: 5px; color: rgba(148,163,184,.7);
    }

    .user .bubble-time { text-align: right; color: rgba(255,255,255,.4); }

    /* Typing */
    .typing-row {
      display: flex; gap: 10px;
      animation: msg-in .3s ease both;
    }

    .typing-bubble {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 20px; border-bottom-left-radius: 5px;
      padding: 14px 18px;
      display: flex; gap: 5px; align-items: center;
      box-shadow: 0 2px 12px rgba(15,23,42,.05);
    }

    .typing-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: #7c3aed;
      animation: bounce 1.2s infinite ease-in-out;
    }

    .typing-dot:nth-child(2) { animation-delay: .2s; }
    .typing-dot:nth-child(3) { animation-delay: .4s; }

    @keyframes bounce {
      0%,60%,100% { transform:translateY(0); opacity:.35; }
      30% { transform:translateY(-7px); opacity:1; }
    }

    /* Error */
    .error-bubble {
      background: #fff5f5;
      border: 1px solid rgba(220,38,38,.18);
      color: #991b1b;
      border-radius: 14px;
      padding: 10px 14px;
      font-size: 12.5px;
      animation: msg-in .3s ease both;
      max-width: 68%;
      align-self: flex-start;
    }

    /* ── Input area ── */
    .input-area {
      padding: 14px 24px 20px;
      background: #fff;
      border-top: 1px solid var(--border);
      flex-shrink: 0;
    }

    .input-row {
      display: flex; gap: 10px; align-items: flex-end;
      background: var(--surface2);
      border: 1.5px solid rgba(148,163,184,.2);
      border-radius: 18px;
      padding: 6px 6px 6px 16px;
      transition: border-color .2s, box-shadow .2s;
    }

    .input-row:focus-within {
      border-color: rgba(124,58,237,.4);
      box-shadow: 0 0 0 3px rgba(124,58,237,.08);
      background: #fff;
    }

    .chat-input {
      flex: 1;
      border: none; background: transparent;
      font-size: 13.5px; font-family: 'Montserrat', sans-serif;
      color: var(--text); outline: none;
      resize: none; max-height: 120px;
      line-height: 1.5; padding: 6px 0;
    }

    .chat-input::placeholder { color: #94a3b8; }
    .chat-input:disabled { opacity: .6; }

    .send-btn {
      width: 40px; height: 40px;
      border-radius: 12px; border: none;
      background: linear-gradient(135deg, #5b21b6, #3730a3);
      color: #fff; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      transition: transform .15s, box-shadow .2s, opacity .2s;
      box-shadow: 0 4px 12px rgba(91,33,182,.35);
    }

    .send-btn:hover { transform: scale(1.05); box-shadow: 0 6px 18px rgba(91,33,182,.45); }
    .send-btn:active { transform: scale(.95); }
    .send-btn:disabled { opacity: .4; cursor: not-allowed; transform: none; box-shadow: none; }

    .input-hint {
      text-align: center; font-size: 10px; color: #94a3b8;
      margin-top: 8px;
    }

    /* ── Divider ── */
    .msg-divider {
      text-align: center; font-size: 10px; color: #94a3b8;
      display: flex; align-items: center; gap: 10px;
    }
    .msg-divider::before,.msg-divider::after {
      content:''; flex:1; height:1px; background:rgba(148,163,184,.2);
    }

    /* ── Limit bar ── */
    .limit-bar {
      display: flex; align-items: center; gap: 10px;
      padding: 8px 24px;
      background: #fffbeb;
      border-top: 1px solid #fde68a;
      flex-shrink: 0;
      font-size: 11px; color: #92400e;
    }
    .limit-bar svg { width: 14px; height: 14px; flex-shrink: 0; }
  </style>
</head>
<body>

<!-- ══════════════════════════════════════════
     SETUP / TUTORIAL SCREEN
══════════════════════════════════════════ -->
<div id="setupScreen">
  <div class="setup-card">
    <div class="setup-logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7H3a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
        <path d="M5 14v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4"/>
        <circle cx="9" cy="17" r="1" fill="white" stroke="none"/>
        <circle cx="15" cy="17" r="1" fill="white" stroke="none"/>
      </svg>
    </div>

    <h1>QCU Assistant Setup</h1>
    <p>Connect your Anthropic API key to power the AI chatbot. Follow the steps below — it only takes 2 minutes.</p>

    <div class="tutorial-steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-body">
          <strong>Create an Anthropic account</strong>
          <p>Go to <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a> and sign up for a free account. New accounts get <strong style="color:#a78bfa">$5 free credits</strong> — enough for ~1,500+ messages.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-body">
          <strong>Generate your API key</strong>
          <p>Once logged in, go to <strong style="color:#e2e8f0">API Keys</strong> in the left sidebar → click <strong style="color:#e2e8f0">"Create Key"</strong> → give it a name like <code>qcu-chatbot</code> → copy the key.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-body">
          <strong>Paste it below</strong>
          <p>Your key starts with <code>sk-ant-...</code> and is only shown once, so keep it safe. It stays in your browser only — never sent anywhere else.</p>
        </div>
      </div>
    </div>

    <div class="api-input-wrap">
      <input class="api-input" type="password" id="apiKeyInput" placeholder="sk-ant-api03-..." autocomplete="off" />
      <button class="toggle-key" onclick="toggleKeyVisibility()" id="toggleBtn">
        <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>

    <div class="setup-error" id="setupError">⚠️ Please enter a valid API key starting with <code>sk-ant-</code></div>

    <button class="btn-start" onclick="startChat()">Launch QCU Assistant →</button>
    <span class="skip-link" onclick="startChatDemo()">Skip for now (demo mode — no real AI)</span>
  </div>
</div>

<!-- ══════════════════════════════════════════
     MAIN CHAT APP
══════════════════════════════════════════ -->
<div id="chatApp">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7H3a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
            <path d="M5 14v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4"/>
            <circle cx="9" cy="17" r="1" fill="white" stroke="none"/>
            <circle cx="15" cy="17" r="1" fill="white" stroke="none"/>
          </svg>
        </div>
        <div class="brand-text">
          <strong>QCU Assistant</strong>
          <span>Powered by Claude AI</span>
        </div>
      </div>
      <button class="btn-new-chat" onclick="newChat()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New conversation
      </button>
    </div>

    <div class="history-section" id="historySidebar">
      <div class="history-empty" id="historyEmpty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <p>No conversations yet.<br>Start chatting to see your history here.</p>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="key-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
        </svg>
        <div class="key-badge-text">
          <strong>API Key</strong>
          <span id="keyPreview">sk-ant-••••••••</span>
        </div>
        <button class="btn-change-key" onclick="changeKey()">Change</button>
      </div>
    </div>
  </aside>

  <!-- Chat Main -->
  <div class="chat-main">
    <div class="chat-topbar">
      <div class="chat-topbar-avatar">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7H3a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
          <path d="M5 14v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4"/>
          <circle cx="9" cy="17" r="1" fill="white" stroke="none"/>
          <circle cx="15" cy="17" r="1" fill="white" stroke="none"/>
        </svg>
      </div>
      <div class="chat-topbar-info">
        <strong id="chatTitle">QCU Assistant</strong>
        <span>School-related queries only · Neil Longbian</span>
      </div>
      <div class="online-pill">
        <div class="online-dot"></div>
        <span id="statusLabel">Online</span>
      </div>
    </div>

    <div class="messages-area" id="msgArea">
      <!-- Welcome state shown when no messages -->
      <div class="welcome-state" id="welcomeState">
        <div class="welcome-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="36" height="36">
            <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7H3a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
            <path d="M5 14v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4"/>
            <circle cx="9" cy="17" r="1" fill="white" stroke="none"/>
            <circle cx="15" cy="17" r="1" fill="white" stroke="none"/>
          </svg>
        </div>
        <h2>Hi Neil, how can I help?</h2>
        <p>I'm your QCU school assistant. Ask me about your grades, subjects, schedule, enrollment, or any academic concerns.</p>
        <div class="welcome-chips">
          <button class="w-chip" onclick="quickSend('What are my enrolled subjects this semester?')">📚 My subjects</button>
          <button class="w-chip" onclick="quickSend('What is my current GPA and grades?')">📝 My grades</button>
          <button class="w-chip" onclick="quickSend('What events are coming up this week?')">📅 Upcoming events</button>
          <button class="w-chip" onclick="quickSend('How do I enroll for next semester?')">📋 Enrollment guide</button>
          <button class="w-chip" onclick="quickSend('Give me study tips for my current subjects')">💡 Study tips</button>
          <button class="w-chip" onclick="quickSend('What are my academic requirements this semester?')">🎓 Requirements</button>
        </div>
      </div>
    </div>

    <div class="limit-bar" id="limitBar" style="display:none">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <span id="limitText">Message limit reached. Start a new conversation.</span>
    </div>

    <div class="input-area">
      <div class="input-row">
        <textarea class="chat-input" id="chatInput" placeholder="Ask me about your grades, schedule, enrollment…" rows="1"></textarea>
        <button class="send-btn" id="sendBtn" onclick="sendMessage()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13" stroke="white"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2" fill="white" stroke="none"/>
          </svg>
        </button>
      </div>
      <div class="input-hint">School-related questions only &nbsp;·&nbsp; Press Enter to send, Shift+Enter for new line</div>
    </div>
  </div>

</div>

<!-- ONLY SHOWING MODIFIED SCRIPT PART — YOUR UI IS UNCHANGED -->
<script>
  // ── Config ───────────────────────────────────────────────
  const MAX_MESSAGES = 30;

  const SYSTEM_PROMPT = `You are a helpful student assistant for QCU.

STRICT RULES:
2. Find all available public information regarding QCU school-related concerns only.
3. Keep answers concise (3–5 sentences max).
4. Be friendly and helpful.`;

  // ── State ───────────────────────────────────────────────
  let apiKey = '';
  let isDemoMode = false;
  let conversationHistory = [];
  let allSessions = JSON.parse(localStorage.getItem('qcu_sessions') || '[]');
  let currentSessionId = null;
  let messageCount = 0;

  const msgArea = document.getElementById('msgArea');
  const chatInput = document.getElementById('chatInput');
  const sendBtn = document.getElementById('sendBtn');

  // ✅ AUTO LOAD SAVED KEY
  window.addEventListener("load", () => {
    const savedKey = localStorage.getItem('qcu_api_key');

    if (savedKey) {
      apiKey = savedKey;
      isDemoMode = false;

      document.getElementById('keyPreview').textContent =
        savedKey.substring(0, 10) + '••••••••';

      hideSetup();
    }

    // ✅ REMOVE ALT TEXT AFTER LOAD
    setTimeout(() => {
      document.querySelectorAll('img[alt="bot"]').forEach(img => {
        img.removeAttribute("alt");
      });
    }, 2000);
  });

  // ── Setup ───────────────────────────────────────────────
  function startChat() {
    const key = document.getElementById('apiKeyInput').value.trim();
    const err = document.getElementById('setupError');

    if (!key || !key.startsWith("sk-or-")) {
      err.style.display = 'block';
      err.innerHTML = '⚠️ Please enter a valid OpenRouter key (sk-or-...)';
      return;
    }

    apiKey = key;

    // ✅ SAVE KEY
    localStorage.setItem('qcu_api_key', key);

    isDemoMode = false;

    document.getElementById('keyPreview').textContent =
      key.substring(0, 10) + '••••••••';

    hideSetup();
  }

  function startChatDemo() {
    isDemoMode = true;
    hideSetup();
  }

  function changeKey() {
    const newKey = prompt("Enter new OpenRouter API key:");

    if (!newKey || !newKey.startsWith("sk-or-")) {
      alert("Invalid API key.");
      return;
    }

    apiKey = newKey;

    localStorage.setItem('qcu_api_key', newKey);

    document.getElementById('keyPreview').textContent =
      newKey.substring(0, 10) + '••••••••';
  }

  function hideSetup() {
    document.getElementById('setupScreen').style.display = 'none';
    document.getElementById('chatApp').classList.add('visible');
    renderHistory();
  }

  // ── API ───────────────────────────────────────────────
  async function callModel(model, messages) {
    const res = await fetch("https://openrouter.ai/api/v1/chat/completions", {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${apiKey}`,
        "Content-Type": "application/json",
        "HTTP-Referer": "http://localhost",
        "X-Title": "QCU Assistant"
      },
      body: JSON.stringify({ model, messages })
    });

    if (!res.ok) throw new Error("API failed");

    const data = await res.json();
    return data.choices[0].message.content;
  }

  async function getAIResponse(messages) {
    return await callModel("tencent/hy3-preview:free", messages);
  }

  // ── SEND MESSAGE (WITH TYPING ANIMATION PRESERVED) ──────
  async function sendMessage() {
    const text = chatInput.value.trim();
    if (!text || sendBtn.disabled) return;

    chatInput.value = '';

    const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    appendBubble(text, 'user', timeNow);
    conversationHistory.push({ role: 'user', content: text });

    messageCount++;
    setLoading(true);

    // ✅ KEEP TYPING ANIMATION
    showTyping();

    let replyText = '';

    if (isDemoMode) {
      await new Promise(r => setTimeout(r, 800));
      replyText = "Demo mode active.";
    } else {
      try {
        const messages = [
          { role: "system", content: SYSTEM_PROMPT },
          ...conversationHistory.slice(-20)
        ];

        replyText = await getAIResponse(messages);

      } catch (err) {
        removeTyping();
        showError("⚠️ AI failed.");
        setLoading(false);
        return;
      }
    }

    // ✅ REMOVE TYPING AFTER RESPONSE
    removeTyping();

    appendBubble(
      replyText,
      'bot',
      new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    );

    conversationHistory.push({ role: 'assistant', content: replyText });

    setLoading(false);
    checkLimit();
  }

  // ── UI ───────────────────────────────────────────────
  function appendBubble(text, role, time) {
    const row = document.createElement('div');
    row.className = 'msg-row ' + role;

    row.innerHTML = `
      <div class="msg-avatar ${role}">
        ${
          role === 'bot'
            ? '<img src="bot.png" alt="bot" style="width:100%;height:100%;border-radius:11px;">'
            : 'YOU'
        }
      </div>
      <div class="bubble">${text}<div class="bubble-time">${time}</div></div>
    `;

    msgArea.appendChild(row);
    msgArea.scrollTop = msgArea.scrollHeight;
  }

  function showTyping() {
    const div = document.createElement('div');
    div.id = 'typing';
    div.className = 'typing-row';
    div.innerHTML = `
      <div class="typing-bubble">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>`;
    msgArea.appendChild(div);
  }

  function removeTyping() {
    const t = document.getElementById('typing');
    if (t) t.remove();
  }

  function showError(msg) {
    const div = document.createElement('div');
    div.className = 'error-bubble';
    div.innerHTML = msg;
    msgArea.appendChild(div);
  }

  function setLoading(state) {
    sendBtn.disabled = state;
    chatInput.disabled = state;
  }

  function checkLimit() {
    if (messageCount >= MAX_MESSAGES) {
      sendBtn.disabled = true;
      chatInput.disabled = true;
    }
  }

  chatInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });
</script>

</body>
</html>
