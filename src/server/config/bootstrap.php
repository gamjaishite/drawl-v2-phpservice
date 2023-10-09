<?php
define('PUBLIC_PATH', '/var/www/html/');
require_once __DIR__ . '/../app/Utils/EnvLoader.php';

// Load env
(new EnvLoader(__DIR__ . '/../.env'))->load();

if (!function_exists('validateQueryParams')) {
    function validateQueryParams($id, $content): ?string
    {
        if (!isset($content)) return null;
        if (isset($_GET[$id]) && in_array($_GET[$id], $content, TRUE)) {
            return $_GET[$id];
        }
        return null;
    }
}
