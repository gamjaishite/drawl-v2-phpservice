<?php
require_once __DIR__ . "/../app/App/Router.php";
require_once __DIR__ . "/../app/Controller/HomeController.php";
require_once __DIR__ . "/../app/Controller/UserController.php";

// Register routes
// Home controllers
Router::add('GET', '/', HomeController::class, 'index', []);

// User controllers
Router::add('GET', '/signup', UserController::class, 'signUp', []);
Router::add('POST', '/signup', UserController::class, 'postSignUp', []);

// Execute
Router::run();
