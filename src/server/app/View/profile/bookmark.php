<?php

function watchlistCard(array $item, bool $saved = true, bool $loved = false, string $loading = "eager")
{
    $uuid = $item["watchlist_uuid"];
    $posters = $item["posters"];
    $visibility = $item["visibility"];
    $title = $item["title"];
    $category = $item["category"];
    $creator = $item["creator"];
    $updatedAt = $item["updated_at"];
    $createdAt = $item["created_at"];
    $description = $item["description"];
    $itemCount = $item["item_count"];
    $loveCount = $item["like_count"];

    require __DIR__ . '/../components/card/watchlistCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>

<main class="watchlist-self">
    <section class="search-filter">
        <h2>My Bookmark</h2>
    </section>
    <?php if (count($model['data']['bookmarks']['items']) == 0): ?>
        <div class="no-item__container">
            <h1>Oops! 😣</h1>
            <div>
                <h2>There's No Watchlist Yet...</h2>
                <p>...Go to <a href="/">Home</a> or <a href="/watchlist/create">create some watchlist!</a></p>
            </div>
        </div>
    <?php endif; ?>
    <section class="content">
        <?php for ($i = 0; $i < count($model['data']['bookmarks']['items']); $i++): ?>
            <?php watchlistCard($model['data']['bookmarks']['items'][$i], true, false, $i < 4 ? "eager" : "lazy"); ?>
        <?php endfor; ?>
        <?php pagination($model['data']['bookmarks']['page'], $model['data']['bookmarks']['totalPage']); ?>
    </section>
</main>