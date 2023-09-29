<?php
require_once __DIR__ . "/../app/App/Router.php";

require_once __DIR__ . "/../app/Controller/HomeController.php";
require_once __DIR__ . "/../app/Controller/UserController.php";
require_once __DIR__ . "/../app/Controller/CatalogController.php";
require_once __DIR__ . '/../app/Controller/WatchlistController.php';

require_once __DIR__ . '/../app/Middleware/UserAuthMiddleware.php';


// Register routes
// Home controllers
Router::add('GET', '/', HomeController::class, 'index', []);

// User controllers
Router::add('GET', '/signup', UserController::class, 'signUp', []);
Router::add('POST', '/signup', UserController::class, 'postSignUp', []);
Router::add('GET', '/signin', UserController::class, 'signIn', []);
Router::add('POST', '/signin', UserController::class, 'postSignIn', []);
Router::add('GET', '/profile', UserController::class, 'showProfile', []);
Router::add('GET', '/editProfile', UserController::class, 'showEditProfile', []);

// Catalog controllers
Router::add('GET', '/catalog', CatalogController::class, 'index', []);
Router::add('GET', '/catalog/create', CatalogController::class, 'create', []);
Router::add('POST', '/catalog/create', CatalogController::class, 'postCreate', []);
Router::add('GET', '/catalog/edit', CatalogController::class, 'edit', []);
Router::add('GET', '/catalog/([A-Za-z0-9]*)', CatalogController::class, 'detail', []);

// Watchlist controllers
Router::add('GET', '/watchlist/create', WatchlistController::class, 'create', []);
Router::add('GET', '/watchlist/([A-Za-z0-9]*)', WatchlistController::class, 'detail', []);

// Execute
Router::run();
