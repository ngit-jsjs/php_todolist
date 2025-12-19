<?php 
// KIỂM TRA ĐĂNG NHẬP
// File auth_check.php đảm nhiệm:
// - session_start()
// - kiểm tra $_SESSION['user_id']
// - nếu chưa đăng nhập thì redirect sang trang đăng nhập
// - nếu hợp lệ thì gán $user_id
include '../includes/auth_check.php'; 


// TRUY VẤN THÔNG TIN USER
// Lấy thông tin người dùng đang đăng nhập dựa trên $user_id
$stmt = $conn->prepare("
    SELECT id, username, email, created_at, is_verified, avatar 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$user_id]);

// Lấy dữ liệu user dưới dạng mảng associative
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// KIỂM TRA USER CÓ TỒN TẠI KHÔNG
// Phòng trường hợp session lỗi hoặc user bị xóa trong db
if (!$user) {
    die("Không tìm thấy người dùng!");
}
?>


<!-- Dùng cho <title> trong header.php -->
<?php $pageTitle = 'Ticky-Tock';
// Nhúng header chung (HTML <head>, menu, CSS...)
    include '../includes/header.php'; ?>

<body>

<!-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG (FLASH MESSAGE)
// Thường được set sau khi upload avatar thành công     -->
<?php if (!empty($_SESSION['flash_success'])): ?>
  <div id="toast"
       class="toast success"
       data-message="<?= htmlspecialchars($_SESSION['flash_success']) ?>">
    <span class="toast-text"></span>
    <button class="toast-close">×</button>
  </div>
  <!-- Xóa session để toast chỉ hiện 1 lần -->
  <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>


<!--  HIỂN THỊ THÔNG BÁO LỖI (FLASH MESSAGE) -->
<?php if (!empty($_SESSION['flash_error'])): ?>
  <div id="toast"
       class="toast error"
       data-message="<?= htmlspecialchars($_SESSION['flash_error']) ?>">
    <span class="toast-text"></span>
    <button class="toast-close">×</button>
  </div>
    <!-- Xóa session để toast chỉ hiện 1 lần -->
  <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="user-profile-wrapper">
  <div class="user-card">

     <!-- FORM UPLOAD AVATAR -->
<!-- Khi bấm Lưu avatar, dữ liệu sẽ gửi sang file PHP này để xử lý
Upload file bắt buộc dùng POST -->
<form class="avatar-form" action="../actions/upload_avatar.php" method="post" enctype="multipart/form-data">
    <label class="avatar-upload">
    <img id="avatarPreview"
        src="<?= !empty($user['avatar'])
                ? '../uploads/avatars/' . $user['avatar']
                : '../assets/icon/user_cute.jpg' ?>"
        alt="Avatar">
<!-- Nếu user đã có avatar load ảnh từ uploads/avatars/
Nếu chưa có, dùng ảnh mặc định user_cute.jpg -->
    <span class="edit-icon">✎</span>
    <input type="file" name="avatar" id="avatarInput" accept="image/*" hidden>
    </label>
  <button type="submit" class="btn small">Lưu avatar</button>
</form>

    <!-- Username -->

    <h2 class="username"><?= htmlspecialchars($user['username']) ?></h2>

    <!-- Badge xác thực -->
    <div class="verify-badge <?= $user['is_verified'] ? 'verified' : 'not-verified' ?>">
      <?= $user['is_verified'] ? '✔ Đã xác thực' : '✖ Chưa xác thực' ?>
    </div>

    <!-- Info -->
    <div class="user-info">
      <div class="info-row">
        <span>Email</span>
        <p><?= htmlspecialchars($user['email']) ?></p>
      </div>

      <div class="info-row">
        <span>Ngày tham gia</span>
        <p><?= htmlspecialchars($user['created_at']) ?></p>
      </div>
    </div>

    <!-- Actions -->
    <div class="user-actions">
      <a href="home.php" class="btn">← Quay lại</a>

      <a href="../actions/delete_account.php"
         class="btn red"
         onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản?')">
        🗑 Xóa tài khoản
      </a>
    </div>

  </div>
</div>

<script src="../assets/js/script.js"></script>

</body>
</html>
