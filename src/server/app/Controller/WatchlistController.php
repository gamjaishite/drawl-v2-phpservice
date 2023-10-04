<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistItemRepository.php';

require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Service/WatchlistService.php';

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/WatchlistAddItemRequest.php';
require_once __DIR__ . '/../Model/WatchlistCreateRequest.php';


class WatchlistController
{
    private CatalogService $catalogService;
    private WatchlistService $watchlistService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $catalogRepository = new CatalogRepository($connection);
        $this->catalogService = new CatalogService($catalogRepository);

        $watchlistRepository = new WatchlistRepository($connection);
        $watchlistItemRepository = new WatchlistItemRepository($connection);
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository);
    }

    public function create(): void
    {
        View::render('watchlist/create', [
            'title' => 'Create Watchlist',
            'description' => 'Create new watchlist',
            'styles' => [
                '/css/watchlistCreate.css',
                '/css/components/watchlist/watchlistItem.css',
                '/css/components/modal/watchlistAddItem.css',
                '/css/components/modal/watchlistAddSearchItem.css',
            ],
            'js' => [
                '/js/watchlistCreate.js',
                '/js/components/modal/watchlistAddItem.js',
                '/js/components/watchlist/watchlistItem.js',
            ]
        ]);
    }

    public function createPost(): void
    {
        $request = new WatchlistCreateRequest();
        $request->title = $_POST["title"];
        $request->description = $_POST["description"];
        $request->visibility = $_POST["visibility"];
        $request->items = $_POST["item"] ?? [];

        try {
            $this->watchlistService->create($request);

            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('watchlist/create', [
                'title' => 'Create Watchlist',
                'description' => 'Create new watchlist',
                'error' => $exception->getMessage(),
                'data' => [
                    "title" => $request->title,
                    "description" => $request->description,
                    "visibility" => $request->visibility
                ],
                'styles' => [
                    '/css/watchlistCreate.css',
                    '/css/components/watchlist/watchlistItem.css',
                    '/css/components/modal/watchlistAddItem.css',
                    '/css/components/modal/watchlistAddSearchItem.css',
                ],
                'js' => [
                    '/js/watchlistCreate.js',
                    '/js/components/modal/watchlistAddItem.js',
                    '/js/components/watchlist/watchlistItem.js',
                ]
            ]);
        }
    }

    public function detail(string $uuid): void
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

    public function watchlistAddItem()
    {
        $request = new WatchlistAddItemRequest();
        $request->id = $_GET["id"];

        $response = $this->catalogService->findByUUID($request->id);
        if (isset($response)) {
            $id = $response->id;
            $title = $response->title;
            $poster = $response->poster;
            $uuid = $response->uuid;
            $category = $response->category;
            require __DIR__ . '/../View/components/watchlist/watchlistItem.php';
        }
    }

    public function self()
    {
        $watchlistItems = [];

        $watchlists = $this->watchlistService->findAll();
        // foreach ($watchlists['items'] as $key => $value) {
        //     foreach ($value as $key => $demvalue) {
        //         echo $demvalue;
        //     }
        // }

        for ($i = 0; $i < 8; $i++) {
            $watchlistItems[] = [
                'uuid' => 'tes',
                'title' => 'Sample Title',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'category' => 'Anime',
                'user' => [
                    'name' => 'Sample Username',
                    'id' => 1,
                ],
                'visibility' => "PRIVATE",
                'items' => [
                    [
                        'uuid' => 'Snowdrop',
                        'title' => 'Snowdrop',
                        'poster' => 'jihu-13.jpg',
                    ],
                    [
                        'uuid' => 'Snowdrop',
                        'title' => 'Snowdrop',
                        'poster' => 'jihu-7.jpg',
                    ],
                    [
                        'uuid' => 'Snowdrop',
                        'title' => 'Snowdrop',
                        'poster' => 'jihu-13.jpg',
                    ],
                    [
                        'uuid' => 'Snowdrop',
                        'title' => 'Snowdrop',
                        'poster' => 'jihu-7.jpg',
                    ],
                    [
                        'uuid' => 'Snowdrop',
                        'title' => 'Snowdrop',
                        'poster' => 'jihu-13.jpg',
                    ]
                ]
            ];
        }
        View::render('watchlist/self', [
            'title' => 'My Watchlist',
            'description' => 'My watchlist',
            'styles' => [
                '/css/watchlist-self.css',
            ],
            'data' => [
                'visibility' => strtolower($_GET['visibility']) ?? 'all',
                'watchlists' => $watchlists
            ]
        ]);
    }
}