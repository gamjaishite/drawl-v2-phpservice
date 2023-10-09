<?php
if (!function_exists("formatDate")) {
    function formatDate($createdAt)
    {
        require __DIR__ . '/../../../../config/dateFormat.php';
    }
}

if (!function_exists("likeAndSave")) {

    function likeAndSave($class, $icon, $ariaLabel = "")
    {
        $triggerClasses = "btn-ghost $class";
        $triggerText = "";
        $triggerIcon = $icon;
        $title = "Sign In Required";
        $content = 'signInRequired';
        require __DIR__ . '/../modal.php';
    }
}
?>

<div id="watchlist-card-<?= $uuid ?>" class="card watchlist__card">
    <div class="card-content">
        <div class="list__poster">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <?php
                if (!isset($posters[3 - $i])): ?>
                    <div>
                        <img loading=<?= $loading ?? 'eager' ?>
                            src="<?= "/assets/images/catalogs/posters/" . (isset($posters[3 - $i]) ? $posters[3 - $i]["poster"] : "no-poster.webp") ?>"
                            alt="Anime or Drama Poster" class="poster" />
                    </div>
                <?php else: ?>
                    <?php
                    if (!file_exists('assets/images/catalogs/posters/' . $posters[3 - $i]['poster'])) {
                        $posters[3 - $i]['poster'] = 'no-poster.webp';
                    }
                    ?>
                    <a href="/catalog<?= isset($posters[3 - $i]) ? "/" . $posters[3 - $i]["catalog_uuid"] : "" ?>">
                        <img loading=<?= $loading ?? 'eager' ?>
                            src="<?= "/assets/images/catalogs/posters/" . (isset($posters[3 - $i]) ? $posters[3 - $i]["poster"] : "no-poster.webp") ?>"
                            alt="Anime or Drama Poster" class="poster" />
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <div class="card-body">
            <div class="watchlist__visibility-title">
                <?php if ($visibility === "PRIVATE"): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                        <path
                            d="M9 7H10.05C10.2985 7 10.5 7.20145 10.5 7.45V12.55C10.5 12.7985 10.2985 13 10.05 13H1.95C1.70147 13 1.5 12.7985 1.5 12.55V7.45C1.5 7.20145 1.70147 7 1.95 7H3M9 7V4C9 3 8.4 1 6 1C3.6 1 3 3 3 4V7M9 7H3"
                            stroke="#0F172A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                <?php endif; ?>
                <a href="/watchlist/<?= $uuid ?>">
                    <h3 class="card-title">
                        <?= $title ?>
                    </h3>
                </a>
            </div>
            <div class="watchlist__meta">
                <div class="watchlist__wrapper-type-author">
                    <span class="tag">
                        <?= $category ?>
                    </span>
                    <?php for ($i = 0; $i < min(3, count($item["tags"])); $i++): ?>
                        <span class="tag">
                            <?= $item["tags"][$i]["name"] ?>
                        </span>
                    <?php endfor; ?>
                    <span class="subtitle">by <span class="author-name">
                            <?= $creator ?>
                        </span></span>
                </div>
                <span class="subtitle">
                    <?= formatDate($createdAt); ?>
                </span>

            </div>
            <p class="watchlist__description">
                <?= $description ?>
            </p>
            <span class="watchlist__item-count">
                <?php require PUBLIC_PATH . 'assets/icons/clapperboard.php' ?>
                <?= $itemCount ?> items
            </span>
        </div>
    </div>
    <div class="watchlist__actions">
        <div class="watchlist__action-save">
            <?php if (!$self): ?>
                <?php if ($userUUID == ""): ?>
                    <?php likeAndSave("btn__save", "bookmark", "Save " . $title); ?>
                <?php else: ?>
                    <button aria-label="Save <?= $title ?>" type="button" class="btn-ghost btn__save" data-id="<?= $uuid ?>"
                        data-saved="<?= $saved ?>">
                        <?php
                        if (isset($saved)) {
                            $type = $saved ? "filled" : "unfilled";
                        }
                        require PUBLIC_PATH . 'assets/icons/bookmark.php'
                            ?>
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="watchlist__action-love">
            <?php if ($userUUID == ""): ?>
                <?php likeAndSave("btn__like", "love", "Love " . $title); ?>
            <?php else: ?>
                <button aria-label="Love <?= $title ?>" type="button" class="btn-ghost btn__like" data-id="<?= $uuid ?>"
                    data-liked="<?= $loved ?>">
                    <?php
                    if (isset($loved)) {
                        $type = $loved ? "filled" : "unfilled";
                    }
                    require PUBLIC_PATH . 'assets/icons/love.php' ?>
                </button>
            <?php endif; ?>
            <span data-id="<?= $uuid ?>">
                <?= $loveCount ?>
            </span>
        </div>
    </div>
</div>