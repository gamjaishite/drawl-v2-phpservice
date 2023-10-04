<?php
function visibility($visibility)
{
    $id = 'visibility';
    $content = [
        "PUBLIC",
        "PRIVATE"
    ];
    $selected = $visibility ?? 'PUBLIC';
    require __DIR__ . '/../components/select.php';
}

function addItem()
{
    $triggerClasses = "btn-outline btn__add-item";
    $triggerText = 'Add Item';
    $triggerIcon = 'plus';
    $title = 'Search';
    $content = 'watchlistAddItem';
    require __DIR__ . '/../components/modal.php';
}

function getItem()
{
    require __DIR__ . '/../components/watchlist/watchlistItem.php';
}

function alert($title, $message)
{
    $type = 'error';
    require __DIR__ . '/../components/alert.php';
}

?>

<main>
    <?php if (isset($error)) echo $error; ?>
    <h2 class="title-h2">New Watchlist</h2>
    <?php if (isset($model['error'])) {
        alert('Failed to ' . $model['title'], $model['error']);
    } ?>
    <div class="container__create-watchlist">
        <div class="container__form">
            <form method="post" action="/watchlist/create" class="form-default form__create-watchlist">
                <div class="form-input-default">
                    <label for="title" class="input-required">Title</label>
                    <input type="text" name="title" id="title" class="input-default"
                           placeholder="Best Incest Anime and Drama" value="<?= $model["data"]["title"] ?? '' ?>"
                           required/>
                </div>
                <div class="form-input-default">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="input-default"
                              placeholder="Enter your watchlist description"
                    ><?= $model["data"]["description"] ?? '' ?></textarea>
                </div>
                <div class="form-input-default">
                    <label for="visibility" class="input-required">Visibility</label>
                    <?php visibility(isset($model["data"]) ? $model["data"]["visibility"] : null); ?>
                </div>

                <h3 class="watchlist-items__title">Items</h3>
                <div class="watchlist-items"></div>

                <input id="input-submit" type="submit" class="hidden"/>
            </form>


        </div>
        <div class="actions">
            <?php addItem(); ?>
            <label for="input-submit" class="btn btn-bold btn__save">
                Save
            </label>
        </div>
    </div>
</main>