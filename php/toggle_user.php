<?php
require_once 'DB.php';
header('Content-Type: application/json');
try{
$db=(new DB())->conn();
$data=json_decode(file_get_contents('php://input'),true);
$id=intval($data['id']);
$res=$db->query("SELECT suspended FROM users WHERE id=$id");
if(!$res->num_rows) throw new Exception("User not found");
$row=$res->fetch_assoc();
$new=$row['suspended']?0:1;
$db->query("UPDATE users SET suspended=$new WHERE id=$id");
echo json_encode(['status'=>'ok']);
}catch(Exception $e){echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);}
?>
