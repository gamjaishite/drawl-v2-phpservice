<?php
function selectCategory()
{
    $id = 'category';
    $placeholder = 'Select Category';
    $content = [
        "MIXED",
        "DRAMA",
        "ANIME"
    ];
    $selected = validateQueryParams($id, $content);
    require __DIR__ . '/../components/select.php';
}

function sortBy()
{
    $id = 'sortBy';
    $placeholder = 'Sort By';
    $content = [
        "DATE",
        "LOVE"
    ];
    $selected = validateQueryParams($id, $content);
    require __DIR__ . '/../components/select.php';
}

function tags($tags)
{
    $id = 'tag';
    $placeholder = 'Select Tag';
    $content = $tags;
    $selected = validateQueryParams($id, $content);

    require __DIR__ . '/../components/select.php';
}

function vallidateOrder(): ?string
{
    if (!isset($_GET["order"]) || ($_GET["order"] != "asc" && $_GET["order"] != "desc"))
        return null;
    return $_GET["order"];
}

function fillLove($item)
{
    $type = $item["like_status"] == 1 ? "filled" : "unfilled";
    require PUBLIC_PATH . 'assets/icons/love.php';
}

function watchlist($item, $userUUID)
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
    $loveCount = $item["love_count"];
    $loved = $item["loved"];
    $saved = $item["saved"];
    $self = ($userUUID == $item["creator_uuid"]);

    require __DIR__ . '/../components/card/watchlistCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}

?>

<main>
    <form class="form-search-filter">
        <div class="search">
            <?php require PUBLIC_PATH . 'assets/icons/search.php'; ?>
            <input type="text" name="search" placeholder="Search title or creator" class="input-default input-search"
                   value="<?= trim($_GET['search'] ?? '') ?? '' ?>"/>
        </div>
        <div class="filter">
            <?php tags($model["data"]["tags"]); ?>
            <?php selectCategory(); ?>
            <div class="filter__sort">
                <?php sortBy(); ?>
                <button aria-label="Sort Category" type="button" class="btn-sort">
                    <span class="span-icon btn-sort-asc <?= vallidateOrder() == 'desc' || !vallidateOrder() ? 'hidden' : '' ?>">
                        <?php require PUBLIC_PATH . 'assets/icons/asc.php' ?>
                    </span>
                    <span class="span-icon btn-sort-desc <?= vallidateOrder() == 'asc' ? 'hidden' : '' ?>">
                        <?php require PUBLIC_PATH . 'assets/icons/desc.php' ?>
                    </span>
                </button>
                <input type="hidden" id="order" name="order" value="<?= vallidateOrder() ?? 'desc' ?>"/>
            </div>
        </div>
        <button type="submit" id="btn-apply" class="btn-primary btn--apply">Apply</button>
    </form>


    <a class="btn btn-primary" href='/watchlist/create'>
        <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
        New List
    </a>

    <div class="list__watchlist">
        <?php if (count($model["data"]["items"]) == 0) : ?>
            <div class="loading">No Results Found.</div>
        <?php endif; ?>
        <?php foreach ($model["data"]["items"] as $item) : ?>
            <?php watchlist($item, $model["data"]["userUUID"]); ?>
        <?php endforeach; ?>
        <?php if (count($model["data"]["items"]) > 0) : ?>
            <?php pagination($model["data"]["page"], $model["data"]["pageTotal"]); ?>
        <?php endif; ?>
    </div>

</main>