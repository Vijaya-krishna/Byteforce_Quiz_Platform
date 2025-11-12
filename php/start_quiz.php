<?php
ini_set('session.cookie_path', '/');
ini_set('session.gc_maxlifetime', 3600);
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

if (empty($_SESSION['calibrated'])) {
  header("Location: eye_calibration.php?status=required");
  exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quiz Rules — BYTE FORCE</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* ✅ SAME BACKGROUND AS index.html */
body {
  margin:0;
  padding:0;
  font-family:'Poppins',sans-serif;
  overflow:hidden;
  background:#0a0f1f;
  color:white;
}

#bgCanvas {
  position:fixed;
  top:0; left:0;
  width:100vw;
  height:100vh;
  z-index:-1;
}

/* ✅ Page Wrapper (lowered content) */
.wrapper {
  margin-top: 120px;
  display:flex;
  justify-content:center;
  align-items:center;
  text-align:center;
}

/* ✅ Glass Rules Card */
.rules-card {
  width:520px;
  padding:35px;
  background:rgba(255,255,255,0.07);
  border-radius:18px;
  box-shadow:0 0 28px rgba(0,255,255,0.22);
  backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,0.12);
}

.rules-card h2 {
  margin-top:0;
  font-size:28px;
  font-weight:700;
  background:linear-gradient(90deg,#00eaff,#7b61ff);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.rules-card p {
  color:#b8c2ff;
}

.rules-list {
  text-align:left;
  margin:18px auto;
  font-size:15px;
  color:#d9dfff;
  line-height:1.6;
}

/* ✅ Checkbox */
.checkbox {
  margin-top:15px;
  font-size:15px;
}

/* ✅ Neon Button */
button {
  width:100%;
  padding:12px;
  margin-top:20px;
  border:none;
  border-radius:30px;
  background:linear-gradient(90deg,#00eaff,#7b61ff);
  color:black;
  font-weight:700;
  cursor:pointer;
  box-shadow:0 0 15px #00eaff;
  transition:0.25s;
}

button:hover:not(:disabled) {
  transform:scale(1.06);
  box-shadow:0 0 25px #00eaff,0 0 25px #7b61ff;
}

button:disabled {
  opacity:0.5;
  cursor:not-allowed;
}

/* ✅ Popup (same as index.html) */
#popup {
  position:fixed;
  top:-80px;
  left:50%;
  transform:translateX(-50%);
  background:rgba(0,255,180,0.2);
  color:#0affc6;
  border:1px solid #0affc6;
  padding:15px 25px;
  border-radius:10px;
  font-weight:600;
  text-shadow:0 0 10px #00ffc6;
  box-shadow:0 0 20px #00ffc6;
  opacity:0;
  transition:0.6s ease;
  z-index:9999;
}

#popup.show { top:20px; opacity:1; }

#popup.error {
  background:rgba(255,0,76,0.2);
  border-color:#ff004c;
  color:#ff6e6e;
  text-shadow:0 0 10px #ff004c;
  box-shadow:0 0 20px #ff004c;
}
</style>
</head>

<body>

<div id="popup"></div>
<canvas id="bgCanvas"></canvas>

<div class="wrapper">
  <div class="rules-card">
    <h2>Welcome, <?= $username; ?> 👋</h2>
    <p>Please read the rules carefully before starting your quiz.</p>

    <ul class="rules-list">
      <li>Each question has a 45-second timer.</li>
      <li>Switching tabs or minimizing gives a warning.</li>
      <li>5 warnings = automatic account suspension.</li>
      <li>Keep your face visible — webcam monitoring active.</li>
      <li>Correct answers: +10 points. Bonus for fast responses.</li>
    </ul>

    <div class="checkbox">
      <label><input type="checkbox" id="agree"> I agree to the rules and terms.</label>
    </div>

    <button id="startBtn" disabled>Start Quiz 🚀</button>
  </div>
</div>

<script>
/* ✅ Agree checkbox enable button */
const chk=document.getElementById('agree');
const btn=document.getElementById('startBtn');

chk.addEventListener('change',()=> btn.disabled=!chk.checked);

btn.addEventListener('click',()=>{
  btn.disabled=true;
  btn.textContent='Launching...';
  setTimeout(()=>window.location.href='quiz.php',1200);
});

/* ✅ BACKGROUND ANIMATION (same as index.html) */
const canvas=document.getElementById("bgCanvas");
const ctx=canvas.getContext("2d");
let particles=[]; let count=130;

function resize(){ canvas.width=innerWidth; canvas.height=innerHeight; }
addEventListener("resize",resize); resize();

class Particle{
  constructor(){
    this.x=Math.random()*canvas.width;
    this.y=Math.random()*canvas.height;
    this.s=Math.random()*2+0.5;
    this.vx=Math.random()*1-0.5;
    this.vy=Math.random()*1-0.5;
  }
  move(){
    this.x+=this.vx;
    this.y+=this.vy;
    if(this.x<0||this.x>canvas.width) this.vx*=-1;
    if(this.y<0||this.y>canvas.height) this.vy*=-1;
  }
  draw(){
    ctx.fillStyle="rgba(0,255,255,0.9)";
    ctx.shadowBlur=10;
    ctx.shadowColor="cyan";
    ctx.beginPath();
    ctx.arc(this.x,this.y,this.s,0,Math.PI*2);
    ctx.fill();
  }
}

for(let i=0;i<count;i++) particles.push(new Particle());

function animate(){
  ctx.clearRect(0,0,canvas.width,canvas.height);
  particles.forEach(p=>{ p.move(); p.draw(); });
  requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
