<?php
require_once __DIR__ . '/DB.php';

class User {
    private $conn;
    public function __construct() {
        $db = new DB();
        $this->conn = $db->conn();
    }

    public function exists($username) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function create($username, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);
        if(!$stmt->execute()){
            $err = $this->conn->error;
            $stmt->close();
            throw new Exception("DB error: " . $err);
        }
        $stmt->close();
        return true;
    }

    public function verify($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, password_hash, suspended FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) { $stmt->close(); return false; }
        $user = $res->fetch_assoc();
        $stmt->close();

        if (intval($user['suspended']) === 1) {
            // throw Exception or return special value
            throw new Exception("Account suspended");
        }
        if (password_verify($password, $user['password_hash'])) {
            // return user row
            return ['id' => $user['id'], 'username' => $user['username']];
        }
        return false;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, suspended, warning_count FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
}
?>
