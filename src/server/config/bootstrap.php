<?php
define('PUBLIC_PATH', '/var/www/html/');
require_once __DIR__ . '/../app/Utils/EnvLoader.php';

// Load env
(new EnvLoader(__DIR__ . '/../.env'))->load();
