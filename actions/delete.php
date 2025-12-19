<?php
session_start();
require "../includes/config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// Xóa task chỉ khi thuộc về user hiện tại
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);

// Set toast theo kết quả
if ($stmt->rowCount() > 0) {
    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Xóa công việc thành công!'
    ];
} else {
    $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Xóa công việc thất bại hoặc công việc không tồn tại!'
    ];
}
// Kiểm tra có từ trang search không
$from = $_GET['from'] ?? '';
if ($from == 'search') {
    $params = [];
    foreach (['name', 'day', 'month', 'year', 'time', 'status', 'page'] as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            $params[] = $key . '=' . urlencode($_GET[$key]);
        }
    }
    header("Location: ../pages/search.php?" . implode('&', $params));
} else {
    header("Location: ../pages/home.php");
}
exit;