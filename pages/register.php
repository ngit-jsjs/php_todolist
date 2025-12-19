<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/send_email.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================
    // 1️⃣ LẤY & LÀM SẠCH INPUT
    // ========================
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {

        
        $pwErrors = [];

        if (strlen($password) < 8) {
            $pwErrors[] = 'Mật khẩu phải có ít nhất 8 ký tự';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $pwErrors[] = 'Mật khẩu phải chứa ít nhất 1 chữ thường';
        }
        if (!preg_match('/\d/', $password)) {
            $pwErrors[] = 'Mật khẩu phải chứa ít nhất 1 chữ số';
        }
       

        if ($password !== $confirm) {
            $pwErrors[] = 'Mật khẩu nhập lại không khớp';
        }


        if ($pwErrors) {
            $error = implode('. ', $pwErrors) . '.';
        } else {

            // ========================
            // 3️⃣ KIỂM TRA EMAIL
            // ========================
            $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if ($existingUser && $existingUser['is_verified'] == 1) {
                $error = 'Email đã được đăng ký và xác thực!';
            } else {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $token = bin2hex(random_bytes(32));

                $sendMail = sendVerificationEmail($email, $token);

                if ($sendMail === true) {

                    if ($existingUser) {
                        $stmt = $conn->prepare(
                            "UPDATE users 
                             SET username = ?, password = ?, verification_token = ? 
                             WHERE email = ?"
                        );
                        $stmt->execute([$username, $hashedPassword, $token, $email]);
                        $success = 'Email xác thực đã được gửi lại!';
                    } else {
                        $stmt = $conn->prepare(
                            "INSERT INTO users (username, email, password, verification_token) 
                             VALUES (?, ?, ?, ?)"
                        );
                        $stmt->execute([$username, $email, $hashedPassword, $token]);
                        $success = 'Đăng ký thành công! Vui lòng kiểm tra email.';
                    }

                } else {
                    $error = 'Không thể gửi email xác thực. ' . $sendMail;
                }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="login-page">

<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">


<div class="login-wrapper" id="loginCard">
    <button class="dark-toggle" id="darkToggle"><i class="fa fa-moon-o" aria-hidden="true"></i></button>
    <h2> Tạo tài khoản </h2>
    <p class="sub">Tham gia cùng chúng tôi trên hành trình này </p>

    <?php if ($error): ?>
        <div class="error-msg"><i class="fa fa-times" style="color:#dc3545;"></i> <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background:#e6ffe6;color:#27ae60;padding:12px;border-radius:12px;margin-bottom:20px;text-align:center;font-size:14px;border:1px solid #a8e6a8;"><i class="fa fa-check" style="color:#28a745;"></i>  <?= $success ?></div>
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
            <span id="toggleIcon"
                onclick="togglePasswordById('password', this)">
                <img src="../assets/icon/eye (1).png" class="eye-icon">
            </span>
        </div>
        <div class="login-input-box" style="position:relative;">
        <input type="password"
            name="confirm_password"
            id="confirm_password"
            required
            placeholder=" ">
        <label>Nhập lại mật khẩu</label>

        <span class="toggle-eye"
            onclick="togglePasswordById('confirm_password', this)">
            <img src="../assets/icon/eye (1).png" class="eye-icon">
        </span>

    </div>

        

        <button type="submit" class="login-btn"> Đăng kí </button>
        <a href="login.php" class="login-btn register"> Đăng nhập </a>
        
    </form>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>