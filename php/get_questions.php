<?php
require_once 'DB.php';
$db = (new DB())->conn();
$res = $db->query("SELECT * FROM quiz_questions ORDER BY id ASC");
if(!$res->num_rows){echo "<p>No questions yet.</p>";exit;}
echo "<table><tr><th>ID</th><th>Question</th><th>Correct</th><th>Actions</th></tr>";
while($r=$res->fetch_assoc()){
  echo "<tr>
  <td>{$r['id']}</td>
  <td>".htmlspecialchars($r['question'])."</td>
  <td>{$r['correct_answer']}</td>
  <td>
    <button class='action edit' onclick='editQ({$r['id']})'>Edit</button>
    <button class='action del' onclick='delQ({$r['id']})'>Delete</button>
  </td></tr>";
}
echo "</table>";
?>
