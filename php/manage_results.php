<?php
require_once 'DB.php';
$db = (new DB())->conn();
$res = $db->query("SELECT u.username, COUNT(r.id) AS attempts, MAX(r.score) AS highscore, MAX(r.created_at) AS last_time
                   FROM results r JOIN users u ON u.id = r.user_id
                   GROUP BY r.user_id ORDER BY highscore DESC");
if(!$res->num_rows){echo "<p>No results recorded.</p>";exit;}
echo "<table><tr><th>User</th><th>Attempts</th><th>High Score</th><th>Last Attempt (IST)</th></tr>";
while($r=$res->fetch_assoc()){
  $dt = new DateTime($r['last_time'], new DateTimeZone('UTC'));
  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
  $time = $dt->format('d M Y, H:i:s');
  echo "<tr><td>".htmlspecialchars($r['username'])."</td>
  <td>{$r['attempts']}</td><td>{$r['highscore']}</td><td>$time</td></tr>";
}
echo "</table>";
?>
