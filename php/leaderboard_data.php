<?php
require_once 'DB.php';
header('Content-Type: application/json');

try {
  $db = new DB();
  $conn = $db->conn();

  $res = $conn->query("
    SELECT u.username, r.score, r.attempts, r.last_attempt
    FROM results r 
    JOIN users u ON r.user_id = u.id
    ORDER BY r.score DESC, r.last_attempt DESC
  ");

  $data = [];
  while($row = $res->fetch_assoc()) $data[] = $row;

  echo json_encode($data, JSON_PRETTY_PRINT);
} catch (Exception $e) {
  echo json_encode(["error" => $e->getMessage()]);
}
