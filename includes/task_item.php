<?php
// Component hiển thị một task item
// Yêu cầu: $t (task data), $now (timestamp), $today (Y-m-d)

$endTime = $t['end_time'] ? strtotime($t['end_time']) : null;
$isOverdue = $endTime && $endTime <= $now && $t['progress'] < 100;
$isNew = date("Y-m-d", strtotime($t["created_at"])) == $today;

$labels = [];

// Hoàn thành
if ($t["progress"] == 100) {
    $labels[] = "✅ Hoàn thành";
}
// Quá hạn
elseif ($endTime && $endTime < $now) {
    $labels[] = "📛 Quá hạn";
}
// Đang tiến hành (mặc định)
else {
    $labels[] = "🔄 Đang tiến hành";

    // Sắp đến hạn (<= 3 ngày)
    if ($endTime && ($endTime - $now) <= 259200) {
        $labels[] = "⏳ Sắp đến hạn";
    }

    // Mới thêm
    if ($isNew) {
        $labels[] = "🆕 Mới thêm";
    }
}

$statusLabel = implode(" • ", $labels);
?>

<div class="task <?php 
    if ($t['progress'] == 100) echo 'done';
    elseif ($isOverdue) echo 'overdue';
?>">
    <h2 style="display: flex;gap: 5px; align-items: center;">
        <img style="width: 30px;height: 30px;" class="small-icon" src="../assets/icon/task.png" alt=""> 
        <b><?= htmlspecialchars($t['title']) ?></b>
    </h2>

    <p class="task-content"><?= nl2br(htmlspecialchars($t['content'])) ?></p>

    <p style="display: flex;gap: 5px; align-items: center;">
        <img class="small-icon" src="../assets/icon//calende 2.png" alt="">
        Ngày tạo: 
        <b><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></b>
    </p>

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
    

    <?php if ($endTime && $t['progress'] < 100): 
        $timeDiff = $endTime - $now;
        $absTime = abs($timeDiff);
        $days = floor($absTime / 86400);
        $hours = floor(($absTime % 86400) / 3600);
        $timeText = $days > 0 ? $days . ' ngày ' . $hours . ' giờ' : $hours . ' giờ';
    ?>
        <p style="display: flex;gap: 5px; align-items: center;">
            <img class="small-icon" src="../assets/icon/calende 2.png"> 
            Còn lại: 
            <b style="color: <?= $timeDiff < 0 ? '#d63031' : ($absTime <= 259200 ? '#fdcb6e' : '#00b894') ?>">
                <?= $timeDiff < 0 ? 'Trễ ' . $timeText : $timeText ?>
            </b>
        </p>
    <?php endif ?>

    <p>🎯 Tiến độ: 
        <b id="progress-text-<?= $t['id'] ?>"><?= $t['progress'] ?>%</b>
    </p>

    <div class="progress-box" style="margin: 5px 0; display: flex; gap: 8px; align-items: center;">
    <input type="range"
           min="0" max="100"
           value="<?= $t['progress'] ?>"
           oninput="document.getElementById('progress-text-<?= $t['id'] ?>').textContent = this.value + '%'"
           style="flex: 1;">

    <button type="button"
            class="btn small"
            onclick="saveProgress(<?= $t['id'] ?>, this)">
        Lưu
    </button>
</div>


    <p style="display: flex;gap: 5px; align-items: center;">
        <img style="width: 20px;height: 20px;" class="small-icon" src="../assets/icon/pin.png" alt=""> 
        Trạng thái: <b><?= $statusLabel ?></b>
    </p>

    <a href="edit.php?id=<?= $t['id'] ?>" class="btn small">Sửa</a>
    <a href="../actions/delete.php?id=<?= $t['id'] ?>"  onclick="return confirm('Bạn chắc muốn xóa công việc này?')" class="btn small red">Xóa</a>
</div>
