<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';

class WatchlistController
{
    public function __construct()
    {
        $connection = Database::getConnection();
    }

    public function create(): void
    {
        View::render('watchlist/create', [
            'title' => 'Drawl | Create Watchlist',
            'description' => 'Create new watchlist',
            'styles' => [
                '/css/watchlistCreate.css',
            ],
            'js' => [
                '/js/watchlistCreate.js',
            ]
        ]);
    }
}
