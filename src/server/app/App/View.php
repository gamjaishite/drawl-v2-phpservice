<?php

class View
{
    public static function render(string $view, $model = [])
    {
        require __DIR__ . '/../View/components/header.php';
        if (!isset($_SERVER['PATH_INFO']) || ($_SERVER['PATH_INFO'] != '/signin' && $_SERVER['PATH_INFO'] != '/signup')) {
            require __DIR__ . '/../View/components/navbar.php';
        }
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/components/footer.php';
    }

    public static function redirect(string $url)
    {
        header("Location: $url");
        exit();
    }
}
