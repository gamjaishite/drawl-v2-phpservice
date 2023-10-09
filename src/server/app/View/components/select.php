<!-- Attributes
- id
- placeholder
- content
- selected
-->

<div class="c-select-menu" data-id="<?= $id ?>">
    <div class="c-select-btn">
        <span class="c-select-btn-text"><?= $selected ?? $placeholder ?? 'Select' ?></span>
        <?php require PUBLIC_PATH . 'assets/icons/chevron-down.php' ?>
    </div>

    <input type="hidden" id="<?= $id ?>" name="<?= $id ?>" value="<?= $selected ?>"/>

    <?php if (isset($content)) : ?>
        <ul class="c-select-options c-select-hide">
            <?php foreach ($content as $item) : ?>
                <li id="c-select-option-<?= $item ?>" class="c-select-option">
                    <span class="c-select-option-text"><?= $item ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>