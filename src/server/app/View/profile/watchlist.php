<?php

function watchlistCard(array $item, string $userUUID, bool $saved = false, bool $loved = false, string $loading = "eager")
{
    $uuid = $item["watchlist_uuid"];
    $posters = $item["posters"];
    $visibility = $item["visibility"];
    $title = $item["title"];
    $category = $item["category"];
    $creator = $item["creator"];
    $createdAt = $item["created_at"];
    $description = $item["description"];
    $itemCount = $item["item_count"];
    $loveCount = $item["like_count"];
    $self = ($userUUID == $item["creator_uuid"]);

    require __DIR__ . '/../components/card/watchlistCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}

?>

<main class="watchlist-self">
    <section class="search-filter">
        <div>
            <h2>My Watchlist</h2>
            <div class="visibility">
                <a id="visibility-all" href="/profile/watchlist?visibility=all"
                   class="btn <?= $model['data']['visibility'] === "all" ? "selected" : "" ?>">All</a>
                <a id="visibility-private" href="/profile/watchlist?visibility=private"
                   class="btn <?= $model['data']['visibility'] === "private" ? "selected" : "" ?>">Private</a>
                <a id="visibility-public" href="/profile/watchlist?visibility=public"
                   class="btn <?= $model['data']['visibility'] === "public" ? "selected" : "" ?>">Public</a>
            </div>
        </div>
        <a href="/watchlist/create" class="btn btn-bold">
            <span class="icon-new">
                <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
            </span>
            Add Watchlist
        </a>
    </section>
    <?php if (count($model['data']['watchlists']['items']) == 0): ?>
        <div class="no-item__container">
            <h1>Oops! 😣</h1>
            <div>
                <h2>There's No Watchlist Yet...</h2>
                <p>...Go to <a href="/">Home</a> or <a href="/watchlist/create">create some watchlist!</a></p>
            </div>
        </div>
    <?php endif; ?>
    <section class="content">
        <?php for ($i = 0; $i < count($model['data']['watchlists']['items']); $i++): ?>
            <?php watchlistCard($model['data']['watchlists']['items'][$i], $model["data"]["userUUID"], true, false, $i < 4 ? "eager" : "lazy",); ?>
        <?php endfor; ?>
        <?php pagination($model['data']['watchlists']['page'], $model['data']['watchlists']['totalPage']); ?>
    </section>
</main>