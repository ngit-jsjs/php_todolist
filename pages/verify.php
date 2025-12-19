<?php
// KHỞI ĐỘNG SESSION
// Dùng session để quản lý đăng nhập, trạng thái người dùng
session_start();

// File config.php chứa thông tin kết nối CSDL (PDO)
require_once '../includes/config.php';

// $message: nội dung thông báo cho người dùng
// $success: đánh dấu xác thực thành công hay thất bại
$message = '';
$success = false;


// =======================
// KIỂM TRA TOKEN TRÊN URL
// =======================
// Ví dụ link: verify.php?token=abc123
if (isset($_GET['token'])) {

    // Lấy token từ URL
    $token = trim($_GET['token']);


    // =======================
    // KIỂM TRA TOKEN CÓ HỢP LỆ KHÔNG
    // =======================
    // - Token phải tồn tại trong bảng users
    // - Tài khoản đó CHƯA xác thực (is_verified = 0)
    $stmt = $conn->prepare("
        SELECT id 
        FROM users 
        WHERE verification_token = ? 
        AND is_verified = 0
    ");
    $stmt->execute([$token]);
    // Lấy thông tin user (nếu có)
    $user = $stmt->fetch();
    
        // =======================
    // NẾU TOKEN HỢP LỆ
    // =======================
    if ($user) {

        // Cập nhật:
        // - Đánh dấu tài khoản đã xác thực
        // - Xóa token để tránh dùng lại
        $stmt = $conn->prepare("
            UPDATE users 
            SET is_verified = 1, 
                verification_token = NULL 
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);

        // Thông báo thành công
        $message = 'Xác thực thành công! Bạn có thể đăng nhập ngay.';
        $success = true;

    } else {
        // Token sai hoặc đã được dùng
        $message = 'Link xác thực không hợp lệ hoặc đã được sử dụng!';
    }

} else {
    // Không có token trên URL
    $message = 'Thiếu mã xác thực!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực Email</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="login-page">
<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">


<div class="login-wrapper">

    <!-- NỘI DUNG THÔNG BÁO -->
   <h2>
    <?= $success 
        ? '<i class="fa fa-check" style="color:#28a745;"></i> Thành công!' 
        : '<i class="fa fa-times" style="color:#dc3545;"></i> Lỗi!' 
    ?>
    </h2>

    <!-- NÚT CHUYỂN SANG TRANG ĐĂNG NHẬP -->
    <p style="text-align:center;margin:20px 0;color:<?= $success ? '#27ae60' : '#d63031' ?>;">
        <?= $message ?>
    </p>
    
    <a href="login.php" class="login-btn"><img class="submit-icon" src="../assets/icon/heart (1).png" > Đăng nhập <img class="submit-icon" src="../assets/icon/heart (1).png" ></a>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>
