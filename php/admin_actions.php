<?php
require_once 'DB.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: ../index.html");
  exit;
}

$db = new DB();
$conn = $db->conn();

$action = $_GET['action'] ?? '';

switch ($action) {
  
  // 🧩 Get all questions
  case 'get_questions':
    $res = $conn->query("SELECT * FROM quiz_questions ORDER BY id ASC");
    $data = [];
    while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    break;

  // 🧩 Add new question
  case 'add_question':
    $q = $_POST['question'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $correct = $_POST['correct'];
    $stmt = $conn->prepare("INSERT INTO quiz_questions (question, a, b, c, d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $q, $a, $b, $c, $d, $correct);
    $stmt->execute();
    echo "✅ Question added successfully.";
    break;

  // 🧩 Delete a question
  case 'delete_question':
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM quiz_questions WHERE id = $id");

    // 🔥 Auto-renumber all IDs after deletion
    $conn->query("SET @count = 0;");
    $conn->query("UPDATE quiz_questions SET id = (@count := @count + 1) ORDER BY id;");
    $conn->query("ALTER TABLE quiz_questions AUTO_INCREMENT = 1;");

    echo "🗑️ Question deleted and IDs renumbered.";
    break;

  // 🧩 Get all users
  case 'get_users':
    $res = $conn->query("SELECT id, username, suspended FROM users ORDER BY id ASC");
    $data = [];
    while ($row = $res->fetch_assoc()) $data[] = $row;
    echo json_encode($data);
    break;

  // 🧩 Toggle user suspension
  case 'toggle_user':
    $id = intval($_GET['id']);
    $conn->query("UPDATE users SET suspended = 1 - suspended WHERE id = $id");
    echo "User status updated.";
    break;

  // 🧩 Delete user
  case 'delete_user':
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM users WHERE id = $id");
    echo "User deleted.";
    break;

  // 🧩 Reset quiz results
  case 'reset_results':
    $conn->query("DELETE FROM results");
    $conn->query("DELETE FROM quiz_attempts");
    echo "All results cleared.";
    break;

  default:
    echo "Invalid action.";
}
?>
