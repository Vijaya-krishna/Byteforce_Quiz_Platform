<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: ../index.html");

$u = htmlspecialchars($_GET['u'] ?? 'Player');
$score = intval($_GET['s'] ?? 0);
$total = intval($_GET['t'] ?? 0);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Quiz Results — BYTE FORCE</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
<style>
:root {
  --accent:#00eaff;
  --accent2:#7b61ff;
  --bg1:#0f0c29;
  --bg2:#302b63;
}

/* Unified background fix */
html, body {
  margin:0;
  padding:0;
  height:100%;
  width:100%;
  display:flex;
  justify-content:center;
  align-items:center;
  background:radial-gradient(circle at center, #151540 0%, #0f0c29 40%, #221f4a 100%);
  background-attachment:fixed;
  color:#ffffff;
  font-family:'Poppins',sans-serif;
  text-align:center;
  overflow:hidden;
}

/* Floating result card */
.box {
  background:rgba(255,255,255,0.05);
  padding:60px 90px;
  border-radius:24px;
  box-shadow:
    0 0 50px rgba(0,234,255,0.25),
    0 0 120px rgba(0,234,255,0.1);
  animation:fadeIn 1s ease forwards;
  backdrop-filter: blur(10px);
}

@keyframes fadeIn {
  from {opacity:0; transform:translateY(30px) scale(0.95);}
  to {opacity:1; transform:translateY(0) scale(1);}
}

/* Headings */
h1 {
  font-size:48px;
  margin-bottom:10px;
  text-shadow:0 0 25px var(--accent);
}

.score {
  font-size:86px;
  font-weight:900;
  color:#00ffff;
  text-shadow:0 0 40px var(--accent2), 0 0 80px rgba(0,234,255,0.4);
  animation:pulseGlow 3s ease-in-out infinite;
}

@keyframes pulseGlow {
  0%, 100% { text-shadow:0 0 40px var(--accent2), 0 0 80px rgba(0,234,255,0.4); }
  50% { text-shadow:0 0 70px var(--accent), 0 0 140px rgba(0,234,255,0.5); }
}

/* Text and buttons */
p {
  font-size:18px;
  opacity:0.9;
  margin-top:12px;
}

button {
  margin:15px 8px 0;
  padding:12px 25px;
  border:none;
  border-radius:30px;
  background:linear-gradient(90deg,var(--accent),var(--accent2));
  color:#061122;
  font-weight:700;
  font-size:16px;
  cursor:pointer;
  transition:0.3s;
}
button:hover {
  transform:translateY(-3px);
  box-shadow:0 0 25px var(--accent);
}

/* Footer */
footer {
  position:fixed;
  bottom:15px;
  width:100%;
  text-align:center;
  opacity:0.6;
  font-size:13px;
  letter-spacing:0.5px;
}

/* Mobile adjustments */
@media (max-width:600px) {
  .box {
    padding:40px 25px;
    width:90%;
  }
  .score {
    font-size:70px;
  }
  h1 {
    font-size:38px;
  }
}
</style>
</head>
<body>
  <div class="box">
    <h1>Quiz Complete</h1>
    <div class="score"><?php echo $score . '/' . ($total ?: '—'); ?></div>
    <p>
      <?php
        if ($total > 0 && $score == $total) echo "Perfect score! 🌟";
        elseif ($score > 0) echo "Result recorded. Nice work!";
        else echo "Keep trying — you’ll get there!";
      ?>
    </p>
    <button onclick="window.location.href='leaderboard.php'">View Leaderboard</button>
    <button onclick="window.location.href='logout.php'">Logout</button>
  </div>
  <footer>BYTE FORCE | Quiz Portal 2025 | JIT</footer>
</body>
</html>
