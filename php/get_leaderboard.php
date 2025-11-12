<?php
require_once 'DB.php';
$db = (new DB())->conn();

$q = "SELECT u.username, 
             COALESCE(r.score, 0) AS score, 
             COALESCE(r.attempts, 0) AS attempts, 
             r.last_attempt 
      FROM users u 
      LEFT JOIN results r ON u.id = r.user_id 
      ORDER BY r.score DESC, r.last_attempt DESC";

$res = $db->query($q);
$data = [];

while ($row = $res->fetch_assoc()) {
    if (!empty($row['last_attempt'])) {
        $dt = new DateTime($row['last_attempt'], new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
        $row['last_attempt'] = $dt->format('d/m/Y, H:i:s');
    } else {
        $row['last_attempt'] = '-';
    }
    $data[] = $row;
}

echo json_encode(["status" => "ok", "data" => $data]);
?>
