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
    <!-- <div class="card-button-container">
        <a href="/catalog/<?= $uuid ?>/edit" id="edit" class="btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
        </a>
        <button id="delete" class="dialog-trigger btn-icon">
            <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
        </button>
    </div> -->
</div>