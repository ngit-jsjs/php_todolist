<?php
session_start();
require_once '../includes/config.php';

$error = '';

if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                $error = 'Vui lòng xác thực email trước khi đăng nhập!';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email;
                header('Location: index.php');
                exit;
            }
        } else {
            $error = 'Email hoặc mật khẩu không đúng!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">

<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">

<div class="login-wrapper" id="loginCard">
    <button class="dark-toggle" id="darkToggle">🌙</button>
    <h2> Chào mừng trở lại </h2>
    <p class="sub">Đăng nhập để tiếp tục hành trình của bạn </p>

    <?php if ($error): ?>
        <div class="error-msg">❌ <?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" >
        <div class="login-input-box">
            <input type="email" name="email" required placeholder=" " autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Email</label>
        </div>

        <div class="login-input-box" style="position:relative;">
            <input type="password" name="password" id="password" required placeholder=" " autocomplete="new-password">
            <label>Mật khẩu</label>
            <span onclick="togglePassword()" id="toggleIcon"><img src="../assets/icon/eye (1).png" class="eye-icon"></span>
        </div>

        
        <button type="submit" class="login-btn">
            <img class="submit-icon" src="../assets/icon/heart (1).png" > 
            Đăng nhập 
            <img class="submit-icon" src="../assets/icon/heart (1).png" >
        </button>


        <a href="dangki.php" class="login-btn register">
            <img class="submit-icon" src="../assets/icon/right-arrow.png" > 
            Đăng kí 
            <img class="submit-icon" src="../assets/icon/right-arrow.png" >
        </a>
        
    </form>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>
