<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?=$model['title'] ?? 'Drawl'?></title>
        <link rel='stylesheet' href='./css/global.css'>
        <?php foreach ($model['styles'] as $style): ?>
            <link rel='stylesheet' href='<?=$style?>'>
        <?php endforeach;?>
    </head>
<body>