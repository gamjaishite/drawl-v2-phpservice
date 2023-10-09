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

if (!function_exists("likeAndSave")) {

    function likeAndSave($class, $icon, $ariaLabel = "")
    {
        $triggerClasses = "btn-ghost $class";
        $triggerText = "";
        $triggerIcon = $icon;
        $title = "Sign In Required";
        $content = 'signInRequired';
        require __DIR__ . '/../components/modal.php';
    }
}

?>

<main>
    <article class="header">
        <div class="detail">
            <h2>
                <?php if ($model['data']['item']['visibility'] === "PRIVATE"): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                        <path
                            d="M9 7H10.05C10.2985 7 10.5 7.20145 10.5 7.45V12.55C10.5 12.7985 10.2985 13 10.05 13H1.95C1.70147 13 1.5 12.7985 1.5 12.55V7.45C1.5 7.20145 1.70147 7 1.95 7H3M9 7V4C9 3 8.4 1 6 1C3.6 1 3 3 3 4V7M9 7H3"
                            stroke="#0F172A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                <?php endif; ?>
                <?= $model['data']['item']['title'] ?>
            </h2>
            <div class="container-subtitle">
                <div class="tag">
                    <?= $model['data']['item']['category'] ?>
                </div>
                <p class="subtitle">
                    <?= $model['data']['item']['creator'] ?> |
                    <?= formatDate($model['data']['item']['created_at']) ?>
                </p>
            </div>
            <div class="watchlist__wrapper-type-author">
                <?php foreach ($model["data"]["item"]["tags"] as $tag): ?>
                    <span class="tag">
                        <?= $tag["name"] ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <p>
                <?= $model['data']['item']['description'] ?>
            </p>
        </div>
        <div class="container-button">
            <div class="container-btn-love">
                <?php if ($model['data']['userUUID'] == ""): ?>
                    <?php likeAndSave("btn__like", "love", "Love " . $model['data']['item']['title']); ?>
                <?php else: ?>
                    <button class="btn-ghost btn__like" data-id="<?= $model["data"]["item"]["watchlist_uuid"] ?>"
                        data-liked="<?= $model["data"]["item"]["liked"] ?>">
                        <?php
                        $type = (isset($model['data']["item"]['liked']) && $model['data']["item"]['liked']) ? "filled" : "unfilled";
                        require PUBLIC_PATH . 'assets/icons/love.php' ?>
                    </button>
                <?php endif; ?>
                <span data-id="<?= $model["data"]["item"]["watchlist_uuid"] ?>">
                    <?= $model['data']['item']['like_count'] ?>
                </span>
            </div>
            <div class="container-btn-love">
                <?php if (!isset($model['data']['userUUID']) || $model['data']['userUUID'] != $model['data']['item']['creator_uuid']): ?>
                    <?php if ($model['data']['userUUID'] == ""): ?>
                        <?php likeAndSave("btn__save", "bookmark", "Save " . $model['data']['item']['title']); ?>
                    <?php else: ?>
                        <button class="btn-ghost btn__save" data-id="<?= $model["data"]["item"]["watchlist_uuid"] ?>"
                            data-saved="<?= $model["data"]["item"]["saved"] ?>">
                            <?php
                            $type = (isset($model['data']['item']['saved']) && $model['data']['item']['saved']) ? "filled" : "unfilled";
                            require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
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
                class="dialog-trigger btn-icon btn__delete" data-id="<?= $model["data"]["item"]["watchlist_uuid"] ?>">
                <?php require PUBLIC_PATH . 'assets/icons/trash.php' ?>
            </button>
        </div>
    <?php endif; ?>
</main>