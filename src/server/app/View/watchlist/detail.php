<?php

if (!function_exists("formatDate")) {
    function formatDate($createdAt)
    {
        require __DIR__ . '/../../../config/dateFormat.php';
    }
}

function catalogCard($catalog)
{
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
                <?= $model['data']['item']['title'] ?>
            </h2>
            <div class="container-subtitle">
                <div class="tag">
                    <?= $model['data']['item']['category'] ?>
                </div>
                <p class="subtitle">
                    <?= $model['data']['item']['creator'] ?> |
                    <?= formatDate($model['data']['item']['updated_at']) ?>
                </p>
            </div>
            <p>
                <?= $model['data']['item']['description'] ?>
            </p>
        </div>
        <div class="container-button">
            <div class="container-btn-love">
                <button class="btn-ghost">
                    <?php
                    $type = (isset($model['data']['item']['like_status']) && $model['data']['item']['like_status']) ? "filled" : "unfilled";
                    require PUBLIC_PATH . 'assets/icons/love.php' ?>
                </button>
                <span>
                    <?= $model['data']['item']['like_count'] ?>
                </span>
            </div>
            <?php if (!isset($model['data']['userUUID']) || $model['data']['userUUID'] != $model['data']['item']['creator_uuid']): ?>
                <button class="btn-ghost">
                    <?php
                    $type = (isset($model['data']['item']['save_status']) && $model['data']['item']['save_status']) ? "filled" : "unfilled";
                    require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
                </button>
            <?php endif; ?>
        </div>
    </article>
    <article id="catalogs" class="content">
        <?php foreach ($model['data']['item']['catalogs']['items'] ?? [] as $catalog): ?>
            <?php catalogCard($catalog); ?>
        <?php endforeach; ?>
        <?php pagination($model['data']['item']['catalogs']['page'], $model['data']['item']['catalogs']['totalPage']); ?>
    </article>
    <?php if (isset($model['data']['userUUID']) && $model['data']['userUUID'] === $model['data']['item']['creator_uuid']): ?>
        <div class="watchlist-detail__button-container">
            <a href="<?= "/watchlist/" . $model['data']['item']['watchlist_uuid'] . "/edit" ?>" id="edit"
                aria-label="Edit <?= $model['data']['item']['title'] ?>" class="btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/edit.php' ?>
            </a>
            <button type="submit" aria-label="Delete <?= $model['data']['item']['title'] ?>"
                class="dialog-trigger btn-icon">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</main>