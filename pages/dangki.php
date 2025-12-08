<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/send_email.php';

$error = '';
$success = '';

if ($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser && $existingUser['is_verified'] == 1) {
            $error = 'Email đã được đăng ký và xác thực!';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            
            $emailResult = sendVerificationEmail($email, $token);
            if ($emailResult === true) {
                if ($existingUser) {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, verification_token = ? WHERE email = ?");
                    $stmt->execute([$username, $hashedPassword, $token, $email]);
                    $success = 'Email xác thực đã được gửi lại! Vui lòng kiểm tra email.';
                } else {
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $hashedPassword, $token]);
                    $success = 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.';
                }
            } else {
                $error = 'Không thể gửi email xác thực. ' . $emailResult;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng kí</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">

<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">


<div class="login-wrapper" id="loginCard">
    <button class="dark-toggle" id="darkToggle">🌙</button>
    <h2> Tạo tài khoản </h2>
    <p class="sub">Tham gia cùng chúng tôi trên hành trình này </p>

    <?php if ($error): ?>
        <div class="error-msg">❌ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background:#e6ffe6;color:#27ae60;padding:12px;border-radius:12px;margin-bottom:20px;text-align:center;font-size:14px;border:1px solid #a8e6a8;">✅ <?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST" >
        <div class="login-input-box">
            <input type="text" name="username" required placeholder=" " autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            <label>Tên người dùng</label>
        </div>

        <div class="login-input-box">
            <input type="email" name="email" required placeholder=" " autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Email</label>
        </div>

        <div class="login-input-box" style="position:relative;">
            <input type="password" name="password" id="password" required placeholder=" " autocomplete="new-password">
            <label>Mật khẩu</label>
            <span onclick="togglePassword()" id="toggleIcon"><img src="../assets/icon/eye (1).png" class="eye-icon"></span>
        </div>

        <button type="submit" class="login-btn"><img class="submit-icon" src="../assets/icon/heart (1).png" > Đăng kí <img class="submit-icon" src="../assets/icon/heart (1).png" ></button>
        <a href="dangnhap.php" class="login-btn register"><img class="submit-icon" src="../assets/icon/left-arrow.png" > Đăng nhập <img class="submit-icon" src="../assets/icon/left-arrow.png" ></a>
        
    </form>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>