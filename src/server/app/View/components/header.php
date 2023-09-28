<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $description ?? '#1 Drama and Anime Watch List Website' ?>" />

    <title>
        <?= 'Drawl | ' . $model['title'] ?? '🌸' ?>
    </title>

    <!-- CSS -->
    <link rel='stylesheet' href='/css/global.css'>
    <link rel='stylesheet' href='/css/components/select.css'>
    <link rel='stylesheet' href='/css/components/card.css'>
    <link rel='stylesheet' href='/css/components/button.css'>
    <link rel='stylesheet' href='/css/components/tag.css'>
    <link rel='stylesheet' href='/css/components/pagination.css'>
    <link rel='stylesheet' href='/css/components/input.css'>
    <link rel='stylesheet' href='/css/components/form.css'>
    <link rel='stylesheet' href='/css/components/icon.css'>


    <?php foreach ($model['styles'] ?? [] as $style) : ?>
        <link rel='stylesheet' href='<?= $style ?>'>
    <?php endforeach; ?>

    <!-- JS -->
    <?php foreach ($model['js'] ?? [] as $js) : ?>
        <script type='text/javascript' src='<?= $js ?>' defer></script>
    <?php endforeach; ?>
    <script type='text/javascript' src='/js/select.js' defer></script>
</head>

<body>