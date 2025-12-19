<?php
// Component kiểm tra đăng nhập và lấy username
session_start();
require "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$username = $stmt->fetchColumn();
?>
