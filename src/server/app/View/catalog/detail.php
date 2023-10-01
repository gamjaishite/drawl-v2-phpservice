<?php
$data = $model['data']
    ?>

<main>
    <article class="catalog-detail-header">
        <div class="catalog-detail-header-poster"></div>
        <img class="catalog-poster" src="<?= '/assets/images/catalogs/posters/' . $data['poster'] ?>"
            alt="<?= 'Poster of ' . $data['title'] ?>">
    </article>
    <article class="catalog-detail-content">
        <h2>
            <?= $data['title'] ?>
        </h2>
        <div class="tag">
            <?= $data['category'] ?>
        </div>
        <p>
            <?= $data['description'] ?>
        </p>
        <h3>Trailer</h3>
        <video class="catalog-trailer" controls>
            <source src="<?= '/assets/videos/catalogs/trailer/' . $data['trailer'] ?>" type="video/mp4">
        </video>
    </article>
</main>