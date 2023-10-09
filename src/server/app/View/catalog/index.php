<?php
function selectCategory($selected)
{
    $id = 'category';
    $placeholder = 'MIXED';
    $content = [
        "MIXED",
        "DRAMA",
        "ANIME"
    ];
    require __DIR__ . '/../components/select.php';
}

function catalogCard(Catalog $catalog, bool $isAdmin = false)
{
    $title = $catalog->title;
    $poster = $catalog->poster;
    $category = $catalog->category;
    $description = $catalog->description;
    $uuid = $catalog->uuid;
    $id = $catalog->id;
    $editable = $isAdmin;
    require __DIR__ . '/../components/card/catalogCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>
<main>
    <section class="search-filter">
        <form action="/catalog">
            <div class="input">
                <label>Category</label>
                <?php selectCategory($model['data']['category'] ?? ""); ?>
            </div>
            <button class="btn-primary" type="submit">
                Apply
            </button>
        </form>
        <?php if ($model['data']['userRole'] && $model['data']['userRole'] === "ADMIN"): ?>
            <a href="/catalog/create" class="btn btn-bold">
                <span class="icon-new">
                    <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
                </span>
                Add Catalog
            </a>
        <?php endif; ?>
    </section>
    <?php if (count($model['data']['catalogs']['items']) == 0): ?>
        <div class="no-item__container">
            <h1>Oops! 😣</h1>
            <div>
                <h2>There's No Catalog Yet...</h2>
                <p>...we will add more soon!</p>
            </div>
        </div>
    <?php endif; ?>
    <section class="content">
        <?php foreach ($model['data']['catalogs']['items'] ?? [] as $catalog): ?>
            <?php catalogCard($catalog, $model['data']['userRole'] && $model['data']['userRole'] === "ADMIN"); ?>
        <?php endforeach; ?>
        <?php pagination($model['data']['catalogs']['page'], $model['data']['catalogs']['totalPage']); ?>
    </section>
</main>