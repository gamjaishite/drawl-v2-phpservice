<div class="pagination">
    <?php if ($currentPage > 1): ?>
        <a href="?page=<?= $currentPage - 1 ?>" class="pagination-item prev">
            <?php require PUBLIC_PATH . 'assets/icons/chevron-down.php' ?>
        </a>
    <?php endif; ?>
    <?php if (max(0, $currentPage - 2) > 0): ?>
        <span class="pagination-elips">...</span>
    <?php endif; ?>
    <?php for ($i = max(0, $currentPage - 2); $i < min($totalPage, $currentPage + 2); $i++): ?>
        <a href="?page=<?= $i + 1 ?>" class="pagination-item"
            data-type="<?= $i + 1 === $currentPage ? 'active' : 'inactive' ?>">
            <?= $i + 1 ?>
        </a>
    <?php endfor; ?>
    <?php if (min($totalPage, $currentPage + 2) < $totalPage): ?>
        <span class="pagination-elips">...</span>
    <?php endif; ?>
    <?php if ($currentPage < $totalPage): ?>
        <a href="?page=<?= $currentPage + 1 ?>" class="pagination-item next">
            <?php require PUBLIC_PATH . 'assets/icons/chevron-down.php' ?>
        </a>
    <?php endif; ?>
</div>