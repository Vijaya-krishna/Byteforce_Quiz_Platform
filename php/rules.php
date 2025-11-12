<?php
session_start();
if(!isset($_SESSION['user_id'])) header("Location: ../index.html");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Quiz Rules — BYTE FORCE Quiz Portal</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<style>
:root{ --bg1:#0f0c29; --bg2:#302b63; --accent:#00eaff; --accent2:#7b61ff; }
html,body{
  height:auto;
  min-height:100vh;
  margin:0;
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,var(--bg1),var(--bg2));
  color:white;
  overflow-y:auto;
}
.container{
  box-sizing:border-box;
  width:94%;
  max-width:900px;
  margin:32px auto;
  padding:28px;
  border-radius:16px;
  background:rgba(255,255,255,0.04);
  backdrop-filter:blur(8px);
  box-shadow:0 10px 40px rgba(0,0,0,0.5);
}
h1{
  font-size:34px;
  margin:0 0 8px;
  color:transparent;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text;
}
.subtitle{color:#d6f8ff;opacity:0.9;margin-bottom:16px}
.section{margin-top:18px}
ul{padding-left:18px;margin:10px 0 20px}
li{margin:10px 0;line-height:1.5}
.rule-note{margin-top:6px}
.controls{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:18px}
.btn{
  padding:12px 20px;
  border-radius:12px;
  border:none;
  font-weight:700;
  cursor:pointer;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  color:#031022;
}
.btn[disabled]{opacity:0.5;cursor:not-allowed}
.checkbox{display:flex;align-items:center;gap:10px}
footer{font-size:13px;text-align:center;margin-top:12px;opacity:0.7}
@media(max-width:640px){ .container{padding:18px} h1{font-size:28px} }
</style>
</head>
<body>
  <div class="container" role="main" aria-labelledby="rulesTitle">
    <h1 id="rulesTitle">BYTE FORCE QUIZ — Rules & Scoring</h1>
    <p class="subtitle">Please read carefully. You must accept the rules to continue to the quiz.</p>

    <div class="section">
      <h3>📋 General Rules</h3>
      <ul>
        <li>This is a timed online quiz with webcam-based attention monitoring.</li>
        <li>Each question must be answered before the timer runs out (45s per question).</li>
        <li>Switching tabs or looking away will generate a warning; 5 warnings → suspension.</li>
        <li>Ensure your face is visible and lighting is adequate for calibration.</li>
      </ul>
    </div>

    <div class="section">
      <h3>🎯 Scoring</h3>
      <ul>
        <li>Each correct answer = <strong>1 base point</strong>.</li>
        <li>Speed bonus: faster answers may get additional bonus (up to +2).</li>
        <li>No negative marking for wrong answers.</li>
      </ul>
    </div>

    <div class="section">
      <h3>⚠️ Conduct</h3>
      <ul>
        <li>Do not use phones, notes, or external help. Keep your eyes on the screen.</li>
        <li>Refreshing or attempting to cheat may result in disqualification.</li>
      </ul>
    </div>

    <div class="section rule-note">
      <label class="checkbox">
        <input type="checkbox" id="agree"> I have read and understood the rules.
      </label>
    </div>

    <div class="controls">
      <button id="proceed" class="btn" disabled>Proceed to Quiz 🚀</button>
      <button id="backHome" class="btn" style="background:transparent;color:var(--accent);border:1px solid rgba(255,255,255,0.08)">Back to Home</button>
      <div id="notice" style="margin-left:auto;color:#ffd6d6;font-weight:600"></div>
    </div>

    <footer>BYTE FORCE | Quiz Portal 2025 | JIT — Keep your webcam on during the quiz</footer>
  </div>

<script>
const agree = document.getElementById('agree');
const proceed = document.getElementById('proceed');
const back = document.getElementById('backHome');
const notice = document.getElementById('notice');

// Enable the proceed button only when checkbox is ticked
agree.addEventListener('change', () => {
  proceed.disabled = !agree.checked;
});

// ✅ FIXED: Now goes directly to quiz.php instead of calibration
proceed.addEventListener('click', async () => {
  if (!agree.checked) return;
  notice.textContent = "Verifying webcam before quiz...";
  try {
    await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
    // ✅ Correct destination after rules
    location.href = 'quiz.php';
  } catch (err) {
    notice.textContent = "⚠️ Webcam permission required. Please allow access and retry.";
    alert("Camera permission is required to start the quiz.");
  }
});

back.addEventListener('click', () => { location.href = '../index.html'; });

// 🔄 Visibility warning counter stored in sessionStorage
(function attachVisibilityWatcher(){
  if (!('sessionStorage' in window)) return;
  if (!sessionStorage.getItem('warnings')) sessionStorage.setItem('warnings', '0');

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      let w = parseInt(sessionStorage.getItem('warnings') || '0', 10);
      w = isNaN(w) ? 1 : w + 1;
      sessionStorage.setItem('warnings', String(w));
      const old = notice.textContent;
      notice.textContent = `Warning ${w}/5 for leaving the tab`;
      setTimeout(() => { notice.textContent = old; }, 3500);
    }
  });
})();
</script>
</body>
</html>
