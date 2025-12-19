<?php
// Component sort selector - yêu cầu: $sort, $extraParams (optional)
$sortValue = $sort ?? 'created';
$params = $extraParams ?? '';
?>
<div style="text-align: center; margin: 20px ; display: flex; align-items: center; gap: 10px;">
    <span class="sort-label">Sắp xếp:</span>

    <form method="get">
        <?php
        // giữ các query hiện có (ngoại trừ sort và page)
        foreach ($_GET as $k => $v) {
            if ($k === 'sort' || $k === 'page') continue;
            echo '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($v).'" />';
        }
        ?>
        <select name="sort" style="font-family: Poppins, sans-serif;" onchange="this.form.submit()" class="select-selected">
            <option class="select-items show" value="created" <?= $sortValue === 'created' ? 'selected' : '' ?>>Mới nhất</option>
            <option class="select-items show" value="deadline" <?= $sortValue === 'deadline' ? 'selected' : '' ?>>Thời hạn</option>
        </select>
    </form>
</div>
