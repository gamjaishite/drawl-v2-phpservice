<!-- Attributes
- title
- poster
- category
- description
-->

<div class="card card-catalog">
    <div class="card-content">
        <img src=<?= "/assets/images/catalogs/posters/" . $poster ?> alt=<?= $title ?> class="poster" />
        <div class="card-body">
            <h3>
                <?= $title ?>
            </h3>
            <div class="tag">
                <?= $category ?>
            </div>
            <p>
                <?= $description ?>
            </p>
        </div>
    </div>
    <div class="card-button-container">
        <button class="btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
        </button>
        <button class="btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
        </button>
    </div>
</div>