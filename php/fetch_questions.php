<?php
require_once 'DB.php';
header('Content-Type: application/json');

try {
    $db = (new DB())->conn();

    // If your table is not quiz_questions, replace below with your table name
    $query = "SELECT id, question, option_a, option_b, option_c, option_d, correct_answer 
              FROM quiz_questions 
              WHERE TRIM(question) <> '' 
              ORDER BY id ASC";

    $res = $db->query($query);

    $questions = [];
    while ($row = $res->fetch_assoc()) {
        $questions[] = [
            'id' => (int)$row['id'],
            'question' => htmlspecialchars($row['question'], ENT_QUOTES, 'UTF-8'),
            'options' => [
                htmlspecialchars($row['option_a'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($row['option_b'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($row['option_c'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($row['option_d'], ENT_QUOTES, 'UTF-8')
            ],
            'answer' => match (strtoupper($row['correct_answer'])) {
                'A' => $row['option_a'],
                'B' => $row['option_b'],
                'C' => $row['option_c'],
                'D' => $row['option_d'],
                default => ''
            }
        ];
    }

    echo json_encode($questions, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
