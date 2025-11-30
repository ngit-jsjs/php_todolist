<?php
session_start();
require_once 'config.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $message = 'Xác thực thành công! Bạn có thể đăng nhập ngay.';
        $success = true;
    } else {
        $message = 'Link xác thực không hợp lệ hoặc đã được sử dụng!';
    }
} else {
    $message = 'Thiếu mã xác thực!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực Email</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-heart">💗</div>
<div class="login-heart">💖</div>
<div class="login-heart">💕</div>

<div class="login-wrapper">
    <h2><?= $success ? '✅ Thành công!' : '❌ Lỗi!' ?></h2>
    <p style="text-align:center;margin:20px 0;color:<?= $success ? '#27ae60' : '#d63031' ?>;">
        <?= $message ?>
    </p>
    <a href="dangnhap.php" class="login-btn">🌟 Đăng nhập ngay 🌟</a>
</div>

<script>
if (localStorage.getItem("darkMode") === "true") {
    document.body.classList.add("dark-mode");
}


</script>

</body>
</html>
