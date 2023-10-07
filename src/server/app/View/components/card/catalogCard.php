<?php
if (!file_exists('assets/images/catalogs/posters/' . $poster)) {
    $poster = 'no-poster.webp';
}
?>

<div id="card-<?= $uuid ?>" class="card card-catalog">
    <div class="card-content">
        <a href="/catalog/<?= $uuid ?>">
            <img width="86.4" height="128" onerror="this.src = '/assets/images/catalogs/posters/no-poster.webp'"
                src="<?= "/assets/images/catalogs/posters/" . $poster ?>" alt=<?= $title ?> class="poster"
                alt="<?= $title ?>" />
        </a>
        <div class="card-body">
            <a href="/catalog/<?= $uuid ?>">
                <h3 class="card-title">
                    <?= $title ?>
                </h3>
            </a>
            <div class="tag">
                <?= $category ?>
            </div>
            <p>
                <?= $description ?>
            </p>
        </div>
    </div>
    <?php if (isset($editable) && $editable): ?>
        <div class="card-button-container">
            <a aria-label="Edit <?= $title ?>" href="/catalog/<?= $uuid ?>/edit" id="edit-<?= $uuid ?>" class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
            </a>
            <button aria-label="Delete <?= $title ?>" id="delete-trigger-<?= $uuid ?>" data-uuid="<?= $uuid ?>"
                data-title="<?= $title ?>" class="catalog-delete-trigger dialog-trigger btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</div>