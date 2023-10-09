<!-- 
Attributes:
- triggerClasses
- triggerText
- triggerIcon
- ariaLabel
- title
- content
- data
-->

<div class="modal">
    <button aria-label="<?= $ariaLabel ?? "" ?>" type="button" class="modal__trigger <?= $triggerClasses ?>"
        <?php foreach ($data ?? []

        as $key => $val): ?>
            data-<?= $key ?>>="<?= $val ?>"
        <?php endforeach; ?>
        >
        <?php
        if (isset($triggerIcon) && $triggerIcon != '') {
            require PUBLIC_PATH . 'assets/icons/' . $triggerIcon . '.php';
        }
        ?>
        <?= $triggerText ?? "" ?>
    </button>

    <div id="modal__content" class="modal__backdrop">
        <div class="modal__content">
            <h2><?= $title ?></h2>
            <button class="modal__close btn-ghost">
                <?php require PUBLIC_PATH . 'assets/icons/x.php' ?>
            </button>

            <?php
            if (isset($content)) {
                require __DIR__ . '/../components/modal/' . $content . '.php';
            }
            ?>
        </div>
    </div>
</div>