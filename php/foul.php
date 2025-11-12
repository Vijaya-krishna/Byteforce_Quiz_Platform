<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status'=>'error','msg'=>'only POST allowed']); exit;
}
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status'=>'error','msg'=>'not logged in']); exit;
}

require_once 'DB.php';
$user_id = intval($_SESSION['user_id']);

try {
  $db = (new DB())->conn();

  $now = time();
  $cool = 6; // seconds cooldown per client
  if (isset($_SESSION['last_foul_ts']) && ($now - intval($_SESSION['last_foul_ts'])) < $cool) {
    $row = $db->query("SELECT foul_count FROM fouls WHERE user_id = $user_id")->fetch_assoc();
    $count = intval($row['foul_count'] ?? 0);
    echo json_encode(['status'=>'warning','count'=>$count]); exit;
  }
  $_SESSION['last_foul_ts'] = $now;

  $stmt = $db->prepare("
    INSERT INTO fouls (user_id, foul_count, last_foul)
    VALUES (?, 1, NOW())
    ON DUPLICATE KEY UPDATE foul_count = LEAST(foul_count + 1, 5), last_foul = NOW()
  ");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();

  $row = $db->query("SELECT foul_count FROM fouls WHERE user_id = $user_id")->fetch_assoc();
  $count = intval($row['foul_count'] ?? 0);

  if ($count >= 5) {
    $stmt2 = $db->prepare("UPDATE users SET suspended = 1 WHERE id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    echo json_encode(['status'=>'suspended','count'=>5]); exit;
  }

  echo json_encode(['status'=>'warning','count'=>$count]); exit;

} catch (Throwable $e) {
  error_log("foul.php: ".$e->getMessage());
  echo json_encode(['status'=>'error','msg'=>'internal error']); exit;
}
