<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

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

    public function detail(): void
    {
        View::render('watchlist/detail', [
            'title' => 'Watchlist',
            'styles' => [
                '/css/watchlist-detail.css',
            ],
            'data' => [
                'title' => 'Sample Title',
                'category' => 'Anime',
                'username' => 'Sample Username',
                'created_at' => '2023-09-28',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'catalogs' => [
                    'items' => [
                        [
                            'title' => 'Snowdrop',
                            'poster' => 'jihu-13.jpg',
                            'category' => 'ANIME',
                            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        ],
                        [
                            'title' => 'Snowdrop',
                            'poster' => 'jihu-7.jpg',
                            'category' => 'ANIME',
                            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        ],
                    ],
                    'currentPage' => 1,
                    'totalPage' => 5,
                ],
                'comments' => [
                    'items' => [
                        [
                            'is_user' => true,
                            'user_image' => 'jihu-7.jpg',
                            'user_name' => 'User Name',
                            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                            'created_at' => '2023-09-28',
                        ],
                        [
                            'is_user' => false,
                            'user_image' => 'Other jihu-7.',
                            'user_name' => 'Other User Name',
                            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                            'created_at' => '2023-09-28',
                        ],
                    ],
                    'currentPage' => 1,
                    'totalPage' => 5,
                ],
            ],
        ]);
    }
}
