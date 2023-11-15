<?php
require_once __DIR__ . "/../app/App/Router.php";

require_once __DIR__ . "/../app/Controller/HomeController.php";
require_once __DIR__ . "/../app/Controller/UserController.php";
require_once __DIR__ . "/../app/Controller/CatalogController.php";
require_once __DIR__ . '/../app/Controller/WatchlistController.php';
require_once __DIR__ . '/../app/Controller/ErrorPageController.php';
require_once __DIR__ . '/../app/Controller/BookmarkController.php';

require_once __DIR__ . '/../app/Middleware/UserAuthMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AuthPageMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AdminAuthMiddleware.php';
require_once __DIR__ . '/../app/Middleware/UserAuthApiMiddleware.php';
require_once __DIR__ . '/../app/Middleware/AdminAuthApiMiddleware.php';
require_once __DIR__ . '/../app/Middleware/ExtUserAuthMiddleware.php';


// Register routes
// Home controllers
Router::add('GET', '/', HomeController::class, 'index', []);
Router::add("GET", "/api/watchlists", HomeController::class, 'watchlists', []);

// User controllers
Router::add('GET', '/signup', UserController::class, 'signUp', [AuthPageMiddleware::class]);
Router::add('POST', '/signup', UserController::class, 'postSignUp', []);
Router::add('GET', '/signin', UserController::class, 'signIn', [AuthPageMiddleware::class]);
Router::add('POST', '/signin', UserController::class, 'postSignIn', []);

Router::add('POST', '/api/auth/logout', UserController::class, 'logout', [UserAuthMiddleware::class]);
Router::add('DELETE', '/api/auth/delete', UserController::class, 'delete', [UserAuthMiddleware::class]);
Router::add('PUT', '/api/auth/update', UserController::class, 'update', [UserAuthMiddleware::class]);

Router::add('POST', '/api/v2/auth/signin', UserController::class, 'signInV2', []);
Router::add('GET', '/api/v2/auth/user', UserController::class, 'getUserInfo', [ExtUserAuthMiddleware::class]);


// Catalog controllers
Router::add('GET', '/catalog', CatalogController::class, 'index', []);
Router::add('GET', '/catalog/create', CatalogController::class, 'create', [AdminAuthMiddleware::class]);
Router::add('GET', '/catalog/request', CatalogController::class, 'request', [UserAuthMiddleware::class]);
Router::add('GET', '/catalog/([A-Za-z0-9\-]*)', CatalogController::class, 'detail', []);
Router::add('GET', '/catalog/([A-Za-z0-9\-]*)/edit', CatalogController::class, 'edit', [AdminAuthMiddleware::class]);

Router::add('POST', '/api/catalog/create', CatalogController::class, 'postCreate', [AdminAuthMiddleware::class]);
Router::add('POST', '/api/catalog/request', CatalogController::class, 'createCatalogRequest', [UserAuthMiddleware::class]);
Router::add('GET', '/api/catalog', CatalogController::class, "search", [UserAuthApiMiddleware::class]);
Router::add("DELETE", "/api/catalog/([A-Za-z0-9\-]*)/delete", CatalogController::class, "delete", [AdminAuthMiddleware::class]);
Router::add("POST", '/api/catalog/([A-Za-z0-9\-]*)/update', CatalogController::class, 'update', [AdminAuthMiddleware::class]);

Router::add("POST", "/api/v2/catalog-request", CatalogController::class, "createCatalogRequest", []);
Router::add("POST", "/api/v2/catalog", CatalogController::class, "catalogRequestCallback", []);
Router::add("GET", "/api/v2/catalogs", CatalogController::class, "getCatalogs", []);
Router::add("GET", "/api/v2/catalog/([A-Za-z0-9\-]*)", CatalogController::class, "getCatalogByUUID", []);


// Watchlist controllers
Router::add("GET", "/watchlist/create", WatchlistController::class, 'create', [UserAuthMiddleware::class]);
Router::add("GET", "/watchlist/([A-Za-z0-9]*)/edit", WatchlistController::class, "edit", [UserAuthMiddleware::class]);
Router::add("GET", "/watchlist/([A-Za-z0-9]*)", WatchlistController::class, 'detail', []);

Router::add("POST", "/api/watchlist", WatchlistController::class, "postCreate", [UserAuthApiMiddleware::class]);
Router::add("PUT", "/api/watchlist", WatchlistController::class, "putEdit", [UserAuthApiMiddleware::class]);
Router::add("DELETE", "/api/watchlist", WatchlistController::class, "delete", [UserAuthApiMiddleware::class]);
Router::add("GET", "/api/watchlist/item", WatchlistController::class, 'item', [UserAuthApiMiddleware::class]);
Router::add("POST", "/api/watchlist/like", WatchlistController::class, "like", [UserAuthApiMiddleware::class]);
Router::add("POST", "/api/watchlist/save", WatchlistController::class, "bookmark", [UserAuthApiMiddleware::class]);

// Profile
Router::add('GET', '/profile', UserController::class, 'showEditProfile', [UserAuthMiddleware::class]);
Router::add('GET', '/profile/bookmark', BookmarkController::class, 'self', [UserAuthMiddleware::class]);
Router::add('GET', '/profile/watchlist', WatchlistController::class, 'self', [UserAuthMiddleware::class]);

// Error page
Router::add('GET', '/404', ErrorPageController::class, 'fourohfour', []);
Router::add('GET', '/500', ErrorPageController::class, 'fivehundred', []);

// Execute
Router::run();