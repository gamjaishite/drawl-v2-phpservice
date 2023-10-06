<?php
$catalog = $model['data']['item'];
$userRole = $model['data']['userRole'];
?>

<main>
    <div class="catalog-detail-header">
        <div class="catalog-detail-header-poster"></div>
        <img class="poster" src="<?= '/assets/images/catalogs/posters/' . $catalog['poster'] ?>"
            alt="<?= 'Poster of ' . $catalog['title'] ?>">
    </div>
    <article class="catalog-detail-content">
        <h2>
            <?= $catalog['title'] ?>
        </h2>
        <div class="tag">
            <?= $catalog['category'] ?>
        </div>
        <p>
            <?= (!isset($catalog['description']) || empty($catalog['description'])) ? "No description" : $catalog['description'] ?>
        </p>
        <?php if (isset($catalog['trailer']) && $catalog['trailer'] !== null): ?>
            <h3>Trailer</h3>
            <video class="catalog-trailer" controls>
                <source src="<?= '/assets/videos/catalogs/trailers/' . $catalog['trailer'] ?>" type="video/mp4">
            </video>
        <?php endif; ?>
    </article>
    <?php if ($userRole && $userRole === "ADMIN"): ?>
        <div class="button-container">
            <a href="/catalog/<?= $catalog['uuid'] ?>/edit" id="edit" aria-label="Edit <?= $catalog['title'] ?>"
                class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
            </a>
            <button type="submit" aria-label="Delete <?= $catalog['title'] ?>" class="dialog-trigger btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</main>

<?php if ($userRole && $userRole === "ADMIN"): ?>
    <div class="dialog hidden">
        <div class="dialog__content">
            <h2>
                Delete Catalog
            </h2>
            <p>
                Are you sure you want to delete <span class="dialog-title">
                    <?= $catalog['title'] ?>
                </span>?
            </p>
            <div class="dialog__button-container">
                <button id="cancel">
                    Cancel
                </button>
                <form action="/catalog/<?= $model['data']['uuid'] ?>/delete" method="POST">
                    <button id="delete" class="btn-bold" type="submit">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>