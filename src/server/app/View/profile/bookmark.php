<?php

function watchlistCard(array $item, string $userUUID, string $loading = "eager")
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
    $loved = $item["liked"];
    $self = ($userUUID == $item["creator_uuid"]);
    $saved = true;

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
                <h2>There's No Bookmark Yet...</h2>
            </div>
        </div>
    <?php endif; ?>
    <section class="content">
        <?php for ($i = 0; $i < count($model['data']['bookmarks']['items']); $i++): ?>
            <?php watchlistCard($model['data']['bookmarks']['items'][$i], $model["data"]["userUUID"], $i < 4 ? "eager" : "lazy"); ?>
        <?php endfor; ?>
        <?php pagination($model['data']['bookmarks']['page'], $model['data']['bookmarks']['totalPage']); ?>
    </section>
</main>