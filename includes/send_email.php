<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

function sendVerificationEmail($email, $token) {
   
    $baseUrl = "https://tickytock.kesug.com";
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        
        $mail->Subject = 'Xác thực tài khoản Todo List';
        
        $verifyLink = $baseUrl . "/verify.php?token=" . urlencode($token);
        $mail->AltBody = "Xác thực tài khoản tại link: $verifyLink";
       $mail->Body = <<<HTML
        <p>Xin chào,</p>

        <p>Bạn vừa đăng ký tài khoản tại <b>Todo List Ticky-Tock</b>.</p>

        <p>Vui lòng xác thực email bằng cách mở link sau:</p>

        <p>
        <a href="$verifyLink">$verifyLink</a>
        </p>

        <p>Nếu bạn không đăng ký tài khoản này, hãy bỏ qua email.</p>

        HTML;

        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return "Lỗi: " . $e->getMessage();
    }
}
