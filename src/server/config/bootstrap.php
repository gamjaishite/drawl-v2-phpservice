<?php
require_once __DIR__ . '/../app/Utils/EnvLoader.php';

// Load env
(new EnvLoader(__DIR__ . '/../.env'))->load();
