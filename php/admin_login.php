<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login — Quiz Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --accent: #00eaff;
      --accent2: #7b61ff;
      --bg1: #0f0c29;
      --bg2: #302b63;
      --bg3: #24243e;
    }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--bg1), var(--bg2), var(--bg3));
      color: white;
      height: 100vh;
      overflow: hidden;
    }

    /* animated glowing background */
    .bg-animation {
      position: fixed;
      width: 200vw;
      height: 200vh;
      background: radial-gradient(circle at center, #ffffff10, transparent);
      top: -50%;
      left: -50%;
      z-index: -1;
      animation: rotateBG 20s linear infinite;
    }
    @keyframes rotateBG { from {transform: rotate(0deg);} to {transform: rotate(360deg);} }

    .container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      flex-direction: column;
      text-align: center;
    }

    .card {
      background: rgba(255,255,255,0.08);
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
      backdrop-filter: blur(15px);
      padding: 40px 50px;
      width: 350px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn { from {opacity: 0; transform: translateY(30px);} to {opacity: 1; transform: translateY(0);} }

    h1 {
      font-weight: 700;
      font-size: 28px;
      color: var(--accent);
      text-shadow: 0 0 10px var(--accent);
      margin-bottom: 20px;
    }

    .input-group {
      margin-bottom: 20px;
      position: relative;
    }
    .input-group input {
      width: 100%;
      padding: 12px;
      border: none;
      border-bottom: 2px solid #bbb;
      background: transparent;
      color: white;
      font-size: 16px;
      outline: none;
      transition: border-color 0.3s;
    }
    .input-group input:focus {
      border-bottom-color: var(--accent);
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, var(--accent), var(--accent2));
      border: none;
      border-radius: 30px;
      color: black;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .btn:hover {
      box-shadow: 0 0 20px var(--accent);
      transform: scale(1.05);
    }

    .footer {
      margin-top: 30px;
      font-size: 13px;
      opacity: 0.6;
    }

    .back-btn {
      margin-top: 15px;
      display: inline-block;
      text-decoration: none;
      color: var(--accent);
      font-weight: 500;
      transition: 0.3s;
    }
    .back-btn:hover {
      text-shadow: 0 0 10px var(--accent);
    }

    /* Glow animation */
    @keyframes glow {
      from { text-shadow: 0 0 10px var(--accent); }
      to { text-shadow: 0 0 20px var(--accent2); }
    }
  </style>
</head>

<body>
  <div class="bg-animation"></div>

  <div class="container">
    <div class="card">
      <h1>👨‍💻 Admin Login</h1>
      <form action="admin_auth.php" method="POST">
        <div class="input-group">
          <input type="text" name="username" required placeholder="Username">
        </div>
        <div class="input-group">
          <input type="password" name="password" required placeholder="Password">
        </div>
        <button type="submit" class="btn">Login</button>
      </form>
      <a href="../index.html" class="back-btn">← Back to Home</a>
    </div>
    <p class="footer">BYTE FORCE | Admin Control Panel | 2025</p>
  </div>
</body>
</html>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login — Quiz Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --accent: #00eaff;
      --accent2: #7b61ff;
      --bg1: #0f0c29;
      --bg2: #302b63;
      --bg3: #24243e;
    }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--bg1), var(--bg2), var(--bg3));
      color: white;
      height: 100vh;
      overflow: hidden;
    }

    /* animated glowing background */
    .bg-animation {
      position: fixed;
      width: 200vw;
      height: 200vh;
      background: radial-gradient(circle at center, #ffffff10, transparent);
      top: -50%;
      left: -50%;
      z-index: -1;
      animation: rotateBG 20s linear infinite;
    }
    @keyframes rotateBG { from {transform: rotate(0deg);} to {transform: rotate(360deg);} }

    .container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      flex-direction: column;
      text-align: center;
    }

    .card {
      background: rgba(255,255,255,0.08);
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.15);
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
      backdrop-filter: blur(15px);
      padding: 40px 50px;
      width: 350px;
      animation: fadeIn 1s ease;
    }

    @keyframes fadeIn { from {opacity: 0; transform: translateY(30px);} to {opacity: 1; transform: translateY(0);} }

    h1 {
      font-weight: 700;
      font-size: 28px;
      color: var(--accent);
      text-shadow: 0 0 10px var(--accent);
      margin-bottom: 20px;
    }

    .input-group {
      margin-bottom: 20px;
      position: relative;
    }
    .input-group input {
      width: 100%;
      padding: 12px;
      border: none;
      border-bottom: 2px solid #bbb;
      background: transparent;
      color: white;
      font-size: 16px;
      outline: none;
      transition: border-color 0.3s;
    }
    .input-group input:focus {
      border-bottom-color: var(--accent);
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, var(--accent), var(--accent2));
      border: none;
      border-radius: 30px;
      color: black;
      font-weight: 700;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .btn:hover {
      box-shadow: 0 0 20px var(--accent);
      transform: scale(1.05);
    }

    .footer {
      margin-top: 30px;
      font-size: 13px;
      opacity: 0.6;
    }

    .back-btn {
      margin-top: 15px;
      display: inline-block;
      text-decoration: none;
      color: var(--accent);
      font-weight: 500;
      transition: 0.3s;
    }
    .back-btn:hover {
      text-shadow: 0 0 10px var(--accent);
    }

    /* Glow animation */
    @keyframes glow {
      from { text-shadow: 0 0 10px var(--accent); }
      to { text-shadow: 0 0 20px var(--accent2); }
    }
  </style>
</head>

<body>
  <div class="bg-animation"></div>

  <div class="container">
    <div class="card">
      <h1>👨‍💻 Admin Login</h1>
      <form action="admin_auth.php" method="POST">
        <div class="input-group">
          <input type="text" name="username" required placeholder="Username">
        </div>
        <div class="input-group">
          <input type="password" name="password" required placeholder="Password">
        </div>
        <button type="submit" class="btn">Login</button>
      </form>
      <a href="../index.html" class="back-btn">← Back to Home</a>
    </div>
    <p class="footer">BYTE FORCE | Admin Control Panel | 2025</p>
  </div>
</body>
</html>
