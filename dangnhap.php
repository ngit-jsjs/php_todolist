<?php
session_start();
require_once 'config.php';

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
    <title>Login Cute WOW ✨</title>
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
    <h2>🌸 Welcome Back 🌸</h2>
    <p class="sub">Login to continue your cute journey 💖</p>

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
            <label>Password</label>
            <span onclick="togglePassword()" style="position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:20px;" id="toggleIcon">👁️</span>
        </div>

        <button type="submit" class="login-btn">✨ Login ✨</button>
        <a href="dangki.php" class="login-btn register">🌟 Register 🌟</a>
        
    </form>
</div>

<script>
    const card = document.getElementById("loginCard");
    const darkToggle = document.getElementById("darkToggle");
    const body = document.body;

    if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
        darkToggle.textContent = "☀️";
    }

    card.addEventListener("mouseover", () => {
        card.style.transform = "translate(-50%, -50%) scale(1.02)";
    });

    card.addEventListener("mouseout", () => {
        card.style.transform = "translate(-50%, -50%) scale(1)";
    });

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
