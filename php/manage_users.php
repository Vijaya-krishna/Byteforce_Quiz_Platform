<?php
require_once 'DB.php';
$db = (new DB())->conn();
$res = $db->query("SELECT id,username,suspended,created_at FROM users ORDER BY id ASC");
if(!$res->num_rows){echo "<p>No users yet.</p>";exit;}
echo "<table><tr><th>ID</th><th>Username</th><th>Status</th><th>Created</th><th>Actions</th></tr>";
while($u=$res->fetch_assoc()){
  $status = $u['suspended'] ? "Suspended" : "Active";
  $btnLabel = $u['suspended'] ? "Unsuspend" : "Suspend";
  $dt = new DateTime($u['created_at'], new DateTimeZone('UTC'));
  $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
  $time = $dt->format('d M Y, H:i:s');
  echo "<tr>
    <td>{$u['id']}</td>
    <td>".htmlspecialchars($u['username'])."</td>
    <td>$status</td>
    <td>$time</td>
    <td>
      <button class='action suspend' onclick='toggleUser({$u['id']})'>$btnLabel</button>
      <button class='action del' onclick='delUser({$u['id']})'>Delete</button>
    </td>
  </tr>";
}
echo "</table>";
?>
