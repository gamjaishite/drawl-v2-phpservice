<?php
require_once __DIR__ . "/../app/App/Router.php";

require_once __DIR__ . "/../app/Controller/HomeController.php";
require_once __DIR__ . "/../app/Controller/UserController.php";
require_once __DIR__ . "/../app/Controller/CatalogController.php";
require_once __DIR__ . '/../app/Controller/WatchlistController.php';
require_once __DIR__ . '/../app/Controller/ErrorPageController.php';
require_once __DIR__ . '/../app/Controller/BookmarkController.php';

require_once __DIR__ . '/../app/Middleware/UserAuthMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AdminAuthMiddleware.php';
require_once __DIR__ . '/../app/Middleware/UserAuthApiMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AdminAuthApiMiddleware.php';


// Register routes
// Home controllers
Router::add('GET', '/', HomeController::class, 'index', []);
Router::add("GET", "/api/watchlists", HomeController::class, 'watchlists', []);

// User controllers
Router::add('GET', '/signup', UserController::class, 'signUp', []);
Router::add('POST', '/signup', UserController::class, 'postSignUp', []);
Router::add('GET', '/signin', UserController::class, 'signIn', []);
Router::add('POST', '/signin', UserController::class, 'postSignIn', []);
Router::add('POST', '/editProfile', UserController::class, 'postEditProfile', [UserAuthMiddleware::class]);
Router::add('POST', '/api/auth/logout', UserController::class, 'logout', [UserAuthMiddleware::class]);

// Catalog controllers
Router::add('GET', '/catalog', CatalogController::class, 'index', []);
Router::add('GET', '/catalog/create', CatalogController::class, 'create', [AdminAuthMiddleware::class]);
Router::add('POST', '/catalog/create', CatalogController::class, 'postCreate', [AdminAuthMiddleware::class]);
Router::add('GET', '/catalog/([A-Za-z0-9]*)/edit', CatalogController::class, 'edit', [AdminAuthMiddleware::class]);
Router::add('POST', '/catalog/([A-Za-z0-9]*)/edit', CatalogController::class, 'postEdit', [AdminAuthMiddleware::class]);
Router::add('POST', '/catalog/([A-Za-z0-9]*)/delete', CatalogController::class, 'postDelete', [AdminAuthMiddleware::class]);
Router::add('GET', '/catalog/([A-Za-z0-9]*)', CatalogController::class, 'detail', []);
Router::add('GET', '/api/catalog', CatalogController::class, "search", [UserAuthApiMiddleware::class]);
Router::add("DELETE", "/api/catalog/([A-Za-z0-9]*)/delete", CatalogController::class, "delete", [AdminAuthMiddleware::class]);
Router::add("POST", '/api/catalog/([A-Za-z0-9]*)/update', CatalogController::class, 'update', [AdminAuthMiddleware::class]);

// Watchlist controllers
Router::add("GET", "/watchlist/create", WatchlistController::class, 'create', [UserAuthMiddleware::class]);
Router::add("POST", "/watchlist/create", WatchlistController::class, "createPost", [UserAuthMiddleware::class]);
Router::add("GET", "/watchlist/([A-Za-z0-9]*)", WatchlistController::class, 'detail', []);

Router::add("GET", "/api/watchlist/item", WatchlistController::class, 'item', [UserAuthApiMiddleware::class]);
Router::add("POST", "/api/watchlist/like", WatchlistController::class, "like", [UserAuthApiMiddleware::class]);
Router::add("POST", "/api/watchlist/save", WatchlistController::class, "bookmark", [UserAuthApiMiddleware::class]);

Router::add('GET', '/404', ErrorPageController::class, 'fourohfour', []);
Router::add('GET', '/500', ErrorPageController::class, 'fivehundred', []);

Router::add('GET', '/profile', UserController::class, 'showEditProfile', [UserAuthMiddleware::class]);
Router::add('GET', '/profile/bookmark', BookmarkController::class, 'self', [UserAuthMiddleware::class]);
Router::add('GET', '/profile/watchlist', WatchlistController::class, 'self', [UserAuthMiddleware::class]);

// Execute
Router::run();