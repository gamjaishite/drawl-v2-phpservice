<div id="watchlist-card-<?= $uuid ?>" class="card watchlist__card">
    <div class="card-content">
        <div class="list__poster">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <a href="/catalog/<?= $items[3 - $i]['catalogs_uuid'] ?>">
                    <img loading=<?= $loading ?> src=<?= "/assets/images/catalogs/posters/" . $items[3 - $i]['catalogs_poster'] ?> alt="Poster of <? $items[3 - $i]['catalogs_title'] ?>" class="poster" />
                </a>
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
                    <h3>
                        <?= $title ?>
                    </h3>
                </a>

            </div>
            <div class="watchlist__meta">
                <div class="watchlist__wrapper-type-author">
                    <span class="tag">
                        <?= $category ?>
                    </span>
                    <span class="subtitle">by <span class="author-name">
                            <?= $user->name ?>
                        </span></span>
                </div>
                <span class="subtitle">2 days ago</span>
            </div>
            <p>
                <?= $description ?>
            </p>
            <span class="watchlist__item-count">
                <?php require PUBLIC_PATH . 'assets/icons/clapperboard.php' ?>
                20 items
            </span>
        </div>
    </div>
    <div class="watchlist__actions">
        <button aria-label="Save <?= $title ?>" type="button" class="btn-ghost">
            <?php
            if (isset($saved)) {
                $type = $saved ? "filled" : "unfilled";
            }
            require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
        </button>
        <div class="watchlist__action-love">
            <button aria-label="Love <?= $title ?>" type="button" class="btn-ghost">
                <?php
                if (isset($loved)) {
                    $type = $loved ? "filled" : "unfilled";
                }
                require PUBLIC_PATH . 'assets/icons/love.php' ?>
            </button>
            <span>1M</span>
        </div>
    </div>
</div>