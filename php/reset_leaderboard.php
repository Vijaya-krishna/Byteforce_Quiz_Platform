<?php
require_once 'DB.php';
header('Content-Type: application/json');
try {
    $db = (new DB())->conn();
    $db->query("TRUNCATE TABLE results");
    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>
