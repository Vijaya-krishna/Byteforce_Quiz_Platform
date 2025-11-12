<?php
require_once 'User.php';
session_start();
class Auth {
    private $userModel;
    public function __construct(){ $this->userModel = new User(); }

    public function login($username, $password){
        $u = $this->userModel->getByUsername($username);
        if(!$u) return false;
        if(password_verify($password, $u['password_hash'])){
            if(intval($u['suspended']) === 1) return 'suspended';
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            return true;
        }
        return false;
    }

    public function logout(){ session_destroy(); }
}
?>
