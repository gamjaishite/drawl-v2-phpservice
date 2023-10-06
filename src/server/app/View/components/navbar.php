<div class="navbar">
    <div class="navbar-content">
        <div class="navbar-header">
            <div>
                <a href='/' class="brand__title">Drawl</a>
            </div>
            <button aria-label="Open Menu" id="navbar-toggle" class="navbar-toggle" aria-expanded="false" aria-controls="navbar-menu">
                <?php require PUBLIC_PATH . 'assets/icons/menu.php' ?>
            </button>
        </div>
        <div id="navbar-menu" class="navbar-menu collapsed" aria-labelledby="navbar-toggle">
            <a href="/" class="btn">Discover</a>
            <a href="/catalog" class="btn">Catalog</a>
            <a href="/profile/watchlist" class="btn">My Watch List</a>
            <?php if ($user == null) : ?>
                <a href="/signin" class="btn">Sign In</a>
            <?php else : ?>
                <a href="/editProfile" class="btn"><?= $user->name ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>