<?php function selectCategory()
{
    $id = 'type';
    $placeholder = 'Mixed';
    $content = [
        "Mixed",
        "Drama",
        "Anime"
    ];
    require __DIR__ . '/../components/select.php';
}

function catalogCard()
{
    $editable = true;
    $title = 'Snowdrop';
    $poster = 'jihu-7.jpg';
    $category = 'DRAMA';
    $description = "Looking for a new animal companion, but tired of the same ol' cats and dogs? Here are some manga featuring unusual creatures that you'd never expect to see as pets!";
    require __DIR__ . '/../components/card/catalogCard.php';
}

function pagination()
{
    $currentPage = 7;
    $totalPage = 10;
    require __DIR__ . '/../components/pagination.php';
}
?>
<main class="container container-catalog">
    <article class="search-filter">
        <form>
            <div class="input">
                <label>Type</label>
                <?php selectCategory(); ?>
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
        <?php foreach ($model['data']['catalogs'] ?? [] as $catalog): ?>
            <?php catalogCard(); ?>
        <?php endforeach; ?>
        <?php pagination(); ?>
    </article>
</main>