<?php function selectCategory()
{
    $id = 'category';
    $placeholder = 'Select Category';
    $content = [
        "Drama",
        "Anime"
    ];
    require __DIR__ . '/../components/select.php';
}

function catalogCard()
{
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

function alert($title, $message)
{
    $type = 'error';
    require __DIR__ . '/../components/alert.php';
}
?>

<main>
    <h2>
        <?= $model['title'] ?>
    </h2>
    <?php if (isset($model['error'])): ?>
        <?php alert('Failed to ' . $model['title'], $model['error']); ?>
    <?php endif; ?>
    <form action="/catalog/create" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label class="input-required">Category</label>
            <?php selectCategory(); ?>
        </div>

        <div class="input-group">
            <label for="titleField" class="input-required">Title</label>
            <input type="text" id="titleField" name="title" placeholder="Title"
                value="<?= $model['data']['title'] ?? "" ?>" maxlength="40" required>
        </div>

        <div class="input-group">
            <label for="descriptionField">Description</label>
            <textarea name="description" id="descriptionField" value="<?= $model['data']['description'] ?? "" ?>"
                maxlength="255"></textarea>
        </div>

        <div class="input-group">
            <label for="posterField" class="input-required">Poster</label>
            <input type="file" id="posterField" name="poster" accept="image/*" required>
        </div>
        <div class="input-group">
            <label for="trailerField">Trailer</label>
            <input type="file" id="trailerField" name="trailer" accept="trailer/mp4">
        </div>

        <button class="btn-bold" type="submit">
            Submit
        </button>
    </form>
</main>