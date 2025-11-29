<?php require "config.php"; ?>

<?php
// lấy tất cả task
$stmt = $conn->query("SELECT * FROM tasks ORDER BY start_time ASC");
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
<style>
/* GLOBAL */
body {
    margin: 0;
    padding: 0;
    background: #fdf6ff;
    font-family: Poppins, sans-serif;
    transition: 0.3s;
}

/* HEADER */
.top {
    position: sticky;
    top: 0;
    background: #ffffffcc;
    backdrop-filter: blur(18px);
    padding: 18px 35px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 15px rgba(255, 95, 177, 0.15);
    z-index: 999;
}

.top h1 {
    flex: 1;
    font-size: 28px;
    color: #ff66c4;
    font-weight: 600;
}

/* BUTTON */
.btn {
    text-decoration: none;
    background: #ff75d1;
    padding: 10px 16px;
    border-radius: 14px;
    color: #fff;
    font-size: 15px;
    transition: 0.25s;
    box-shadow: 0 4px 12px rgba(255, 105, 189, 0.25);
}

.btn:hover {
    background: #ff48c2;
    transform: translateY(-2px);
}

.btn.small {
    padding: 6px 10px;
    font-size: 13px;
    border-radius: 10px;
}

.btn.red {
    background: #ff5f5f;
}

.btn.red:hover {
    background: #ff4040;
}

/* SEARCH */
/* #searchInput {
    padding: 12px 16px;
    border-radius: 14px;
    border: none;
    outline: none;
    width: 240px;
    background: #ffe9f8;
    box-shadow: 0 2px 10px rgba(255, 115, 180, 0.15);
    font-size: 15px;
} */
.filter-bar {
    display: flex;
    gap: 12px;
    background: #ffffffcc;
    padding: 12px 18px;
    border-radius: 14px;
    backdrop-filter: blur(10px);
    box-shadow: 0 3px 12px rgba(255, 100, 180, 0.15);
}

.filter-bar input,
.filter-bar select {
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #f4cfee;
    outline: none;
    font-size: 14px;
    background: #fff6fd;
    box-shadow: 0 2px 8px rgba(255, 110, 190, 0.15);
}

.filter-bar input:focus,
.filter-bar select:focus {
    border-color: #ff71d1;
}


/* GRID DAY CARDS */
.day-container {
    padding: 35px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    grid-gap: 28px;
    height:fit-content;
    align-self: start; /* Bắt mỗi ô tự cao theo nội dung của nó */
    align-items: start;
}

/* DAY BOX */
.day-box {
    background: #fff;
    padding: 25px;
    border-radius: 22px;
    box-shadow: 0px 8px 25px rgba(255, 95, 177, 0.2);
    animation: fadeUp 0.4s ease;
    position: relative;
    transition: 0.25s;
    align-items: start;
}

.day-box:hover {
    transform: translateY(-5px);
}

.day-box h2 {
    margin: 0;
    margin-bottom: 10px;
    font-weight: 600;
    font-size: 20px;
    color: #ff54bd;
}

/* DELETE DAY */
.del-day {
    position: absolute;
    right: 20px;
    top: 22px;
    color: #ff3e67;
    font-size: 13px;
    text-decoration: none;
    transition: 0.15s;
}

.del-day:hover {
    transform: scale(1.1);
}

/* TASK CARD */
.task {
    background: #fff0fa;
    padding: 17px;
    border-radius: 18px;
    margin-bottom: 14px;
    box-shadow: 0px 4px 12px rgba(255, 115, 180, 0.2);
    transition: 0.2s;
}

.task:hover {
    transform: translateY(-4px);
}

.task.done {
    background: #d7ffe4;
}

/* TASK TITLE */
.task h3 {
    margin: 0 0 8px 0;
    font-size: 17px;
    color: #ff48c8;
}

/* ANIMATIONS */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function search() {
    let k = document.getElementById("searchInput").value;
    if (!k.trim()) return;
    window.location.href = "search.php?key=" + encodeURIComponent(k);
}
</script>

</head>

<body>

<script src="script.js"></script>

<div class="top">
    <h1>🌸 Todo List Cute Premium</h1>

    <a class="btn" href="add.php">+ Thêm công việc</a>

   <div class="filter-bar">
    <input type="text" id="filter_name" placeholder="🔍 Tên công việc...">

    <input type="date" id="filter_day">
    <input type="number" id="filter_month" min="1" max="12" placeholder="Tháng">
    <input type="number" id="filter_year" min="2000" max="2100" placeholder="Năm">

    <input type="time" id="filter_time">

    <select id="filter_status">
        <option value="">-- Trạng thái --</option>
        <option value="overdue">📛 Quá hạn</option>
        <option value="soon">⏳ Sắp đến hạn</option>
        <option value="new">🆕 Mới thêm</option>
        <option value="done">✅ Hoàn thành</option>
    </select>

    <button class="btn" onclick="applyFilter()">Lọc</button>
</div>

</div>

<div class="day-container">

<?php foreach ($group as $day => $items): ?>
    <div class="day-box">
        
        <h2>📅 <?= $day ?></h2>

        <a href="delete_day.php?day=<?= urlencode($day) ?>" class="del-day">Xóa ngày</a>

        <?php foreach ($items as $t): ?>
            <div class="task <?= ($t['progress'] == 100 ? 'done' : '') ?>">
            <?php
            // tính trạng thái
            $now = date("Y-m-d H:i:s");
            $statusLabel = "";

            if ($t["progress"] == 100) {
                $statusLabel = "✅ Hoàn thành";
            } else if ($t["end_time"] < $now) {
                $statusLabel = "📛 Quá hạn";
            } else {
                // còn hạn → kiểm tra gần hết hạn chưa
                $timeDiff = strtotime($t["end_time"]) - time();

                if ($timeDiff <= 3600 * 3) { // <= 3 giờ
                    $statusLabel = "⏳ Sắp đến hạn";
                } else {
                    $statusLabel = "🆕 Đang tiến hành";
                }
            }
        ?>

                <h3>📝 <?= htmlspecialchars($t['title']) ?></h3>

                <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
                <p>⏰ Bắt đầu: <b><?= $t['start_time'] ?></b></p>
                <p>🚀 Hạn chót: <b><?= $t['end_time'] ?></b></p>
                <p>🎯 Tiến độ: <b><?= $t['progress'] ?>%</b></p>
                <p>📌 Trạng thái: <b><?= $statusLabel ?></b></p>

                
                <a href="edit.php?id=<?= $t['id'] ?>" class="btn small">Sửa</a>
                <a href="delete.php?id=<?= $t['id'] ?>" class="btn small red">Xóa</a>

                <form action="toggle.php" method="POST" style="margin-top: 8px;">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="range" name="progress" value="<?= $t['progress'] ?>" min="0" max="100" onchange="this.form.submit()">
                </form>

            </div>
        <?php endforeach ?>

    </div>
<?php endforeach ?>

</div>

</body>
</html>
