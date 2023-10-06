<?php

if (!function_exists("formatDate")) {
    function formatDate($createdAt)
    {
        require __DIR__ . '/../../../config/dateFormat.php';
    }
}

function catalogCard($catalog)
{
    $is_admin = false;
    $title = $catalog['title'];
    $poster = $catalog['poster'];
    $category = $catalog['category'];
    $description = $catalog['description'];
    $uuid = $catalog['catalog_uuid'];
    require __DIR__ . '/../components/card/catalogCard.php';
}

function pagination($currentPage, $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>

<main>
    <article class="header">
        <div class="detail">
            <h2>
                <?= $model['data']['title'] ?>
            </h2>
            <div class="container-subtitle">
                <div class="tag">
                    <?= $model['data']['category'] ?>
                </div>
                <p class="subtitle">
                    <?= $model['data']['creator'] ?> |
                    <?= formatDate($model['data']['updated_at']) ?>
                </p>
            </div>
            <p>
                <?= $model['data']['description'] ?>
            </p>
        </div>
        <div class="container-button">
            <div class="container-btn-love">
                <button class="btn-ghost">
                    <?php
                    $type = (isset($model['data']['like_status']) && $model['data']['like_status']) ? "filled" : "unfilled";
                    require PUBLIC_PATH . 'assets/icons/love.php' ?>
                </button>
                <span>
                    <?= $model['data']['like_count'] ?>
                </span>
            </div>
            <button class="btn-ghost">
                <?php
                $type = (isset($model['data']['save_status']) && $model['data']['save_status']) ? "filled" : "unfilled";
                require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
            </button>
        </div>
    </article>
    <article id="catalogs" class="content">
        <?php foreach ($model['data']['catalogs']['items'] ?? [] as $catalog): ?>
            <?php catalogCard($catalog); ?>
        <?php endforeach; ?>
        <?php pagination($model['data']['catalogs']['page'], $model['data']['catalogs']['totalPage']); ?>
    </article>
    <?php if (isset($model['editable']) && $model['editable']): ?>
        <div class="watchlist-detail__button-container">
            <a href="<?= "/watchlist/" . $model['data']['watchlist_uuid'] . "/edit" ?>" id="edit"
                aria-label="Edit <?= $model['data']['title'] ?>" class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
            </a>
            <button type="submit" aria-label="Delete <?= $model['data']['title'] ?>" class="dialog-trigger btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</main>