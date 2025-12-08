<?php
session_start();
require "../includes/config.php";

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

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="add-page">

<div class="add-container">
    <button class="dark-toggle small" id="darkToggle">🌙</button>
    <h1> Sửa Công Việc</h1>

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
        <input type="datetime-local" name="start" id="startInput"
            value="<?= date('Y-m-d\TH:i', strtotime($task['start_time'])) ?>">

        <label>Số ngày làm: <small>(tự động tính hạn chót)</small></label>
        <input type="number" id="daysInput" min="0" placeholder="VD: 7 ngày (0 = trong ngày)">

        <label>Hạn chót: <small>(để trống = vô thời hạn, phải sau thời gian bắt đầu)</small></label>
        <input type="datetime-local" name="end" id="endInput"
            value="<?= $task['end_time'] ? date('Y-m-d\TH:i', strtotime($task['end_time'])) : '' ?>">

        <button>Lưu thay đổi</button>
    </form>

    <a href="index.php" class="back">← Quay lại danh sách</a>

</div>

<script src="../script.js"></script>
<script>
const daysInput = document.getElementById("daysInput");
const startInput = document.getElementById("startInput");
const endInput = document.getElementById("endInput");

daysInput.addEventListener("input", () => {
    const days = parseInt(daysInput.value);
    if (isNaN(days) || days < 0) return;
    
    const start = startInput.value ? new Date(startInput.value) : new Date();
    
    if (days === 0) {
        start.setHours(23, 59, 0, 0);
    } else {
        start.setDate(start.getDate() + days);
    }
    
    const year = start.getFullYear();
    const month = String(start.getMonth() + 1).padStart(2, '0');
    const day = String(start.getDate()).padStart(2, '0');
    const hours = String(start.getHours()).padStart(2, '0');
    const minutes = String(start.getMinutes()).padStart(2, '0');
    
    endInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
});

startInput.addEventListener("change", () => {
    if (daysInput.value) daysInput.dispatchEvent(new Event('input'));
    validateEndTime();
});

endInput.addEventListener("change", () => {
    validateEndTime();
    
    if (!startInput.value || !endInput.value) return;
    
    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    const diffTime = end - start;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays >= 0) {
        daysInput.value = diffDays;
    }
});

function validateEndTime() {
    if (!startInput.value || !endInput.value) return;
    
    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    
    if (end <= start) {
        endInput.setCustomValidity('Thời gian kết thúc phải sau thời gian bắt đầu!');
        endInput.reportValidity();
    } else {
        endInput.setCustomValidity('');
    }
}
</script>

</body>
</html>
