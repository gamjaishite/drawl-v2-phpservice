<div class="card card-comment">
    <div class="card-content">
        <img src=<?= "/assets/images/catalogs/posters/" . $user_image ?> alt=<?= $user_name . ' profile image' ?>
            class="avatar" />
        <div class="card-body">
            <div class="header">
                <h4>
                    <?= $user_name ?>
                </h4>
                <p class="subtitle">
                    <?= $created_at ?>
                </p>
            </div>
            <p>
                <?= $content ?>
            </p>
        </div>
    </div>
    <?php if ($is_user): ?>
        <div class="card-button-container">
            <button class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</div>