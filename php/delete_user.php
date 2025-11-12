<?php
require_once 'DB.php';
header('Content-Type: application/json');
try{
$db=(new DB())->conn();
$data=json_decode(file_get_contents('php://input'),true);
$id=intval($data['id']);
$db->query("DELETE FROM users WHERE id=$id");
echo json_encode(['status'=>'ok']);
}catch(Exception $e){echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);}
?>
