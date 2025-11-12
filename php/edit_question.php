<?php
ini_set('session.cookie_path', '/'); // ensures same cookie across subfolders
ini_set('session.gc_maxlifetime', 3600); // keep session 1hr
session_start();
require_once 'DB.php';
header('Content-Type: application/json');

try {
    $db = new DB();
    $conn = $db->conn();

    $input = json_decode(file_get_contents('php://input'), true);

    $id = intval($input['id'] ?? 0);
    $q = trim($input['question'] ?? '');
    $a = trim($input['option_a'] ?? '');
    $b = trim($input['option_b'] ?? '');
    $c = trim($input['option_c'] ?? '');
    $d = trim($input['option_d'] ?? '');
    $correct = strtoupper(trim($input['correct'] ?? ''));

    if (!$id || !$q || !$a || !$b || !$correct) {
        echo json_encode(['status' => 'error', 'msg' => 'Missing required fields']);
        exit;
    }

    if (!in_array($correct, ['A', 'B', 'C', 'D'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid correct answer']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE quiz_questions 
                            SET question=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=? 
                            WHERE id=?");
    $stmt->bind_param("ssssssi", $q, $a, $b, $c, $d, $correct, $id);
    $stmt->execute();

    echo json_encode(['status' => 'ok', 'msg' => 'Question updated successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>
