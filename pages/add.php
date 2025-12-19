<?php
require "../includes/config.php";
include '../includes/auth_check.php';

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
        try{
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, content, start_time, end_time, progress) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$user_id, $title, $content, $start, $end]);
        // THÀNH CÔNG
        $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Thêm công việc thành công!'
        ];
        header("Location: home.php");
        exit();
        }
        catch(PDOException $e){
        $_SESSION['toast'] = [
        'type' => 'error',
        'message' => 'Không thể thêm công việc. Vui lòng thử lại!'
        ];
        }
    }
}
?>

<?php $pageTitle = 'Thêm Công Việc'; 
include '../includes/header.php';
?>
<body>
<div class="add-page">
    <div class="add-container">        
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
            <input type="datetime-local" name="start" id="startInput" value="<?= htmlspecialchars($_POST['start'] ?? date('Y-m-d\TH:i')) ?>">

            <label>Số ngày làm: <small>(tự động tính hạn chót)</small></label>
            <input type="number" id="daysInput" min="0" placeholder="VD: 7 ngày (0 = trong ngày)">

            <label>Hạn chót: <small>(để trống = vô thời hạn, phải sau thời gian bắt đầu)</small></label>
            <input type="datetime-local" name="end" id="endInput" value="<?= htmlspecialchars($_POST['end'] ?? '') ?>">

            <button>Thêm công việc</button>
        </form>

        <a href="home.php" class="back">← Quay lại danh sách</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
