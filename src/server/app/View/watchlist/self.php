<?php

function watchlistCard(Watchlist $watchlist, bool $saved = false, bool $loved = false, string $loading = "eager")
{
    $uuid = $watchlist->uuid;
    $title = $watchlist->title;
    $description = $watchlist->description;
    $category = $watchlist->category;
    $visibility = $watchlist->visibility;
    $user = $watchlist->user;
    $items = $watchlist->items;
    require __DIR__ . '/../components/card/watchlistCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>

<main>
    <section class="search-filter">
        <h2>My Watchlist</h2>
        <div class="visibility">
            <a id="visibility-all" href="/profile/watchlist?visibility=all"
                class="btn <?= $model['data']['visibility'] === "all" ? "selected" : "" ?>">All</a>
            <a id="visibility-private" href="/profile/watchlist?visibility=private"
                class="btn <?= $model['data']['visibility'] === "private" ? "selected" : "" ?>">Private</a>
            <a id="visibility-public" href="/profile/watchlist?visibility=public"
                class="btn <?= $model['data']['visibility'] === "public" ? "selected" : "" ?>">Public</a>
        </div>
    </section>
    <section class="content">
        <?php for ($i = 0; $i < count($model['data']['watchlists']['items']); $i++): ?>
            <?php watchlistCard($model['data']['watchlists']['items'][$i], true, false, $i < 4 ? "eager" : "lazy"); ?>
        <?php endfor; ?>
        <?php pagination($model['data']['watchlists']['page'], $model['data']['watchlists']['totalPage']); ?>
    </section>
</main>