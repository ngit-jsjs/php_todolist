<?php
session_start();
if (!empty($_SESSION['flash'])) {
    $msg = $_SESSION['flash']['message'];
    echo "<script>alert(" . json_encode($msg) . ");</script>";
    unset($_SESSION['flash']);
}


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
                header('Location: home.php');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="login-page">

<img src="../assets/icon/background.jpg" class="login-bg-left">
<img src="../assets/icon/background2.jpg" class="login-bg-right">

<div class="login-wrapper" id="loginCard">
    <button class="dark-toggle" id="darkToggle"> <i class="fa fa-moon-o"></i></button>
    <h2> Chào mừng trở lại </h2>
    <p class="sub">Đăng nhập để tiếp tục hành trình của bạn </p>

    <?php if ($error): ?>
        <div class="error-msg"><i class="fa fa-times" style="color:#dc3545;"></i> <?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" >
        <div class="login-input-box">
            <input type="email" name="email" required placeholder=" " autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Email</label>
        </div>

        <div class="login-input-box" style="position:relative;">
            <input type="password" name="password" id="password" required placeholder=" " autocomplete="new-password">
            <label>Mật khẩu</label>
            <span class="toggle-eye"
                onclick="togglePasswordById('password', this)">
                <img src="../assets/icon/eye (1).png" class="eye-icon">
            </span>

        </div>

        
        <button type="submit" class="login-btn">
            Đăng nhập 
        </button>


        <a href="register.php" class="login-btn register">
            Đăng kí 
        </a>
        
    </form>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'account_deleted'): ?>
  <div id="toast"
       class="toast success"
       data-message="✔ Tài khoản đã được xóa thành công!">
    <span class="toast-text"></span>
    <button class="toast-close">×</button>
  </div>
<?php endif; ?>

<script src="../assets/js/script.js"></script>

</body>
</html>
