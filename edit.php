<?php
session_start();
require "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id = $_GET["id"];

// Kiểm tra quyền sở hữu task
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header('Location: index.php');
    exit;
}

$error = "";

if ($_POST) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $start = $_POST["start"];
    $end = $_POST["end"] ?: null;
    
    if (!$title) {
        $error = "Thiếu tên công việc!";
    } elseif ($end && strtotime($end) <= strtotime($start)) {
        $error = "Hạn chót phải sau thời gian bắt đầu!";
    } elseif ($end && strtotime($end) < time()) {
        $error = "Hạn chót không được trước ngày hôm nay!";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET title=?, content=?, start_time=?, end_time=? WHERE id=? AND user_id=?");
        $stmt->execute([$title, $content, $start, $end, $id, $user_id]);
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa công việc</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">

<link rel="stylesheet" href="style.css">

</head>

<body class="add-page">

<div class="add-container">
    <button class="dark-toggle small" id="darkToggle">🌙</button>
    <h1>✏️ Sửa Công Việc</h1>

    <?php if ($error): ?>
        <div class="error-box">
            <?= $error ?>
        </div>
    <?php endif ?>

    <form method="POST">

        <label>Tên công việc:</label>
        <input name="title" value="<?= htmlspecialchars($task['title']) ?>" required>

        <label>Nội dung:</label>
        <textarea name="content"><?= htmlspecialchars($task['content']) ?></textarea>

        <label>Bắt đầu:</label>
        <input type="datetime-local" name="start" 
            value="<?= date('Y-m-d\TH:i', strtotime($task['start_time'])) ?>">

        <label>Hạn chót: <small>(để trống = vô thời hạn)</small></label>
        <input type="datetime-local" name="end"
            value="<?= $task['end_time'] ? date('Y-m-d\TH:i', strtotime($task['end_time'])) : '' ?>">

        <button>Lưu thay đổi</button>
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
