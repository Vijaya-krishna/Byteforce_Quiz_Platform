<?php
require_once 'DB.php';
session_start();
$db = (new DB())->conn();
$username = $_SESSION['username'] ?? null;

// --- AJAX refresh ---
if (isset($_GET['ajax'])) {
    $filter = $_GET['filter'] ?? 'all';

    switch ($filter) {
        case 'mine':
            $sql = "SELECT username, score, total_questions, attempt_time 
                    FROM quiz_attempts 
                    WHERE username = ? 
                    ORDER BY attempt_time DESC";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();
            break;

        case 'top10':
            $sql = "SELECT username, score, total_questions, attempt_time 
                    FROM quiz_attempts 
                    WHERE score > 0 
                    ORDER BY score DESC, attempt_time ASC 
                    LIMIT 10";
            $res = $db->query($sql);
            break;

        default:
            $sql = "SELECT username, score, total_questions, attempt_time 
                    FROM quiz_attempts 
                    WHERE score > 0 
                    ORDER BY score DESC, attempt_time ASC";
            $res = $db->query($sql);
    }

    $rows = [];
    if ($res && $res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
    }
    echo json_encode($rows);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Live Leaderboard — BYTE FORCE</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: radial-gradient(circle at top, #120e32, #060518);
  color: #fff;
  text-align: center;
  margin: 0;
  padding: 40px;
  overflow-x: hidden;
}
h2 {
  background: linear-gradient(90deg, #00eaff, #7b61ff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 800;
  margin-bottom: 25px;
}
.filters, .mode-switch {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}
.filter-btn, .mode-btn {
  background: transparent;
  border: 2px solid #00eaff;
  border-radius: 10px;
  color: #00eaff;
  padding: 10px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}
.filter-btn.active, .mode-btn.active {
  background: linear-gradient(90deg, #00eaff, #7b61ff);
  color: #0e0c27;
  border-color: transparent;
  box-shadow: 0 0 15px #00eaff;
}
.filter-btn:hover, .mode-btn:hover { transform: scale(1.05); }
table {
  width: 90%;
  margin: 0 auto;
  border-collapse: collapse;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
}
th, td {
  padding: 14px 20px;
  text-align: center;
}
th {
  background: #00eaff;
  color: #0e0c27;
  text-transform: uppercase;
}
tr:nth-child(even) { background: rgba(255, 255, 255, 0.05); }
tr:hover { background: rgba(255, 255, 255, 0.1); }
.rank { color: #00eaff; font-weight: 700; }

.stage {
  display: none;
  justify-content: center;
  align-items: flex-end;
  gap: 30px;
  margin-top: 60px;
}
.stage.active { display: flex; }

.podium {
  width: 140px;
  border-radius: 12px 12px 0 0;
  color: #fff;
  padding: 10px;
  text-align: center;
  box-shadow: 0 0 30px rgba(0,0,0,0.5);
  position: relative;
  transition: transform 0.6s ease, box-shadow 0.6s ease;
}
.podium .name { font-weight: 700; margin-bottom: 8px; font-size: 1.1em; }
.podium .score { font-size: 20px; font-weight: 800; transition: 0.4s ease; }
.podium.glow { box-shadow: 0 0 40px 10px rgba(0,234,255,0.5); }

#first { height: 200px; background: linear-gradient(180deg,#00eaff,#007bff); transform: scale(1.05); }
#second { height: 160px; background: linear-gradient(180deg,#7b61ff,#4a2fff); }
#third { height: 130px; background: linear-gradient(180deg,#ff9800,#e65100); }

.return, .admin-btn {
  display: inline-block;
  margin: 30px 10px 0;
  background: linear-gradient(90deg, #00eaff, #7b61ff);
  padding: 10px 20px;
  border-radius: 12px;
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
.return:hover, .admin-btn:hover { box-shadow: 0 0 20px #00eaff; transform: scale(1.05); }

.confetti {
  position: fixed;
  width: 8px; height: 8px;
  background: #00eaff;
  top: 0; left: 50%;
  opacity: 0.9;
  animation: fall 3s linear forwards;
  z-index: 9999;
}
@keyframes fall {
  to { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}
</style>
</head>
<body>
<h2>🏆 BYTE FORCE — Live Leaderboard</h2>

<div class="filters">
  <button class="filter-btn active" data-filter="all">All Attempts</button>
  <button class="filter-btn" data-filter="mine">My Attempts</button>
  <button class="filter-btn" data-filter="top10">Top 10</button>
</div>

<div class="mode-switch">
  <button id="tableBtn" class="mode-btn active">Table Mode</button>
  <button id="stageBtn" class="mode-btn">Stage Mode</button>
</div>

<table id="leaderboardTable">
  <thead>
    <tr><th>Rank</th><th>Username</th><th>Score</th><th>Total Questions</th><th>Attempt Time (IST)</th></tr>
  </thead>
  <tbody></tbody>
</table>

<div class="stage" id="stageView">
  <div class="podium" id="second"><div class="name">—</div><div class="score">0</div></div>
  <div class="podium" id="first"><div class="name">—</div><div class="score">0</div></div>
  <div class="podium" id="third"><div class="name">—</div><div class="score">0</div></div>
</div>

<!-- Back Buttons -->
<div style="margin-top:30px;">
  <a href="#" class="return" id="backBtn">← Back</a>
  <?php if ($username === 'admin'): ?>
    <a href="admin_dashboard.php" class="admin-btn">🏠 Admin Dashboard</a>
  <?php endif; ?>
</div>

<script>
let currentFilter = 'all';
let lastTopUser = null;
const tbody = document.querySelector('#leaderboardTable tbody');
const stageView = document.getElementById('stageView');
const table = document.getElementById('leaderboardTable');

// Convert UTC → IST for display
function toIST(utcString){
  if(!utcString) return '-';
  const d = new Date(utcString + ' UTC');
  return d.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata', hour12: false });
}

// Dynamic "Back" button
document.getElementById('backBtn').addEventListener('click', e=>{
  e.preventDefault();
  if (document.referrer && !document.referrer.includes('leaderboard.php')) {
    history.back(); // Go back to previous page if from quiz
  } else {
    window.location.href = 'start_quiz.php'; // Fallback: go to start page
  }
});

document.querySelectorAll('.filter-btn').forEach(btn=>{
  btn.onclick=()=>{
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter=btn.dataset.filter;
    fetchLeaderboard();
  }
});
document.getElementById('tableBtn').onclick=()=>{
  document.getElementById('tableBtn').classList.add('active');
  document.getElementById('stageBtn').classList.remove('active');
  table.style.display='table'; stageView.classList.remove('active');
};
document.getElementById('stageBtn').onclick=()=>{
  document.getElementById('stageBtn').classList.add('active');
  document.getElementById('tableBtn').classList.remove('active');
  table.style.display='none'; stageView.classList.add('active');
};

async function fetchLeaderboard(){
  try{
    const res=await fetch(`leaderboard.php?ajax=1&filter=${currentFilter}`);
    const data=await res.json();
    renderTable(data);
    renderStage(data);
  }catch(e){console.error(e);}
}

function renderTable(data){
  tbody.innerHTML='';
  if(!data.length){tbody.innerHTML='<tr><td colspan=5>No attempts yet.</td></tr>';return;}
  data.forEach((r,i)=>{
    tbody.innerHTML+=`<tr>
      <td>#${i+1}</td><td>${r.username}</td>
      <td>${r.score}</td><td>${r.total_questions}</td>
      <td>${toIST(r.attempt_time)}</td></tr>`;
  });
}

function renderStage(data){
  const [first,second,third]=[data[0],data[1],data[2]]||[];
  updatePodium('first',first);
  updatePodium('second',second);
  updatePodium('third',third);

  if(first && first.username!==lastTopUser){
    triggerConfetti();
    document.getElementById('first').classList.add('glow');
    setTimeout(()=>document.getElementById('first').classList.remove('glow'),3000);
    lastTopUser=first.username;
  }
}

function updatePodium(id,info){
  const el=document.getElementById(id);
  const name=el.querySelector('.name');
  const score=el.querySelector('.score');
  if(!info){name.textContent='—';score.textContent='';return;}
  if(parseInt(score.textContent)||0 !== info.score){
    animateScore(score,parseInt(score.textContent)||0,info.score);
  }
  name.textContent=info.username;
}

function animateScore(el,from,to){
  let start=null;
  const duration=600;
  const step=(ts)=>{
    if(!start)start=ts;
    const progress=Math.min((ts-start)/duration,1);
    const val=Math.floor(from+(to-from)*progress);
    el.textContent=val+' pts';
    if(progress<1)requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

function triggerConfetti(){
  for(let i=0;i<80;i++){
    const c=document.createElement('div');
    c.className='confetti';
    c.style.left=Math.random()*100+'%';
    c.style.background=`hsl(${Math.random()*360},80%,60%)`;
    c.style.animationDuration=(2+Math.random()*1.5)+'s';
    document.body.appendChild(c);
    setTimeout(()=>c.remove(),3000);
  }
}

fetchLeaderboard();
setInterval(fetchLeaderboard,3000);
</script>
</body>
</html>
