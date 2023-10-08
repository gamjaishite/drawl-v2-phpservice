<div id="toast" class="toast hidden" data-type="<?= $type ?? "error" ?>">
    <div>
        <h3>
            <?= $title ?? "" ?>
        </h3>
        <p>
            <?= $message ?? "" ?>
        </p>
    </div>
    <button id="close" class="btn-ghost">
        <?php require PUBLIC_PATH . "assets/icons/cancel.php" ?>
    </button>
</div>