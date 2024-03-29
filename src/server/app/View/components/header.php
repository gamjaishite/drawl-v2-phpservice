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
    <link rel="stylesheet" href="/css/global.css">
    <!-- <link rel="stylesheet" href="/css/components/select.css">
    <link rel="stylesheet" href="/css/components/card.css">
    <link rel="stylesheet" href="/css/components/button.css">
    <link rel="stylesheet" href="/css/components/pagination.css">
    <link rel="stylesheet" href="/css/components/input.css">
    <link rel="stylesheet" href="/css/components/form.css">
    <link rel="stylesheet" href="/css/components/icon.css">
    <link rel="stylesheet" href="/css/components/textarea.css">
    <link rel="stylesheet" href="/css/components/modal.css">
    <link rel='stylesheet' href='/css/components/alert.css'> -->

    <?php foreach ($model['styles'] ?? [] as $style): ?>
        <link rel='stylesheet' href='<?= $style ?>'>
    <?php endforeach; ?>

    <!-- JS -->
    <script type="text/javascript" src="/js/global.js" defer></script>
    <script type='text/javascript' src='/js/components/navbar.js' defer></script>
    <script type="text/javascript" src="/js/components/select.js" defer></script>
    <script type="text/javascript" src="/js/components/modal.js" defer></script>
    <script type='text/javascript' src='/js/components/alert.js' defer></script>

    <?php foreach ($model['js'] ?? [] as $js): ?>
        <script type='text/javascript' src='<?= $js ?>' defer></script>
    <?php endforeach; ?>
</head>

<body>