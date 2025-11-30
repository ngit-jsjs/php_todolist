<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($email, $token) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . '://' . $host . dirname($_SERVER['PHP_SELF']);
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ngtien1924@gmail.com'; // VD: ngtien1924@gmail.com
        $mail->Password = 'jctgifyzitxvmooy'; // App Password 16 ký tự (không có khoảng trắng)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom('ngtien1924@gmail.com', 'Todo List');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = '🌸 Xác thực tài khoản Todo List';
        
        $verifyLink = $baseUrl . "/verify.php?token=" . $token;
        $mail->Body = "
            <h2>🌸 Chào mừng bạn đến với Todo List! 🌸</h2>
            <p>Vui lòng click vào link bên dưới để xác thực email:</p>
            <a href='$verifyLink' style='background:#ff71c5;color:white;padding:10px 20px;text-decoration:none;border-radius:8px;display:inline-block;'>
                ✨ Xác thực ngay ✨
            </a>
            <p>Hoặc copy link này: <br>$verifyLink</p>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return "Lỗi: " . $e->getMessage();
    }
}
