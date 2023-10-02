<div id="card-<?= $uuid ?>" class="card card-catalog">
    <div class="card-content">
        <a href="/catalog/<?= $uuid ?>">
            <img width="86.4" height="128" src=<?= "/assets/images/catalogs/posters/" . $poster ?> alt=<?= $title ?>
                class="poster" />
        </a>
        <div class="card-body">
            <a href="/catalog/<?= $uuid ?>">
                <h3>
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
    <div class="card-button-container">
        <a aria-label="Edit <?= $title ?>" href="/catalog/<?= $uuid ?>/edit" id="edit-<?= $uuid ?>" class="btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
        </a>
        <button aria-label="Delete <?= $title ?>" id="delete-<?= $uuid ?>" class="dialog-trigger btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
        </button>
    </div>
</div>

<div id="dialog-<?= $uuid ?>" class="dialog">
    <div class="dialog-content">
        <h2>
            Delete Catalog
        </h2>
        <p>
            Are you sure you want to delete <span class="dialog-title">
                <?= $title ?>
            </span>?
        </p>
        <div class="dialog-button-container">
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