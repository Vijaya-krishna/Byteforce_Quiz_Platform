<?php
require_once 'DB.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $db = (new DB())->conn();
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $db->prepare("INSERT INTO quiz_questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",
        $data['question'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $data['correct']
    );
    $stmt->execute();
    echo json_encode(['status'=>'ok']);
} catch (Exception $e) {
    echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
}
?>
