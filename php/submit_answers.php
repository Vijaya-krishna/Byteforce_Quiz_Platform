<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msg' => 'not_authenticated']);
    exit;
}

require_once 'DB.php';

try {
    $db = (new DB())->conn();
    $user_id = intval($_SESSION['user_id']);
    $username = $_SESSION['username'] ?? 'Unknown';
    $score = 0;
    $total = 0;

    if (($_SERVER['CONTENT_TYPE'] ?? '') === 'application/json') {
        $input = json_decode(file_get_contents('php://input'), true);
        $score = intval($input['score'] ?? 0);
        $total = intval($input['total'] ?? 0);
    } else {
        $score = intval($_POST['score'] ?? 0);
        $total = intval($_POST['total'] ?? 0);
    }

    if ($score < 0) $score = 0;

    // find previous high score
    $highRes = $db->query("SELECT MAX(score) AS high FROM results");
    $highRow = $highRes ? $highRes->fetch_assoc() : ['high' => 0];
    $prevHigh = intval($highRow['high'] ?? 0);
    $is_highscore = $score > $prevHigh;

    // Update current user record
    $db->begin_transaction();

    $stmt = $db->prepare("SELECT id, score, attempts FROM results WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $attempts = intval($row['attempts']) + 1;
        $stmt = $db->prepare("UPDATE results SET score = ?, attempts = ?, last_attempt = NOW() WHERE user_id = ?");
        $stmt->bind_param("iii", $score, $attempts, $user_id);
        $stmt->execute();
    } else {
        $attempts = 1;
        $stmt = $db->prepare("INSERT INTO results (user_id, score, attempts, last_attempt) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $user_id, $score, $attempts);
        $stmt->execute();
    }

    // Insert into attempts log
    $stmt = $db->prepare("INSERT INTO quiz_attempts (user_id, username, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $user_id, $username, $score, $total);
    $stmt->execute();

    // fetch top 3 for leaderboard overlay
    $top3 = [];
    $tres = $db->query("SELECT username, score FROM results ORDER BY score DESC LIMIT 3");
    while ($r = $tres->fetch_assoc()) $top3[] = $r;

    $db->commit();

    echo json_encode([
        'status' => 'ok',
        'score' => $score,
        'attempts' => $attempts,
        'is_highscore' => $is_highscore,
        'top3' => $top3
    ]);
    exit;

} catch (Exception $e) {
    if (isset($db) && $db->errno) $db->rollback();
    error_log("submit_answers error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'server_error']);
    exit;
}
?>
