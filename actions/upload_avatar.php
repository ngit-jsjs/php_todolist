<?php
// KHỞI ĐỘNG SESSION
// Dùng session để:
// - lấy thông tin đăng nhập (user_id)
// - lưu flash message (flash_success / flash_error) để hiển thị toast bên user.php
session_start();

// KẾT NỐI DATABASE
require '../includes/config.php';
// KIỂM TRA ĐĂNG NHẬP
// =======================
// auth_check.php thường:
// - kiểm tra $_SESSION['user_id']
// - nếu chưa login thì redirect
// - gán biến $user_id để dùng phía dưới
require '../includes/auth_check.php';

// 1) KIỂM TRA FILE CÓ ĐƯỢC GỬI LÊN KHÔNG + CÓ LỖI KHÔNG
// $_FILES['avatar'] chỉ tồn tại nếu form có input name="avatar"
// $_FILES['avatar']['error'] = 0 nghĩa là upload OK (PHP không báo lỗi)
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== 0) {
    // Lưu thông báo lỗi vào session để trang user.php hiển thị toast
    $_SESSION['flash_error'] = 'Upload ảnh thất bại';
    header("Location: ../pages/user.php");
    exit;
}

$file = $_FILES['avatar'];

if ($file['size'] > 2 * 1024 * 1024) {
    $_SESSION['flash_error'] = 'Ảnh quá lớn (tối đa 2MB)';
    header("Location: ../pages/user.php");
    exit;
}

$allowed = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowed)) {
    $_SESSION['flash_error'] = 'Chỉ cho phép JPG / PNG / WEBP';
    header("Location: ../pages/user.php");
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
$uploadPath = '../uploads/avatars/' . $filename;

/* xóa avatar cũ (nếu có) */
$old = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
$old->execute([$user_id]);
$oldAvatar = $old->fetchColumn();

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    $_SESSION['flash_error'] = 'Không thể lưu file ảnh';
    header("Location: ../pages/user.php");
    exit;
}

if ($oldAvatar && file_exists('../uploads/avatars/' . $oldAvatar)) {
    unlink('../uploads/avatars/' . $oldAvatar);
}

$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->execute([$filename, $user_id]);

$_SESSION['flash_success'] = 'Lưu avatar thành công!';
header("Location: ../pages/user.php");
exit;
?>