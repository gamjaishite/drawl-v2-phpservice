<div class="container container-watchlist-create">
    <h2 class="title-h2">New Watchlist</h2>
    <div class="container-form-watchlist-create">
        <div class="subcon-form-watchlist-create">
            <form class="form-default form-watchlist-create">
                <div class="form-input-default">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="input-default" />
                </div>
                <div class="form-input-default">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="input-default"> </textarea>
                </div>
                <div class="form-input-default">
                    <span>Visibility</span>
                    <div class="form-input-radio-default">
                        <input type="radio" id="private" name="visibility" class="input-radio-default" value="private" />
                        <label for="private">Private</label>
                    </div>
                    <div class="form-input-radio-default">
                        <input type="radio" id="public" name="visibility" class="input-radio-default" value="public" />
                        <label for="public">Public</label>
                    </div>
                </div>
            </form>

            <div class="container-watchlist-items">
                <h3 class="title-h3">Items</h3>
            </div>
        </div>
        <div class="form-watchlist-actions">
            <button class="btn-outline btn-watchlist-add-item">
                <?php require PUBLIC_PATH . '/assets/icons/plus.php' ?>
                Add Items
            </button>
            <label class="btn-primary-dark btn-watchlist-save">
                Save
            </label>
        </div>
    </div>
</div>