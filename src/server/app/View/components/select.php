<!-- Attributes
- id
- placeholder
- contents
-->

<div class="c-select-menu">
    <div class="c-select-btn">
        <span class="c-select-btn-text"><?= $placeholder ?? 'Select' ?></span>
        <?php require  PUBLIC_PATH . 'assets/icons/chevron-down.php' ?>
    </div>

    <input type="hidden" />

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