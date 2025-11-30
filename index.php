<?php 
session_start();
require "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy username
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$username = $stmt->fetchColumn();
?>

<?php
// số task mỗi trang
$limit = 10;

// lấy page hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// tổng số task của user để tính tổng số trang
$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $limit);

// lấy task của user theo giới hạn trang
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY end_time IS NULL, end_time ASC, start_time ASC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
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
<title>Todo Cute Premium</title>

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="style.css">

<script>
function search() {
    let k = document.getElementById("searchInput").value;
    if (!k.trim()) return;
    window.location.href = "search.php?key=" + encodeURIComponent(k);
}
</script>

</head>

<body>

<div class="header-wrapper">
<div class="top">
    <h1>🌸 Todo List</h1>
    <button class="main-dark-toggle" id="mainDarkToggle">🌙</button>

   <div class="filter-bar">
    <input type="text" id="filter_name" placeholder="🔍 Tên công việc...">

    <input type="date" id="filter_day">
    <input type="number" id="filter_month" min="1" max="12" placeholder="Tháng">
    <input type="number" id="filter_year" min="2000" max="2100" placeholder="Năm">

    <input type="time" id="filter_time">

    <div class="custom-select">
        <div class="select-selected" id="filter_status_display">-- Trạng thái --</div>
        <input type="hidden" id="filter_status" value="">
        <ul class="select-items">
            <li data-value="">-- Trạng thái --</li>
            <li data-value="overdue">📛 Quá hạn</li>
            <li data-value="soon">⏳ Sắp đến hạn</li>
            <li data-value="in_progress">🔄 Đang tiến hành</li>
            <li data-value="no_deadline">♾️ Vô thời hạn</li>
            <li data-value="new">🆕 Mới thêm</li>
            <li data-value="done">✅ Hoàn thành</li>
        </ul>
    </div>

    <button styl class="btn" onclick="applyFilter()">Lọc</button>
</div>

</div>

<div class="menu-bar">
    <span class="menu-item" style="cursor: default;">👋 Xin chào, <?= htmlspecialchars($username) ?></span>
    <a href="add.php" class="menu-item">+ Thêm công việc</a>
    <a href="logout.php" class="menu-item">Đăng xuất</a>
    <a href="lab.php" class="menu-item">Lab thực hành</a>
</div>
</div>

<div class="day-container">

<?php foreach ($group as $day => $items): ?>
    <div class="day-box">
        
        <h2>📅 <?= $day ?></h2>

        <a href="delete_day.php?day=<?= urlencode($day) ?>" class="del-day">Xóa ngày</a>

        <div class="task-container">
        <?php foreach ($items as $t): ?>
            <div class="task <?php 
                if ($t['progress'] == 100) echo 'done';
                elseif ($t['end_time'] && strtotime($t['end_time']) <= time() && $t['progress'] < 100) echo 'overdue';
            ?>">
            <?php
            // tính trạng thái
            $now = time();
            $statusLabel = "";

            $isNew = date("Y-m-d", strtotime($t["created_at"])) == date("Y-m-d");
            
            if ($t["progress"] == 100) {
                $statusLabel = "✅ Hoàn thành";
            } else if (!$t["end_time"]) {
                $statusLabel = "♾️ Vô thời hạn";
            } else if (strtotime($t["end_time"]) < $now) {
                $statusLabel = "📛 Quá hạn";
            } else {
                $timeDiff = strtotime($t["end_time"]) - time();
                if ($timeDiff <= 3600 * 24 * 3) {
                    $statusLabel = "⏳ Sắp đến hạn";
                } else {
                    $statusLabel = "🔄 Đang tiến hành";
                }
            }
            
            if ($isNew) {
                $statusLabel = "🆕 Mới thêm - " . $statusLabel;
            }
        ?>

                <h3>📝 <?= htmlspecialchars($t['title']) ?></h3>

                <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
                <p>⏰ Bắt đầu: <b><?= date('d/m/Y H:i', strtotime($t['start_time'])) ?></b></p>
                <p>🚀 Hạn chót: <b><?= $t['end_time'] ? date('d/m/Y H:i', strtotime($t['end_time'])) : '♾️ Vô thời hạn' ?></b></p>
                <?php if ($t['end_time'] && $t['progress'] < 100): 
                    $daysLeft = ceil((strtotime($t['end_time']) - time()) / 86400);
                ?>
                <p>📅 Còn lại: <b style="color: <?= $daysLeft < 0 ? '#d63031' : ($daysLeft <= 3 ? '#fdcb6e' : '#00b894') ?>"><?= $daysLeft < 0 ? 'Trễ ' . abs($daysLeft) : $daysLeft ?> ngày</b></p>
                <?php endif ?>
                <p>🎯 Tiến độ: <b id="progress-text-<?= $t['id'] ?>"><?= $t['progress'] ?>%</b></p>
                <form action="toggle.php" method="POST" style="margin: 5px 0; display: flex; gap: 8px; align-items: center;">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="range" name="progress" value="<?= $t['progress'] ?>" min="0" max="100" 
                           oninput="document.getElementById('progress-text-<?= $t['id'] ?>').textContent = this.value + '%'" style="flex: 1;">
                    <button type="submit" class="btn small" style="margin: 0;">Lưu</button>
                </form>
                <p>📌 Trạng thái: <b><?= $statusLabel ?></b></p>

                
                <a href="edit.php?id=<?= $t['id'] ?>" class="btn small">Sửa</a>
                <a href="delete.php?id=<?= $t['id'] ?>" class="btn small red">Xóa</a>

            </div>
        <?php endforeach ?>
        </div>
    </div>
<?php endforeach ?>

</div>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>">«</a>
    <?php endif ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page ? 'active' : '') ?>">
            <?= $i ?>
        </a>
    <?php endfor ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>">»</a>
    <?php endif ?>
</div>

<script>
const mainDarkToggle = document.getElementById("mainDarkToggle");
const body = document.body;

if (localStorage.getItem("darkMode") === "true") {
    body.classList.add("dark-mode");
    mainDarkToggle.textContent = "☀️";
}

mainDarkToggle.addEventListener("click", () => {
    body.classList.toggle("dark-mode");
    const isDark = body.classList.contains("dark-mode");
    mainDarkToggle.textContent = isDark ? "☀️" : "🌙";
    localStorage.setItem("darkMode", isDark);
});
</script>

<script src="script.js"></script>

</body>
</html>
