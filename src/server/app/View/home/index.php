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

?>

<?php
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

?>

<?php
function vallidateOrder(): ?string
{
    if (!isset($_GET["order"]) || ($_GET["order"] != "asc" && $_GET["order"] != "desc"))
        return null;
    return $_GET["order"];
}

?>

<?php
function fillLove($item)
{
    $type = $item["like_status"] == 1 ? "filled" : "unfilled";
    require PUBLIC_PATH . 'assets/icons/love.php';
}

?>

<?php
function watchlist($item)
{
    $uuid = $item["watchlist_uuid"];
    $posters = $item["posters"];
    $visibility = $item["visibility"];
    $title = $item["title"];
    $category = $item["category"];
    $creator = $item["creator"];
    $updatedAt = $item["updated_at"];
    $description = $item["description"];
    $itemCount = $item["item_count"];

    require __DIR__ . '/../components/card/watchlistCard.php';
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
            <?php selectCategory(); ?>
            <div class="filter__sort">
                <?php sortBy(); ?>
                <button aria-label="Sort Category" type="button" class="btn-sort">
               <span class="span-icon btn-sort-asc <?= vallidateOrder() == 'desc' ? 'hidden' : '' ?>">
                  <?php require PUBLIC_PATH . 'assets/icons/asc.php' ?>
               </span>
                    <span class="span-icon btn-sort-desc <?= vallidateOrder() == 'asc' || !vallidateOrder() ? 'hidden' : '' ?>">
                  <?php require PUBLIC_PATH . 'assets/icons/desc.php' ?>
               </span>
                </button>
                <input type="hidden" id="order" name="order" value="<?= vallidateOrder() ?? 'asc' ?>"/>
            </div>
        </div>
        <button type="submit" id="btn-apply" class="btn-primary btn--apply">Apply</button>
    </form>


    <a class="btn btn-primary" href='/watchlist/create'>
        <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
        New List
    </a>

    <div class="list__watchlist">
        <?php foreach ($model["data"] as $item) : ?>
            <?php watchlist($item); ?>
            <!--            <div class="watchlist">-->
            <!--                <div class="list__poster">-->
            <!--                    --><?php //for ($i = 0; $i < 4; $i++): ?>
            <!--                        <img loading="lazy"-->
            <!--                             src="--><?php //= "/assets/images/catalogs/posters/" . (isset($item["posters"][$i]) ? $item["posters"][$i]["poster"] : "no-poster.webp") ?><!--"-->
            <!--                             alt="top---><?php //= $i + 1 ?><!--"-->
            <!--                             class="poster"/>-->
            <!--                    --><?php //endfor; ?>
            <!--                </div>-->
            <!--                <div class="watchlist__content">-->
            <!--                    <h3 class="watchlist__title">--><?php //= $item['title'] ?><!--</h3>-->
            <!--                    <div class="watchlist__meta">-->
            <!--                        <div class="watchlist__wrapper-type-author">-->
            <!--                            <span class="watchlist__type">--><?php //= $item["category"] ?><!--</span>-->
            <!--                            <span class="catalog-list-content-author">by <span-->
            <!--                                        class="author-name">--><?php //= $item["creator"] ?><!--</span></span>-->
            <!--                        </div>-->
            <!--                        <span class="span-icon watchlist__dot">-->
            <!--                     --><?php //require PUBLIC_PATH . 'assets/icons/dot.php' ?>
            <!--                  </span>-->
            <!--                        <span class="subtitle">2 days ago</span>-->
            <!--                    </div>-->
            <!--                    <p class="watchlist__description">--><?php //= $item["description"] ?><!--</p>-->
            <!--                    <span class="watchlist__item-count">-->
            <!--                  --><?php //require PUBLIC_PATH . 'assets/icons/clapperboard.php' ?>
            <!--                        --><?php //= $item["item_count"] ?><!-- items-->
            <!--               </span>-->
            <!--                </div>-->
            <!--                <div class="watchlist__actions">-->
            <!--                    <button aria-label="Save --><?php //= $item["title"] ?><!--" class="catalog-list-btn catalog-list-btn-save"-->
            <!--                            type="button">-->
            <!--                        --><?php //require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
            <!--                    </button>-->
            <!--                    <div class="watchlist__action-love">-->
            <!--                        <button aria-label="Love --><?php //= $item["title"] ?><!--" class="catalog-list-btn catalog-list-btn-love"-->
            <!--                                type="button">-->
            <!--                            --><?php //fillLove($item) ?>
            <!--                        </button>-->
            <!--                        <span>--><?php //= $item["like_count"] ?><!--</span>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
        <?php endforeach; ?>
    </div>
</main>