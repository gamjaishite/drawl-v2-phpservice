<div id="card-<?= $uuid ?>" class="card card-catalog">
    <div class="card-content">
        <a href="/catalog/<?= $uuid ?>">
            <img width="86.4" height="128" src="<?= "/assets/images/catalogs/posters/" . $poster ?>" alt=<?= $title ?>
                class="poster" alt="<?= $title ?>" />
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
    <?php if (isset($is_admin) && $is_admin): ?>
        <div class="card-button-container">
            <a aria-label="Edit <?= $title ?>" href="/catalog/<?= $uuid ?>/edit" id="edit-<?= $uuid ?>" class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
            </a>
            <button aria-label="Delete <?= $title ?>" id="delete-<?= $uuid ?>" class="dialog-trigger btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if (isset($is_admin) && $is_admin): ?>
    <div id="dialog-<?= $uuid ?>" class="dialog hidden">
        <div class="dialog__content">
            <h2>
                Delete Catalog
            </h2>
            <p>
                Are you sure you want to delete
                <?= $title ?>?
            </p>
            <div class="dialog__button-container">
                <button id="cancel">
                    Cancel
                </button>
                <form action="/catalog/<?= $uuid ?>/delete" method="POST">
                    <button id="delete" class="btn-bold" type="submit">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>