<div class="navbar">
    <div class="navbar-content">
        <div class="navbar-header">
            <div>
                <a href='/' class="brand__title">Drawl</a>
            </div>
            <button aria-label="Open Menu" id="navbar-toggle" class="navbar-toggle" aria-expanded="false"
                aria-controls="navbar-menu">
                <?php require PUBLIC_PATH . 'assets/icons/menu.php' ?>
            </button>
        </div>
        <div id="navbar-menu" class="navbar-menu collapsed" aria-labelledby="navbar-toggle">
            <a href="/" class="btn">Discover</a>
            <a href="/catalog" class="btn">Catalog</a>
            <?php if ($user == null): ?>
                <a href="/signin" class="btn">Sign In</a>
            <?php else: ?>
                <button id="profile-menu-toggle" class="profile-icon" aria-label="Open Profile Dropdown"
                    aria-expanded="false">
                    <?php require PUBLIC_PATH . 'assets/icons/user.php' ?>
                </button>
                <div id="profile-menu" class="profile-menu collapsed" aria-labelledby="profile-menu-toggle">
                    <a href="/profile" class="btn">Profile</a>
                    <a href="/profile/watchlist" class="btn">My Watchlist</a>
                    <a href="/profile/bookmark" class="btn">My Bookmark</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>