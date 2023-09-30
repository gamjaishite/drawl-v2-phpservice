<?php function selectCategory($selected)
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

function catalogCard(Catalog $catalog)
{
    $editable = true;
    $title = $catalog->title;
    $poster = $catalog->poster;
    $category = $catalog->category;
    $description = $catalog->description;
    $uuid = $catalog->uuid;
    require __DIR__ . '/../components/card/catalogCard.php';
}

function pagination(int $currentPage, int $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>
<main>
    <article class="search-filter">
        <form action="/catalog">
            <div class="input">
                <label>Category</label>
                <?php selectCategory($model['data']['category'] ?? ""); ?>
            </div>
            <button class="btn-primary" type="submit">
                Apply
            </button>
        </form>
        <a href="/catalog/create" class="btn btn-bold">
            <span class="icon-new">
                <?php require PUBLIC_PATH . 'assets/icons/plus.php' ?>
            </span>
            Add Catalog
        </a>
    </article>
    <article class="content">
        <?php foreach ($model['data']['catalogs']['items'] ?? [] as $catalog): ?>
            <?php catalogCard($catalog); ?>
        <?php endforeach; ?>
        <?php pagination($model['data']['catalogs']['page'], $model['data']['catalogs']['totalPage']); ?>
    </article>
</main>