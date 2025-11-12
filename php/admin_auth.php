<?php
session_start();
require_once 'DB.php';

$db = new DB();
$conn = $db->conn();

// Ensure the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_login.php?status=invalid_request");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header("Location: admin_login.php?status=empty");
    exit;
}

// Fetch admin data from the database
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $admin = $res->fetch_assoc();

    // Verify password hash
    if (password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['LAST_ACTIVITY'] = time();
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: admin_login.php?status=wrong_password");
        exit;
    }
} else {
    header("Location: admin_login.php?status=not_found");
    exit;
}
?>
