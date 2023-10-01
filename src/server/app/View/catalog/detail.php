<?php
$data = $model['data']
    ?>

<main>
    <article class="catalog-detail-header">
        <div class="catalog-detail-header-poster"></div>
        <img class="poster" src="<?= '/assets/images/catalogs/posters/' . $data['poster'] ?>"
            alt="<?= 'Poster of ' . $data['title'] ?>">
    </article>
    <article class="catalog-detail-content">
        <h2>
            <?= $data['title'] ?>
        </h2>
        <div class="tag">
            <?= $data['category'] ?>
        </div>
        <p>
            <?= $data['description'] ?>
        </p>
        <?php if (isset($data['trailer']) && $data['trailer'] !== null): ?>
            <h3>Trailer</h3>
            <video class="catalog-trailer" controls>
                <source src="<?= '/assets/videos/catalogs/trailer/' . $data['trailer'] ?>" type="video/mp4">
            </video>
        <?php endif; ?>
    </article>
    <div class="button-container">
        <a href="/catalog/<?= $data['uuid'] ?>/edit" id="edit" aria-label="Edit <?= $data['title'] ?>" class="btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
        </a>
        <button type="submit" aria-label="Delete <?= $data['title'] ?>" class="dialog-trigger btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
        </button>
    </div>
</main>


<div class="dialog">
    <div class="dialog-content">
        <h2>
            Delete Catalog
        </h2>
        <p>
            Are you sure you want to delete <span class="dialog-title">
                <?= $data['title'] ?>
            </span>?
        </p>
        <div class="dialog-button-container">
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