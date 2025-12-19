<?php 
include '../includes/auth_check.php';
include '../includes/sort.php'; 
// 1) NHẬN DỮ LIỆU LỌC TỪ URL (GET)
// =======================
// Dùng toán tử ?? "" để:
// - nếu param không tồn tại -> gán chuỗi rỗng
// - tránh lỗi "Undefined index"
$name   = trim($_GET['name']   ?? '');
$day   = $_GET["day"]   ?? "";
$month = $_GET["month"] ?? "";
$year  = $_GET["year"]  ?? "";
$status= $_GET["status"] ?? "";
$time   = trim($_GET['time']   ?? ""); // <-- thêm dòng này


// phân trang
$limit = 12;
// Lấy page từ URL, nếu không có thì mặc định trang 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Đảm bảo page không âm/0
if ($page < 1) $page = 1;
// $offset: bỏ qua bao nhiêu dòng để lấy trang hiện tại
// VD: page=1 -> offset=0
//     page=2 -> offset=12
$offset = ($page - 1) * $limit;

// lấy điều kiện lọc là loại nào
$hasFilter = $name || $day || $month || $year || $time || $status;

$invalidFilter = false;
if ($day && $month && $year && !checkdate((int)$month, (int)$day, (int)$year)) {
    $invalidFilter = true;
}
// 2) KIỂM TRA ĐIỀU KIỆN LỌC HỢP LỆ KHÔNG

// chọn tháng nhưng không có năm
if ($month && !$year) {
    $invalidFilter = true;
}
// chọn ngày nhưng không có năm
if ($day && !$year) {
    $invalidFilter = true;
}
// 4) NẾU KHÔNG CÓ FILTER HOẶC FILTER KHÔNG HỢP LỆ -> TRẢ VỀ RỖNG
// Nếu filter không hợp lệ (ví dụ chọn ngày nhưng không có năm) hoặc không có filter,
// không thực hiện truy vấn để tránh lỗi SQL do giá trị ngày không đầy đủ.
if (!$hasFilter || $invalidFilter) {
    $tasks = [];
    $total = 0;
} else {
    // chuẩn hóa ngày (nếu user chọn day+month+year) sang định dạng YYYY-MM-DD
    $dayIso = null;
    if ($day && $month && $year) {
        $dayIso = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);
    }
    //sprintf() = format chuỗi theo khuôn mẫu
    // đếm tổng số
    $sqlCount = "SELECT COUNT(*) FROM tasks WHERE user_id = ?";
    $params = [$user_id];  
     // 5.1) LỌC THEO TÊN TASK
    // -----------------------
    // title LIKE %name%: tìm task có chứa chuỗi name
    // lọc tên
    if ($name) {
        $sqlCount .= " AND title LIKE ?";
        $params[] = "%$name%";
    }

    // lọc ngày - tìm công việc đang diễn ra trong ngày đó
    if ($day) {
        $sqlCount .= " AND (DATE(start_time) <= ? AND (end_time IS NULL OR DATE(end_time) >= ?))";
        $params[] = $dayIso ?? $day;
        $params[] = $dayIso ?? $day;
    }

    // lọc tháng - tìm công việc bắt đầu từ tháng đó trở đi
    if ($year && $month && !$day) {
        $startMonth = "$year-$month-01 00:00:00";
        $endMonth   = date("Y-m-t 23:59:59", strtotime($startMonth));

        $sqlCount .= " AND start_time <= ? AND (end_time IS NULL OR end_time >= ?)";
        $params[] = $endMonth;
        $params[] = $startMonth;
    }

    
    // lọc năm - tìm công việc diễn ra trong năm đó
    if ($year && !$month && !$day) {
    $startYear = "$year-01-01 00:00:00";
    $endYear   = "$year-12-31 23:59:59";

    $sqlCount .= " AND start_time <= ? AND (end_time IS NULL OR end_time >= ?)";
    $params[] = $endYear;
    $params[] = $startYear;
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
        $sql .= " AND (DATE(start_time) <= ? AND (end_time IS NULL OR DATE(end_time) >= ?))";
        $params[] = $dayIso ?? $day;
        $params[] = $dayIso ?? $day;
    }
    if ($month && !$day) {
        $sql .= " AND MONTH(start_time) >= ?";
        $params[] = $month;
    }
    if ($year && !$day && !$month) {
        $sql .= " AND YEAR(start_time) >= ?";
        $params[] = $year;
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


    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$totalPages = $hasFilter ? ceil($total / $limit) : 0;
?>

<?php $pageTitle = 'Kết quả tìm kiếm'; 
include '../includes/header.php';
?>

<div class="top">
    <h1>Kết quả tìm kiếm</h1>
</div>

<?php 
$extraParams = "+'&name=" . urlencode($name) . "&day=" . urlencode($day) . "&month=" . urlencode($month) . "&year=" . urlencode($year) . "&status=" . urlencode($status) . "'";
include '../includes/sort_selector.php'; 
?>


<?php if (!$hasFilter): ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #f742b1ff; font-size: 18px; isolation: isolate; font-weight: bolder;">Vui lòng nhập ít nhất một điều kiện lọc!</p>
    </div>
    <?php elseif ($invalidFilter): ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #f742b1ff; font-size: 18px; isolation: isolate; font-weight: bolder;">
            Điều kiện lọc không hợp lệ. Vui lòng kiểm tra lại ngày, tháng hoặc thời gian!
        </p>
    </div>
<?php elseif (empty($tasks)): ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #f742b1ff; font-size: 18px; isolation: isolate; font-weight: bolder;">Không tìm thấy kết quả nào!</p>
    </div>
<?php else: ?>
    <div class="day-box">
        <div class="task-container">
            <?php 
            $now = time();
            $today = date("Y-m-d");
            foreach ($tasks as $t): 
                include '../includes/task_item.php';
            endforeach; 
            ?>
        </div>
    </div>
<?php endif ?>


<?php 
if ($hasFilter) {
    $queryParams = '&sort=' . ($sort ?? 'created') . '&name=' . urlencode($name) . '&day=' . urlencode($day) . '&month=' . urlencode($month) . '&year=' . urlencode($year) . '&status=' . urlencode($status);
    include '../includes/pagination.php';
}
?>

<?php include "../includes/footer.php"; ?>

