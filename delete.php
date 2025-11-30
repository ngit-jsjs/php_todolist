<?php
session_start();
require "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// Xóa task chỉ khi thuộc về user hiện tại
$conn->prepare("DELETE FROM tasks WHERE id=? AND user_id=?")->execute([$id, $user_id]);

// Kiểm tra có từ trang search không
$from = $_GET['from'] ?? '';
if ($from == 'search') {
    $params = [];
    foreach (['name', 'day', 'month', 'year', 'time', 'status', 'page'] as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            $params[] = $key . '=' . urlencode($_GET[$key]);
        }
    }
    header("Location: search.php?" . implode('&', $params));
} else {
    header("Location: index.php");
}
