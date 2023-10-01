<?php
function catalogCard($catalog)
{
    $editable = false;
    $title = $catalog['title'];
    $poster = $catalog['poster'];
    $category = $catalog['category'];
    $description = $catalog['description'];
    require __DIR__ . '/../components/card/catalogCard.php';
}

function commentCard($comment)
{
    $is_user = $comment['is_user'];
    $user_image = $comment['user_image'];
    $user_name = $comment['user_name'];
    $content = $comment['content'];
    $created_at = $comment['created_at'];
    require __DIR__ . '/../components/card/commentCard.php';
}

function pagination($currentPage, $totalPage)
{
    require __DIR__ . '/../components/pagination.php';
}
?>

<main>
    <article class="header">
        <div class="detail">
            <h2>
                <?= $model['data']['title'] ?>
            </h2>
            <div class="container-subtitle">
                <div class="tag">
                    <?= $model['data']['category'] ?>
                </div>
                <p class="subtitle">
                    <?= $model['data']['username'] ?> |
                    <?= $model['data']['created_at'] ?>
                </p>
            </div>
            <p>
                <?= $model['data']['description'] ?>
            </p>
        </div>
        <div class="container-button">
            <div class="container-btn-love">
                <button class="btn-ghost">
                    <?php
                    $type = "filled";
                    require PUBLIC_PATH . 'assets/icons/love.php' ?>
                </button>
                <span>1M</span>
            </div>
            <button class="btn-ghost">
                <?php
                $type = "unfilled";
                require PUBLIC_PATH . 'assets/icons/bookmark.php' ?>
            </button>
        </div>
    </article>
    <article id="catalogs" class="content">
        <?php foreach ($model['data']['catalogs']['items'] ?? [] as $catalog): ?>
            <?php catalogCard($catalog); ?>
        <?php endforeach; ?>
        <?php pagination($model['data']['catalogs']['currentPage'], $model['data']['catalogs']['totalPage']); ?>
    </article>
    <!-- <article id="comments" class="content">
        <h3>Comments</h3>
        <form action="">
            <textarea name="comment" id="comment" cols="30" rows="10" placeholder="Write a comment"></textarea>
            <button class="btn-bold" type="submit">
                Submit
            </button>
        </form>
        <?php foreach ($model['data']['comments']['items'] ?? [] as $comment): ?>
            <?php commentCard($comment); ?>
        <?php endforeach; ?>
        <button id="show-more" class="btn-text">
            Show More
        </button>
    </article> -->
</main>