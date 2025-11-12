<?php 
session_start();
require_once 'DB.php';

// Secure admin login
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../index.html");
  exit;
}

$db = new DB();
$conn = $db->conn();

// Basic stats
$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$totalResults = $conn->query("SELECT COUNT(*) AS c FROM results")->fetch_assoc()['c'];
$totalQuestions = $conn->query("SELECT COUNT(*) AS c FROM quiz_questions")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard — BYTE FORCE</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
  --bg1:#0f0c29; --bg2:#302b63; --card:#171a29;
  --accent:#00eaff; --accent2:#7b61ff; --muted:rgba(255,255,255,0.7);
}
*{margin:0;padding:0;box-sizing:border-box;}
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,var(--bg1),var(--bg2));
  color:#e8f9ff; padding:20px;
}
input, select {
  padding:8px 10px;
  border-radius:8px;
  border:none;
  outline:none;
  background:rgba(255,255,255,0.1);
  color:#fff;
  appearance:none;
}
select option {
  background:#1e1e2f;
  color:#fff;
}

header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;}
h1{
  font-size:28px;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  -webkit-background-clip:text;color:transparent;
}
a.btn{
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  color:#021120;padding:8px 16px;border-radius:8px;text-decoration:none;
  font-weight:600;
}
a.btn.alt{
  background:transparent;border:1px solid var(--accent);color:var(--accent);
}
.card{
  background:rgba(255,255,255,0.06);padding:20px;border-radius:16px;margin-bottom:25px;
  box-shadow:0 8px 25px rgba(0,0,0,0.4);
}
.stats{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;
}
.stat{text-align:center;background:rgba(255,255,255,0.08);padding:18px;border-radius:12px;}
.stat h3{font-size:14px;color:var(--muted);}
.stat .num{font-size:32px;font-weight:800;color:var(--accent);}
.table-wrap{
  overflow-x:auto;
  margin-top:12px;
  display:flex;
  justify-content:center;
}
table{
  width:100%;
  max-width:1000px;
  border-collapse:collapse;
  font-size:14px;
  text-align:center;
}
th,td{
  padding:10px;
  border-bottom:1px solid rgba(255,255,255,0.1);
}
th{
  color:var(--accent2);
  text-transform:uppercase;
  font-size:13px;
}
tr:hover{background:rgba(255,255,255,0.04);}
form{display:flex;gap:10px;margin-top:15px;flex-wrap:wrap;}
button{
  padding:8px 14px;border-radius:8px;border:none;cursor:pointer;
  background:linear-gradient(90deg,var(--accent),var(--accent2));color:#021120;
  font-weight:600;
  transition:0.2s;
}
button:hover{
  transform:translateY(-2px);
  box-shadow:0 0 8px var(--accent2);
}
button.danger{background:#e33c3c;}
footer{text-align:center;margin-top:25px;font-size:13px;color:var(--muted);}
.refresh{color:var(--accent);cursor:pointer;font-weight:600;}
/* RESPONSIVE FIXES */
@media(max-width:768px){
  table{font-size:12px;}
  th,td{padding:8px;}
  h1{font-size:22px;}
  header{flex-direction:column;gap:10px;}
  .card{padding:15px;}
  form input, form select{width:100%;}
}
</style>
</head>
<body>

<header>
  <h1>BYTE FORCE Admin Dashboard</h1>
  <div>
    <a href="leaderboard.php" class="btn">Leaderboard</a>
    <a href="logout.php" class="btn alt">Logout</a>
  </div>
</header>

<!-- STATISTICS -->
<section class="card stats">
  <div class="stat"><h3>Total Users</h3><div class="num"><?= $totalUsers ?></div></div>
  <div class="stat"><h3>Quiz Attempts</h3><div class="num"><?= $totalResults ?></div></div>
  <div class="stat"><h3>Total Questions</h3><div class="num"><?= $totalQuestions ?></div></div>
</section>

<!-- LIVE LEADERBOARD -->
<section class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
    <h2>📊 Live Leaderboard</h2>
    <span class="refresh" onclick="refreshLeaderboard()">🔄 Refreshing...</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Rank</th><th>Username</th><th>Score</th><th>Attempts</th><th>Last Attempt</th></tr></thead>
      <tbody id="leaderboardBody"><tr><td colspan="5">Loading...</td></tr></tbody>
    </table>
  </div>
  <div style="text-align:center;margin-top:15px;">
    <button class="danger" onclick="resetResults()">🧹 Reset All Results</button>
  </div>
</section>

<!-- QUESTION MANAGEMENT -->
<section class="card">
  <h2>📝 Manage Questions</h2>
  <form id="addQForm">
    <input type="text" name="question" placeholder="Enter question" required style="flex:1;">
    <input type="text" name="a" placeholder="Option A" required>
    <input type="text" name="b" placeholder="Option B" required>
    <input type="text" name="c" placeholder="Option C" required>
    <input type="text" name="d" placeholder="Option D" required>
    <select name="correct" required>
      <option value="">Correct Answer</option>
      <option value="A">A</option><option value="B">B</option>
      <option value="C">C</option><option value="D">D</option>
    </select>
    <button type="submit">➕ Add</button>
  </form>

  <div class="table-wrap" id="questionTable"></div>
</section>

<!-- USER MANAGEMENT -->
<section class="card">
  <h2>👥 Manage Users</h2>
  <div class="table-wrap" id="userTable"></div>
</section>

<footer>BYTE FORCE Portal © <?= date("Y") ?> | Admin Control Center</footer>

<script>
async function refreshLeaderboard(){
  const res = await fetch('leaderboard_data.php');
  const data = await res.json();
  const body = document.getElementById('leaderboardBody');
  body.innerHTML='';
  if(!data.length){ body.innerHTML="<tr><td colspan='5'>No results yet.</td></tr>"; return; }
  data.forEach((r,i)=>{
    body.innerHTML += `<tr><td>#${i+1}</td><td>${r.username}</td><td>${r.score}</td><td>${r.attempts}</td><td>${r.last_attempt ?? '-'}</td></tr>`;
  });
}
setInterval(refreshLeaderboard,3000);
refreshLeaderboard();

// Load Questions
async function loadQuestions(){
  const res = await fetch('admin_actions.php?action=get_questions');
  const data = await res.json();
  const box = document.getElementById('questionTable');
  if(!data.length){ box.innerHTML='<p>No questions found.</p>'; return; }
  box.innerHTML=`<table><thead><tr><th>ID</th><th>Question</th><th>Correct</th><th>Action</th></tr></thead><tbody>${
    data.map(q=>`<tr><td>${q.id}</td><td>${q.question}</td><td>${q.correct_answer}</td><td><button class='danger' onclick='deleteQuestion(${q.id})'>Delete</button></td></tr>`).join('')
  }</tbody></table>`;
}

// Load Users
async function loadUsers(){
  const res = await fetch('admin_actions.php?action=get_users');
  const data = await res.json();
  const box = document.getElementById('userTable');
  if(!data.length){ box.innerHTML='<p>No users registered.</p>'; return; }
  box.innerHTML=`<table><thead><tr><th>ID</th><th>Username</th><th>Status</th><th>Action</th></tr></thead><tbody>${
    data.map(u=>`<tr><td>${u.id}</td><td>${u.username}</td><td>${u.suspended==1?'Suspended':'Active'}</td><td>
    <button onclick='toggleUser(${u.id},${u.suspended})'>${u.suspended==1?'Unsuspend':'Suspend'}</button>
    <button class='danger' onclick='deleteUser(${u.id})'>Delete</button></td></tr>`).join('')
  }</tbody></table>`;
}

// Add Question
document.getElementById('addQForm').addEventListener('submit',async(e)=>{
  e.preventDefault();
  const fd = new FormData(e.target);
  const res = await fetch('admin_actions.php?action=add_question',{method:'POST',body:fd});
  const msg = await res.text();
  alert(msg); e.target.reset(); loadQuestions();
});

// Delete Question
async function deleteQuestion(id){
  if(confirm("Delete this question?")){
    await fetch(`admin_actions.php?action=delete_question&id=${id}`);
    loadQuestions();
  }
}

// Toggle User Status
async function toggleUser(id,s){
  const act = s==1?'unsuspend':'suspend';
  if(confirm(`Are you sure you want to ${act} this user?`)){
    await fetch(`admin_actions.php?action=toggle_user&id=${id}`);
    loadUsers();
  }
}

// Delete User
async function deleteUser(id){
  if(confirm("Delete this user permanently?")){
    await fetch(`admin_actions.php?action=delete_user&id=${id}`);
    loadUsers();
  }
}

// Reset Results
async function resetResults(){
  if(confirm("This will clear all quiz scores. Continue?")){
    await fetch('admin_actions.php?action=reset_results');
    refreshLeaderboard();
    alert("All results cleared.");
  }
}

// Initial load
loadQuestions();
loadUsers();
</script>

</body>
</html>

