<?php
require_once 'DB.php';
header('Content-Type: application/json');

try {
    $db = new DB();
    $conn = $db->conn();

    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);

    if (!$id) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid question ID']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'ok', 'msg' => 'Question deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Question not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>
