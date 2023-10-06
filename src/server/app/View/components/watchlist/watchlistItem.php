<!-- <div class="watchlist-item__wrapper"> -->
<img src="<?= '/assets/images/catalogs/posters/' . ($poster ?? 'no-poster.webp') ?>" class="watchlist-item__poster">
<div class="watchlist-item__content">
    <h3 class="watchlist-item__title">
        <?= $title ?>
    </h3>
    <textarea name="<?= 'item[' . $id . '__' . $uuid . '__' . $category . ']' ?>"
              class="input-default watchlist-item__description" placeholder="Enter description"
              maxlength="255"><?= $description ?? "" ?></textarea>
</div>
<!-- </div> -->
<div class="watchlist-item__actions">
    <button type="button" class="btn-ghost watchlist-item__delete" data-id="<?= $uuid ?>">
        <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
    </button>
    <span class="span__icon drag-handler">
        <?php require PUBLIC_PATH . 'assets/icons/grip-vertical.php' ?>
    </span>
</div>