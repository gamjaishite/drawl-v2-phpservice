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
require_once __DIR__ . '/../Model/watchlist/WatchlistGetSelfRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';

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
        $request = new WatchlistsGetOneRequest();
        $request->uuid = $uuid;
        $request->page = $_GET["page"] ?? 1;

        $result = $this->watchlistService->findByUUID($request);
        if ($result == null) {
            View::redirect('/404');
        }
        View::render('watchlist/detail', [
            'title' => 'Watchlist',
            'styles' => [
                '/css/watchlist-detail.css',
            ],
            'data' => $result,
            'editable' => true,
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
        $request = new WatchlistsGetSelfRequest();
        $request->visibility = $_GET["visibility"] ?? "";

        $result = $this->watchlistService->findUserBookmarks($request);

        function posterCompare($element1, $element2)
        {
            return $element1["rank"] - $element2["rank"];
        }

        $watchlists = [];

        foreach ($result["items"] as $item) {
            $posters = json_decode($item["posters"], true);
            usort($posters, "posterCompare");
            $item["posters"] = $posters;

            array_push($watchlists, $item);
        }

        $result["items"] = $watchlists;

        View::render('watchlist/self', [
            'title' => 'My Watchlist',
            'description' => 'My watchlist',
            'styles' => [
                '/css/watchlist-self.css',
            ],
            'data' => [
                'visibility' => strtolower($_GET['visibility'] ?? 'all'),
                'watchlists' => $result
            ]
        ]);
    }
}

function pretty_dump($arr, $d = 1)
{
    if ($d == 1)
        echo "<pre>"; // HTML Only
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            for ($i = 0; $i < $d; $i++) {
                echo "\t";
            }
            if (is_array($v)) {
                echo $k . PHP_EOL;
                Pretty_Dump($v, $d + 1);
            } else {
                echo $k . "\t" . $v . PHP_EOL;
            }
        }
    }
    if ($d == 1)
        echo "</pre>"; // HTML Only
}