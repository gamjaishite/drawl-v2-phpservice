<?php

require_once __DIR__ . '/../App/Controller.php';
require_once __DIR__ . '/../App/View.php';

class HomeController
{
    public function index(): void
    {
        View::render('home/index', [
            'title' => 'Drawl | Homepage',
            'styles' => [
                './css/home.css',
            ],
        ]);
    }
}
