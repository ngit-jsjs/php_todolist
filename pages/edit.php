<?php
require "../includes/config.php";
include '../includes/auth_check.php';
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
        try{
        $stmt = $conn->prepare("UPDATE tasks SET title=?, content=?, start_time=?, end_time=? WHERE id=? AND user_id=?");
        $stmt->execute([$title, $content, $start, $end, $id, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Sửa công việc thành công!'
            ];
        } else {
            // ⚠️ Không có gì thay đổi
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => ' Không có thay đổi nào được lưu!'
            ];
        }

        header("Location: home.php");
        exit();}

        catch (PDOException $e) {
        
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Sửa không thành công. Vui lòng thử lại!'
        ];
    }
    }
}
?>

<?php $pageTitle = 'Sửa Công Việc'; 
include '../includes/header.php';
?>

<body>
<div class="add-page">

<div class="add-container">
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

    <a href="home.php" class="back">← Quay lại danh sách</a>

</div>
</div>

<script src="../assets/js/script.js"></script>


</body>
</html>
