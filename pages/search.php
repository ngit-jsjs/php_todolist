<?php
session_start();
require "../includes/config.php";

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

$name  = $_GET["name"]  ?? "";
$day   = $_GET["day"]   ?? "";
$month = $_GET["month"] ?? "";
$year  = $_GET["year"]  ?? "";
$time  = $_GET["time"]  ?? "";
$status= $_GET["status"] ?? "";

// phân trang
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// kiểm tra có điều kiện lọc nào không
$hasFilter = $name || $day || $month || $year || $time || $status;

if (!$hasFilter) {
    $tasks = [];
    $total = 0;
} else {
    // đếm tổng số
    $sqlCount = "SELECT COUNT(*) FROM tasks WHERE user_id = ?";
    $params = [$user_id];

    // lọc tên
    if ($name) {
        $sqlCount .= " AND title LIKE ?";
        $params[] = "%$name%";
    }

    // lọc ngày
    if ($day) {
        $sqlCount .= " AND DATE(start_time) = ?";
        $params[] = $day;
    }

    // lọc tháng / năm
    if ($month) {
        $sqlCount .= " AND MONTH(start_time) = ?";
        $params[] = $month;
    }
    if ($year) {
        $sqlCount .= " AND YEAR(start_time) = ?";
        $params[] = $year;
    }

    // lọc giờ phút
    if ($time) {
        $sqlCount .= " AND TIME(start_time) = ?";
        $params[] = $time;
    }

    // lọc trạng thái
    if ($status == "done") {
        $sqlCount .= " AND progress = 100";
    } else if ($status == "overdue") {
        $sqlCount .= " AND end_time IS NOT NULL AND end_time < NOW() AND progress < 100";
    } else if ($status == "soon") {
        $sqlCount .= " AND end_time IS NOT NULL AND end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY) AND progress < 100 AND start_time <= NOW()";
    } else if ($status == "in_progress") {
        $sqlCount .= " AND end_time IS NOT NULL AND end_time > DATE_ADD(NOW(), INTERVAL 3 DAY) AND progress < 100 AND start_time <= NOW()";
    } else if ($status == "no_deadline") {
        $sqlCount .= " AND end_time IS NULL";
    } else if ($status == "new") {
        $sqlCount .= " AND DATE(created_at) = CURDATE()";
    }

    $stmt = $conn->prepare($sqlCount);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    // lấy dữ liệu
    $sql = "SELECT * FROM tasks WHERE user_id = ?";
    $params = [$user_id];

    if ($name) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$name%";
    }
    if ($day) {
        $sql .= " AND DATE(start_time) = ?";
        $params[] = $day;
    }
    if ($month) {
        $sql .= " AND MONTH(start_time) = ?";
        $params[] = $month;
    }
    if ($year) {
        $sql .= " AND YEAR(start_time) = ?";
        $params[] = $year;
    }
    if ($time) {
        $sql .= " AND TIME(start_time) = ?";
        $params[] = $time;
    }
    if ($status == "done") {
        $sql .= " AND progress = 100";
    } else if ($status == "overdue") {
        $sql .= " AND end_time IS NOT NULL AND end_time < NOW() AND progress < 100";
    } else if ($status == "soon") {
        $sql .= " AND end_time IS NOT NULL AND end_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY) AND progress < 100 AND start_time <= NOW()";
    } else if ($status == "in_progress") {
        $sql .= " AND end_time IS NOT NULL AND end_time > DATE_ADD(NOW(), INTERVAL 3 DAY) AND progress < 100 AND start_time <= NOW()";
    } else if ($status == "no_deadline") {
        $sql .= " AND end_time IS NULL";
    } else if ($status == "new") {
        $sql .= " AND DATE(created_at) = CURDATE()";
    }

    $sql .= " ORDER BY end_time IS NULL, end_time ASC, start_time ASC LIMIT $limit OFFSET $offset";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalPages = $hasFilter ? ceil($total / $limit) : 0;

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
<title>Kết quả tìm kiếm</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="header-wrapper">
<div class="top">
    
    <h1><a style="display: flex; align-items: center; gap:5px; padding:5px; text-decoration: none;" href="./index.php"><img style="width: auto; height: 70px;" class="icon-user" src="../assets/animation/RetroCat.png" alt=""> <h1>Ticky-Tock</h1></a></h1>
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
    <a href="index.php" class="menu-item">← Quay lại</a>
    <span class="menu-item" style="cursor: pointer; align-items: center; display: flex; gap: 5px;"> <img class="icon-user" src="../assets/animation/Box3.png" alt=""> <?= htmlspecialchars($username) ?></span>
    <a href="add.php" class="menu-item">Thêm công việc</a>
    <a href="../actions/logout.php" class="menu-item">Đăng xuất</a>
    <a href="lab.php" class="menu-item">Lab thực hành</a>
</div>
</div>






<div class="top">
    <h1>🔍 Kết quả tìm kiếm</h1>
</div>

<div class="day-container">
<?php if (!$hasFilter): ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #ff66c4; font-size: 18px; margin: 40px 0;">Vui lòng nhập ít nhất một điều kiện lọc!</p>
    </div>
<?php elseif (empty($tasks)): ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #ff66c4; font-size: 18px; margin: 40px 0;">Không tìm thấy kết quả nào!</p>
    </div>
<?php else: ?>
    <?php foreach ($group as $day => $items): ?>
        <div class="day-box">
            
         <h2 style="display: flex;gap: 8px; align-items: center;">
            <img class="calender-icon" src="../assets/icon/calender.png"> <?= $day ?>
        </h2>
            
            <a href="../actions/delete_day.php?day=<?= urlencode($day) ?>" class="del-day">Xóa ngày</a>

            <div class="task-container">
                <?php foreach ($items as $t): ?>
                    <div class="task <?php 
                        if ($t['progress'] == 100) echo 'done';
                        elseif ($t['end_time'] && $t['end_time'] < date('Y-m-d H:i:s') && $t['progress'] < 100) echo 'overdue';
                    ?>">
                    <?php
                    // tính trạng thái
                    $now = date("Y-m-d H:i:s");
                    $statusLabel = "";

                    $isNew = date("Y-m-d", strtotime($t["created_at"])) == date("Y-m-d");
                    
                    if ($t["progress"] == 100) {
                        $statusLabel = "✅ Hoàn thành";
                    } else if (!$t["end_time"]) {
                        $statusLabel = "♾️ Vô thời hạn";
                    } else if ($t["end_time"] < $now) {
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
                        <h3 style="display: flex;gap: 5px; align-items: center;"><img style="width: 30px;height: 30px;" class="small-icon" src="../assets/icon/task.png" alt="">
                          <?= htmlspecialchars($t['title']) ?>
                        </h3>

                        <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>

                        <p style="display: flex;gap: 5px; align-items: center;">
                            <img class="small-icon" src="../assets/icon/clock.png" alt=""> 
                            Bắt đầu: 
                            <b><?= date('d/m/Y H:i', strtotime($t['start_time'])) ?></b>
                        </p>

                       <p style="display: flex;gap: 5px; align-items: center;">
                        <img style="width: 22px;height: 22px;" class="small-icon" src="../assets/icon/rocket.png" alt="">
                         Hạn chót: 
                        <b><?= $t['end_time'] ? date('d/m/Y H:i', strtotime($t['end_time'])) : '♾️ Vô thời hạn' ?></b>
                    </p>
                        <?php if ($t['end_time'] && $t['progress'] < 100): 
                            $timeDiff = strtotime($t['end_time']) - time();
                            $absTime = abs($timeDiff);
                            $days = floor($absTime / 86400);
                            $hours = floor(($absTime % 86400) / 3600);
                            $timeText = $days > 0 ? $days . ' ngày ' . $hours . ' giờ' : $hours . ' giờ';
                        ?>
                        <p style="display: flex;gap: 5px; align-items: center;">

                        <img class="small-icon" src="../assets/icon/calende 2.png"> 
                         Còn lại: 
                         <b style="color: <?= $timeDiff < 0 ? '#d63031' : ($absTime <= 259200 ? '#fdcb6e' : '#00b894') ?>"><?= $timeDiff < 0 ? 'Trễ ' . $timeText : $timeText ?></b></p>
                        <?php endif ?>
                        <p>🎯 Tiến độ: <b><?= $t['progress'] ?>%</b></p>
                        <p style="display: flex;gap: 5px; align-items: center;">
                    <img style="width: 20px;height: 20px;" class="small-icon" src="../assets/icon/pin.png" alt=""> 
                     Trạng thái: <b><?= $statusLabel ?></b></p>
                        <a href="edit.php?id=<?= $t['id'] ?>" class="btn small">Sửa</a>
                        <a href="../actions/delete.php?id=<?= $t['id'] ?>&from=search&name=<?= urlencode($name) ?>&day=<?= urlencode($day) ?>&month=<?= urlencode($month) ?>&year=<?= urlencode($year) ?>&time=<?= urlencode($time) ?>&status=<?= urlencode($status) ?>&page=<?= $page ?>" class="btn small red">Xóa</a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endforeach ?>
<?php endif ?>
</div>

<?php if ($hasFilter && $totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&name=<?= urlencode($name) ?>&day=<?= urlencode($day) ?>&month=<?= urlencode($month) ?>&year=<?= urlencode($year) ?>&time=<?= urlencode($time) ?>&status=<?= urlencode($status) ?>">«</a>
    <?php endif ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&name=<?= urlencode($name) ?>&day=<?= urlencode($day) ?>&month=<?= urlencode($month) ?>&year=<?= urlencode($year) ?>&time=<?= urlencode($time) ?>&status=<?= urlencode($status) ?>" class="<?= ($i == $page ? 'active' : '') ?>">
            <?= $i ?>
        </a>
    <?php endfor ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>&name=<?= urlencode($name) ?>&day=<?= urlencode($day) ?>&month=<?= urlencode($month) ?>&year=<?= urlencode($year) ?>&time=<?= urlencode($time) ?>&status=<?= urlencode($status) ?>">»</a>
    <?php endif ?>
</div>
<?php endif ?>

<script src="../script.js"></script>

</body>
</html>
