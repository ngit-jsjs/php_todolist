<?php
session_start();
require "./includes/config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ./pages/dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Cập nhật tiến độ chỉ khi task thuộc về user
$conn->prepare("UPDATE tasks SET progress=? WHERE id=? AND user_id=?")->execute([$_POST['progress'], $_POST['id'], $user_id]);
header("Location: ./pages/index.php");
