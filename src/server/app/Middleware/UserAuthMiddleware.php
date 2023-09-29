<?php

require_once __DIR__ . '/../App/Middleware.php';

class UserAuthMiddleware implements Middleware
{
    public function run(): void
    {
        // redirect to login page
        header("Location: /signin");
        exit();
    }
}
