<?php
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_secure', $secure ? 1 : 0);
session_start();

require_once __DIR__ . '/DB.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: /index.html"); exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
  header("Location: /index.html?status=fill_required"); exit;
}

try {
  $db = (new DB())->conn();
  $stmt = $db->prepare("SELECT id, username, password_hash, suspended FROM users WHERE username = ? LIMIT 1");
  $stmt->bind_param("s", $username);
  $stmt->execute(); $res = $stmt->get_result();

  if (!$res || $res->num_rows === 0) {
    header("Location: /index.html?status=user_not_found"); exit;
  }
  $row = $res->fetch_assoc();

  if (!password_verify($password, $row['password_hash'])) {
    header("Location: /index.html?status=invalid_password"); exit;
  }

  if (intval($row['suspended']) === 1) {
    header("Location: /index.html?status=suspended"); exit;
  }

  session_regenerate_id(true);
  $_SESSION['username'] = $row['username'];
  $_SESSION['user_id']  = intval($row['id']);
  $_SESSION['calibrated'] = false;

  header("Location: /php/eye_calibration.php?status=login_success");
  exit;

} catch (Throwable $e) {
  error_log("LOGIN error: " . $e->getMessage());
  header("Location: /index.html?status=server_error"); exit;
}
