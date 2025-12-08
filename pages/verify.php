<?php
session_start();
require_once '../includes/config.php';

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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-page">
<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">


<div class="login-wrapper">
   <h2>
    <?= $success 
        ? '<i class="fa fa-check" style="color:#28a745;"></i> Thành công!' 
        : '<i class="fa fa-times" style="color:#dc3545;"></i> Lỗi!' 
    ?>
</h2>

    <p style="text-align:center;margin:20px 0;color:<?= $success ? '#27ae60' : '#d63031' ?>;">
        <?= $message ?>
    </p>
    
    <a href="dangnhap.php" class="login-btn"><img class="submit-icon" src="../assets/icon/heart (1).png" > Đăng nhập ngay <img class="submit-icon" src="../assets/icon/heart (1).png" ></a>
</div>

<script src="../script.js"></script>

</body>
</html>
