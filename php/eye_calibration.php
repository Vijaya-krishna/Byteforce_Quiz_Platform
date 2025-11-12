<?php
ini_set('session.cookie_path', '/');
ini_set('session.gc_maxlifetime', 3600);
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Handle POST when calibration finishes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['done'])) {
  $_SESSION['calibrated'] = true;
  echo json_encode(['status' => 'ok']);
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Eye Calibration | BYTE FORCE</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{
  margin:0;height:100vh;display:flex;justify-content:center;align-items:center;
  background:radial-gradient(circle at top,#0f0c29,#302b63,#24243e);color:white;
  font-family:'Poppins',sans-serif;overflow:hidden;
}

.card{
  background:rgba(255,255,255,0.07);
  padding:40px;border-radius:20px;
  box-shadow:0 0 25px rgba(0,0,0,0.3);
  text-align:center;width:480px;
}

h1{
  background:linear-gradient(90deg,#00eaff,#7b61ff);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  grid-gap:40px;
  justify-items:center;
  margin:25px auto;
}

.dot{
  width:25px;height:25px;background:#00eaff;border-radius:50%;
  box-shadow:0 0 20px #00eaff;
  animation:pulse 1.5s infinite;
  cursor:pointer;
}

.dot.done{
  background:#00ffb3;
  box-shadow:0 0 20px #00ffb3;
}

@keyframes pulse{
  0%{transform:scale(1);opacity:0.7;}
  50%{transform:scale(1.4);opacity:1;}
  100%{transform:scale(1);opacity:0.7;}
}

.progressbar{
  height:10px;background:rgba(255,255,255,0.15);
  border-radius:10px;overflow:hidden;margin-top:10px;
}
.progressfill{
  height:100%;width:0%;background:linear-gradient(90deg,#ff004c,#ffb300,#00ff9d);
  transition:width .3s ease;
}

#status{margin-top:10px;font-weight:600;}

.complete{
  position:fixed;top:0;left:0;right:0;bottom:0;
  background:rgba(0,0,0,0.85);
  display:none;justify-content:center;align-items:center;
  flex-direction:column;color:#00ff9d;font-size:1.5em;font-weight:700;
}

video{
  position:fixed;top:10px;left:10px;width:240px;border-radius:10px;opacity:0.65;
}
</style>
</head>
<body>

<video id="webgazerVideoFeed" autoplay muted playsinline></video>

<div class="card">
  <h1>👁️ Eye Calibration</h1>
  <p>Follow the glowing dots with your eyes.</p>

  <div class="grid" id="grid"></div>

  <p id="progress">Calibrating... 0/9</p>

  <div class="progressbar">
      <div class="progressfill" id="bar"></div>
  </div>

  <p id="status">Accuracy: 0%</p>
</div>

<div class="complete" id="overlay">
  <p>✅ Calibration Complete</p>
  <p>Redirecting to quiz rules...</p>
</div>

<script src="https://webgazer.cs.brown.edu/webgazer.js"></script>
<script>
const grid=document.getElementById('grid');
const progress=document.getElementById('progress');
const bar=document.getElementById('bar');
const status=document.getElementById('status');
const overlay=document.getElementById('overlay');

const totalDots=9;
let done=0,gazePoints=[],targets=[];

// Build dots
for(let i=0;i<totalDots;i++){
  const d=document.createElement('div');
  d.className='dot';
  d.addEventListener('click',()=>handleClick(d));
  grid.appendChild(d);
}

function handleClick(dot){
  if(dot.classList.contains('done')) return;
  dot.classList.add('done');
  done++;
  progress.textContent=`Calibrating... ${done}/${totalDots}`;

  const r=dot.getBoundingClientRect();
  targets.push({x:r.left+r.width/2,y:r.top+r.height/2});
}

webgazer.setGazeListener(data=>{
  if(!data) return;
  gazePoints.push({x:data.x,y:data.y});
  if(gazePoints.length>25) gazePoints.shift(); // fast calibration
  updateAccuracy();
}).begin();

function updateAccuracy(){
  if(targets.length===0) return;

  let total=0,c=0;
  targets.forEach(t=>{
    gazePoints.forEach(g=>{
      const dx=g.x-t.x,dy=g.y-t.y;
      total+=Math.sqrt(dx*dx+dy*dy);
      c++;
    });
  });

  const avg=total/Math.max(1,c);
  const acc=Math.max(0,100-(avg/28));   // original fast formula
  const val=Math.min(Math.max(acc,0),100);

  bar.style.width=val+'%';
  status.textContent='Accuracy: '+val.toFixed(1)+'%';

  if(done>=totalDots && val>=65) finishCalibration();  // original threshold
}

function finishCalibration(){
  webgazer.pause();
  overlay.style.display='flex';

  fetch('eye_calibration.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'done=1'
  }).then(()=>setTimeout(()=>window.location.href='start_quiz.php',1500));
}
</script>
</body>
</html>
