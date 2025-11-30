<?php
session_start();
require "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$error = "";

if ($_POST) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $start = $_POST["start"] ?: date("Y-m-d H:i:s");
    $end = $_POST["end"] ?: null;

    if (!$title) {
        $error = "Thiếu tên công việc!";
    } elseif ($end && strtotime($end) <= strtotime($start)) {
        $error = "Hạn chót phải sau thời gian bắt đầu!";
    } elseif ($end && strtotime($end) < time()) {
        $error = "Hạn chót không được trước ngày hôm nay!";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, content, start_time, end_time, progress) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$user_id, $title, $content, $start, $end]);
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm công việc</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
<link rel="stylesheet" href="style.css">
</head>

<body class="add-page">

<div class="add-container">
    <button class="dark-toggle" id="darkToggle" style="width: 26px; height: 26px; font-size: 13px; top: 15px; right: 15px; padding: 0;">🌙</button>
    <h1>➕ Thêm Công Việc</h1>

    <?php if ($error): ?>
        <div style="background: #ffe4e4; color: #d63031; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
            <?= $error ?>
        </div>
    <?php endif ?>

    <form method="POST">

        <label>Tên công việc:</label>
        <input name="title" placeholder="Nhập tên công việc..." value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>

        <label>Nội dung:</label>
        <textarea name="content" placeholder="Nội dung chi tiết..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>

        <label>Bắt đầu:</label>
        <input type="datetime-local" name="start" value="<?= htmlspecialchars($_POST['start'] ?? '') ?>">

        <label>Hạn chót: <small>(để trống = vô thời hạn)</small></label>
        <input type="datetime-local" name="end" value="<?= htmlspecialchars($_POST['end'] ?? '') ?>">

        <button>Thêm công việc</button>
    </form>

    <a href="index.php" class="back">← Quay lại danh sách</a>
</div>

<script>
const darkToggle = document.getElementById("darkToggle");
const body = document.body;

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
</script>

</body>
</html>
