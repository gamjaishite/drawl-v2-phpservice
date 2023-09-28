<!-- Attributes
- id
- placeholder
- content
-->

<?php
if (!function_exists('validateQueryParams')) {
    function validateQueryParams($id, $content): ?string
    {
        if (!isset($content)) return null;
        if (isset($_GET[$id]) && in_array($_GET[$id], $content, TRUE)) {
            return $_GET[$id];
        }
        return null;
    }
}
?>

<div class="c-select-menu" id="<?= $id ?>">
    <div class="c-select-btn">
        <span class="c-select-btn-text"><?= validateQueryParams($id, $content) ?? $placeholder ?? 'Select' ?></span>
        <?php require  PUBLIC_PATH . 'assets/icons/chevron-down.php' ?>
    </div>

    <input type="hidden" id="<?= $id ?>" name="<?= $id ?>" value="<?= validateQueryParams($id, $content) ?>" />

    <?php if (isset($content)) : ?>
        <ul class="c-select-options c-select-hide">
            <?php foreach ($content as $item) : ?>
                <li class="c-select-option">
                    <span class="c-select-option-text"><?= $item ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>