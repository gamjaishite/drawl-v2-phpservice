<?php function selectCategory($selected)
{
    $id = 'category';
    $placeholder = 'Select Category';
    $content = [
        "DRAMA",
        "ANIME"
    ];
    require __DIR__ . '/../components/select.php';
}
?>

<main>
    <h2>
        <?= $model['title'] ?>
    </h2>
    <form id="catalog-edit-form" enctype="multipart/form-data">
        <div class="input-group">
            <label class="input-required">Category</label>
            <?php selectCategory($model['data']['category'] ?? 'ANIME'); ?>
        </div>
        <div class="input-group">
            <label for="titleField" class="input-required">Title</label>
            <input type="text" id="titleField" name="title" placeholder="Title"
                value="<?= $model['data']['title'] ?? "" ?>" maxlength="40" required>
        </div>
        <div class="input-group">
            <label for="descriptionField">Description</label>
            <textarea placeholder="Enter description" name="description" id="descriptionField" maxlength="255"><?php if (isset($model['data'])) {
                echo $model['data']['description'];
            } ?></textarea>
        </div>
        <div class="input-group">
            <label for="posterField" class="input-required">Poster</label>
            <img class="poster" src="<?= '/assets/images/catalogs/posters/' . $model['data']['poster'] ?>"
                alt="<?= 'Poster of ' . $model['data']['title'] ?>">
            <input type="file" id="posterField" name="poster" accept="image/*">
        </div>
        <div class="input-group">
            <?php if (isset($model['data']['trailer']) && $model['data']['trailer'] !== null): ?>
                <video class="catalog-trailer" controls>
                    <source src="<?= '/assets/videos/catalogs/trailers/' . $model['data']['trailer'] ?>" type="video/mp4">
                </video>
            <?php endif; ?>
            <label for="trailerField">Trailer</label>
            <input type="file" id="trailerField" name="trailer" accept="video/mp4">
        </div>
        <button id="edit" class="btn-bold" type="submit">
            Edit
        </button>
    </form>
</main>