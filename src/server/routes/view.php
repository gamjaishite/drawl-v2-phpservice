<?php
require_once __DIR__ . "/../app/App/Router.php";

require_once __DIR__ . "/../app/Controller/HomeController.php";
require_once __DIR__ . "/../app/Controller/UserController.php";
require_once __DIR__ . "/../app/Controller/CatalogController.php";
require_once __DIR__ . '/../app/Controller/WatchlistController.php';
require_once __DIR__ . '/../app/Controller/ErrorPageController.php';

require_once __DIR__ . '/../app/Middleware/UserAuthMiddleware.php';


// Register routes
// Home controllers
Router::add('GET', '/', HomeController::class, 'index', []);

// User controllers
Router::add('GET', '/signup', UserController::class, 'signUp', []);
Router::add('POST', '/signup', UserController::class, 'postSignUp', []);
Router::add('GET', '/signin', UserController::class, 'signIn', []);
Router::add('POST', '/signin', UserController::class, 'postSignIn', []);
Router::add('GET', '/editProfile', UserController::class, 'showEditProfile', []);

// Catalog controllers
Router::add('GET', '/catalog', CatalogController::class, 'index', []);
Router::add('GET', '/catalog/create', CatalogController::class, 'create', []);
Router::add('POST', '/catalog/create', CatalogController::class, 'postCreate', []);
Router::add('GET', '/catalog/([A-Za-z0-9]*)/edit', CatalogController::class, 'edit', []);
Router::add('POST', '/catalog/([A-Za-z0-9]*)/edit', CatalogController::class, 'postEdit', []);
Router::add('POST', '/catalog/([A-Za-z0-9]*)/delete', CatalogController::class, 'postDelete', []);
Router::add('GET', '/catalog/([A-Za-z0-9]*)', CatalogController::class, 'detail', []);
Router::add('GET', '/api/catalog', CatalogController::class, "search", []);

// Watchlist controllers
Router::add('GET', '/watchlist/create', WatchlistController::class, 'create', []);
Router::add("POST", "/watchlist/create", WatchlistController::class, "createPost", []);
Router::add('GET', '/watchlist/([A-Za-z0-9]*)', WatchlistController::class, 'detail', []);
Router::add("POST", "/api/watchlist/item", WatchlistController::class, 'watchlistAddItem', []);

Router::add('GET', '/404', ErrorPageController::class, 'fourohfour', []);
Router::add('GET', '/500', ErrorPageController::class, 'fivehundred', []);

Router::add('GET', '/profile/watchlist', WatchlistController::class, 'self', []);

// Execute
Router::run();