<?php
// Component pagination - yêu cầu: $page, $totalPages, $queryParams (optional)
if ($totalPages >= 1):
    $params = $queryParams ?? '';
?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page-1 ?><?= $params ?>">«</a>
    <?php endif ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?><?= $params ?>" class="<?= ($i == $page ? 'active' : '') ?>">
            <?= $i ?>
        </a>
    <?php endfor ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?><?= $params ?>">»</a>
    <?php endif ?>
</div>
<?php endif; ?>
