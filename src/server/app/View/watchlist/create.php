<?php
function visibility()
{
    $id = 'visibility';
    $content = [
        "Public",
        "Private"
    ];
    $selected = 'Public';
    require __DIR__ . '/../components/select.php';
}
?>

<?php
function addItem()
{
    $triggerClasses = "btn-outline btn__add-item";
    $triggerText = 'Add Item';
    $triggerIcon = 'plus';
    $title = 'Search';
    $content = 'watchlistAddItem';
    require __DIR__ . '/../components/modal.php';
}
?>

<?php
function getItem()
{
    require __DIR__ . '/../components/watchlist/watchlistItem.php';
}
?>

<div class="container__default">
    <h2 class="title-h2">New Watchlist</h2>
    <div class="container__create-watchlist">
        <div class="container__form">
            <form class="form-default form__create-watchlist">
                <div class="form-input-default">
                    <label for="title" class="input-required">Title</label>
                    <input type="text" name="title" id="title" class="input-default" placeholder="Best Incest Anime and Drama" />
                </div>
                <div class="form-input-default">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="input-default" placeholder="Enter your watchlist description"></textarea>
                </div>
                <div class="form-input-default">
                    <label for="visibility" class="input-required">Visibility</label>
                    <?php visibility(); ?>
                </div>

                <h3 class="watchlist-items__title">Items</h3>
                <div class="watchlist-items">
                    <?php getItem();  ?>
                    <?php getItem();  ?>
                </div>
            </form>


        </div>
        <div class="actions">
            <?php addItem(); ?>
            <label class="btn btn-bold btn__save">
                Save
            </label>
        </div>
    </div>
</div>