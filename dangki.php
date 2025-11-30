<?php
session_start();
require_once 'config.php';
require_once 'send_email.php';

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
    <title>Register Cute WOW ✨</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<div class="login-heart">💗</div>
<div class="login-heart">💖</div>
<div class="login-heart">💕</div>
<div class="login-heart">💗</div>
<div class="login-heart">💖</div>
<div class="login-heart">💕</div>
<div class="login-heart">💗</div>
<div class="login-heart">💖</div>
<div class="login-heart">💖</div>
<div class="login-heart">💖</div>
<div class="login-heart">💖</div>

<div class="login-wrapper" id="loginCard">
    <button class="dark-toggle" id="darkToggle">🌙</button>
    <h2>🌸 Create Account 🌸</h2>
    <p class="sub">Join us on this cute journey 💖</p>

    <?php if ($error): ?>
        <div class="error-msg">❌ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background:#e6ffe6;color:#27ae60;padding:12px;border-radius:12px;margin-bottom:20px;text-align:center;font-size:14px;border:1px solid #a8e6a8;">✅ <?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST" >
        <div class="login-input-box">
            <input type="text" name="username" required placeholder=" " autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            <label>Username</label>
        </div>

        <div class="login-input-box">
            <input type="email" name="email" required placeholder=" " autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Email</label>
        </div>

        <div class="login-input-box" style="position:relative;">
            <input type="password" name="password" id="password" required placeholder=" " autocomplete="new-password">
            <label>Password</label>
            <span onclick="togglePassword()" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:20px;" id="toggleIcon">👁️</span>
        </div>

        <button type="submit" class="login-btn">✨ Register ✨</button>
        <a href="dangnhap.php" class="login-btn register">🌟 Back to Login 🌟</a>
        
    </form>
</div>

<script>
    const card = document.getElementById("loginCard");
    const darkToggle = document.getElementById("darkToggle");
    const body = document.body;

    card.addEventListener("mouseover", () => {
        card.style.transform = "translate(-50%, -50%) scale(1.02)";
    });

    card.addEventListener("mouseout", () => {
        card.style.transform = "translate(-50%, -50%) scale(1)";
    });

    if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
        darkToggle.textContent = "☀️";
    }

    darkToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        const isDark = body.classList.contains("dark-mode");
        darkToggle.textContent = isDark ? "☀️" : "🌙";
        localStorage.setItem("darkMode", isDark);
    });

    function togglePassword() {
        const pwd = document.getElementById("password");
        const icon = document.getElementById("toggleIcon");
        if (pwd.type === "password") {
            pwd.type = "text";
            icon.textContent = "🙈";
        } else {
            pwd.type = "password";
            icon.textContent = "👁️";
        }
    }
</script>

</body>
</html>