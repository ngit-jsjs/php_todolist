<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'send_email.php';

echo "<h2>🔍 Test Gửi Email</h2>";
echo "<p>Đang kiểm tra cấu hình email...</p>";

// Test với email của bạn
$testEmail = "ngtien1924@gmail.com"; // Thay bằng email thật để test
$testToken = "jctgifyzitxvmooy";

echo "<hr>";
echo "<h3>Kết quả:</h3>";

$result = sendVerificationEmail($testEmail, $testToken);

if ($result === true) {
    echo "<p style='color:green;'>✅ Gửi email thành công!</p>";
} else {
    echo "<p style='color:red;'>❌ Lỗi: " . htmlspecialchars($result) . "</p>";
}

echo "<hr>";
echo "<h3>Thông tin PHP:</h3>";
echo "Error Log: " . ini_get('error_log') . "<br>";
echo "OpenSSL: " . (extension_loaded('openssl') ? '✅ Đã bật' : '❌ Chưa bật') . "<br>";
?>
