<?php
session_start();

// If user already logged in, go straight to quiz start
if (isset($_SESSION['user_id'])) {
    header("Location: php/start_quiz.php");
    exit;
}

// Otherwise, show login/register page
include 'index.html';
?>
