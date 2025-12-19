<?php include '../includes/auth_check.php'; ?>

<?php
$now = time();
$today = date("Y-m-d");
// số task mỗi trang
$limit = 12;

// lấy page hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// lấy tùy chọn sắp xếp (định nghĩa $sort và $orderBy)
include '../includes/sort.php';

// tổng số task của user để tính tổng số trang
$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
$stmt->execute([$user_id]);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $limit);


// lấy task của user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY $orderBy LIMIT :limit OFFSET :offset");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php $pageTitle = 'Ticky-Tock'; 
include '../includes/header.php'; 
include '../includes/sort_selector.php';
?>



<?php if (!empty($tasks)): ?>

    <div class="day-box">
        <div class="task-container">
            <?php foreach ($tasks as $t): include '../includes/task_item.php'; endforeach; ?>
        </div>
    </div>

<?php else: ?>
    <div style="width: 100%; text-align: center;">
        <p style="color: #f742b1ff; font-size: 18px; isolation: isolate; font-weight: bolder;">Bạn chưa có task nào!</p>
    </div>
<?php endif; ?>


<?php 
$queryParams = '&sort=' . $sort;
include '../includes/pagination.php'; 
?>

<?php include "../includes/footer.php"; ?>

<?php if (!empty($_SESSION['toast'])): ?>
  <div id="toast"
       class="toast <?= $_SESSION['toast']['type'] ?>"
       data-message="<?= htmlspecialchars($_SESSION['toast']['message']) ?>">
    <span class="toast-text"></span>
    <button class="toast-close" aria-label="Đóng">×</button>
  </div>
  <?php unset($_SESSION['toast']); ?>
<?php endif; ?>



