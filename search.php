<?php
require "config.php";

$name  = $_GET["name"]  ?? "";
$day   = $_GET["day"]   ?? "";
$month = $_GET["month"] ?? "";
$year  = $_GET["year"]  ?? "";
$time  = $_GET["time"]  ?? "";
$status= $_GET["status"] ?? "";

$sql = "SELECT * FROM tasks WHERE 1";

// lọc tên
if ($name) {
    $sql .= " AND title LIKE '%$name%'";
}

// lọc ngày
if ($day) {
    $sql .= " AND DATE(start_time) = '$day'";
}

// lọc tháng / năm
if ($month) $sql .= " AND MONTH(start_time) = $month";
if ($year)  $sql .= " AND YEAR(start_time) = $year";

// lọc giờ phút
if ($time) {
    $sql .= " AND TIME(start_time) = '$time'";
}

// lọc trạng thái
if ($status == "done") {
    $sql .= " AND progress = 100";
} else if ($status == "overdue") {
    $sql .= " AND end_time < NOW() AND progress < 100";
} else if ($status == "soon") {
    $sql .= " AND end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)";
} else if ($status == "new") {
    $sql .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
}

$stmt = $conn->query($sql);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// gom nhóm theo ngày
$group = [];
foreach ($tasks as $t) {
    $day = date("d/m/Y", strtotime($t['start_time']));
    $group[$day][] = $t;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả tìm kiếm - Todo Cute Premium</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top">
    <h1>🔍 Kết quả tìm kiếm</h1>
    <a class="btn" href="index.php">← Quay lại</a>
</div>

<div class="day-container">
<?php if (empty($tasks)): ?>
    <p style="text-align: center; color: #ff66c4; font-size: 18px;">Không tìm thấy kết quả nào!</p>
<?php else: ?>
    <?php foreach ($group as $day => $items): ?>
        <div class="day-box">
            <h2>📅 <?= $day ?></h2>
            <?php foreach ($items as $t): ?>
                <div class="task <?= ($t['progress'] == 100 ? 'done' : '') ?>">
                    <h3>📝 <?= htmlspecialchars($t['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
                    <p>⏰ Bắt đầu: <b><?= $t['start_time'] ?></b></p>
                    <p>🚀 Hạn chót: <b><?= $t['end_time'] ?></b></p>
                    <p>🎯 Tiến độ: <b><?= $t['progress'] ?>%</b></p>
                    <a href="edit.php?id=<?= $t['id'] ?>" class="btn small">Sửa</a>
                    <a href="delete.php?id=<?= $t['id'] ?>" class="btn small red">Xóa</a>
                </div>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
</div>

</body>
</html>
