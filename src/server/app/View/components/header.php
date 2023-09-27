<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $description ?? '#1 Drama and Anime Watch List Website' ?>" />

    <title><?= $model['title'] ?? 'Drawl' ?></title>

    <!-- CSS -->
    <link rel='stylesheet' href='/css/global.css'>
    <link rel='stylesheet' href='/css/components/select.css'>
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