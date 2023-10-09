<div class="search-item" data-page="<?= $page ?>">
    <img src="<?= '/assets/images/catalogs/posters/' . $poster ?>" class="search-item__poster" />
    <div class="search-item__content">
        <h3 class="search-item__title"><?= $title ?></h3>
        <p class="search-item__description"><?= $description ?></p>
    </div>
    <button type="button" class="search-item__action" data-id="<?= $uuid ?>">
        <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
    </button>
</div>