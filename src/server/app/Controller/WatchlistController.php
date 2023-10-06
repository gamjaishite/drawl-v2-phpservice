<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistItemRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';
require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';

require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Service/WatchlistService.php';
require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/WatchlistAddItemRequest.php';
require_once __DIR__ . '/../Model/WatchlistCreateRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneByUserRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistLikeRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistSaveRequest.php';

class WatchlistController
{
    private CatalogService $catalogService;
    private WatchlistService $watchlistService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $catalogRepository = new CatalogRepository($connection);
        $this->catalogService = new CatalogService($catalogRepository);

        $watchlistRepository = new WatchlistRepository($connection);
        $watchlistItemRepository = new WatchlistItemRepository($connection);
        $watchlistLikeRepository = new WatchlistLikeRepository($connection);
        $watchlistSaveRepository = new WatchlistSaveRepository($connection);
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository, $watchlistLikeRepository, $watchlistSaveRepository);

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
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
        ], $this->sessionService);
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
            ], $this->sessionService);
        }
    }

    public function detail(string $uuid): void
    {
        $user = $this->sessionService->current();
        $request = new WatchlistsGetOneRequest();
        $request->uuid = $uuid;
        $request->page = $_GET["page"] ?? 1;
        $request->userId = $user ? $user->id : null;

        $result = $this->watchlistService->findByUUID($request);
        if ($result == null) {
            View::redirect('/404');
        }

        View::render('watchlist/detail', [
            'title' => 'Watchlist',
            'styles' => [
                '/css/watchlist-detail.css',
            ],
            'data' => [
                'item' => $result,
                'userUUID' => $user ? $user->uuid : null
            ]
        ], $this->sessionService);
    }

    public function item(): void
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
        $user = $this->sessionService->current();

        $request = new WatchlistGetOneByUserRequest();
        $request->visibility = $_GET["visibility"] ?? "";
        $request->userId = $user->id;

        $result = $this->watchlistService->findByUser($request);

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

        View::render('profile/watchlist', [
            'title' => 'My Watchlist',
            'description' => 'My watchlist',
            'styles' => [
                '/css/watchlist-self.css',
            ],
            'data' => [
                'visibility' => strtolower($_GET['visibility'] ?? 'all'),
                'watchlists' => $result,
                'userUUID' => $user->uuid
            ]
        ], $this->sessionService);
    }

    public function like(): void
    {
        $user = $this->sessionService->current();

        if ($user == null) {
            http_response_code(400);
            $response = [
                "message" => "Please login before liking this watchlist.",
            ];
            $response = json_encode($response);
            echo $response;
            return;
        }

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $watchlistLikeRequest = new WatchlistLikeRequest();
        $watchlistLikeRequest->watchlistUUID = $data["watchlistUUID"] ?? "";
        $watchlistLikeRequest->userId = $user->id;

        try {
            $this->watchlistService->like($watchlistLikeRequest);
        } catch (ValidationException $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        }

    }

    public function bookmark(): void
    {
        $user = $this->sessionService->current();

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $watchlistSaveRequest = new WatchlistSaveRequest();
        $watchlistSaveRequest->watchlistUUID = $data["watchlistUUID"] ?? "";
        $watchlistSaveRequest->userId = $user->id;

        try {
            $this->watchlistService->bookmark($watchlistSaveRequest);
        } catch (ValidationException $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        }
    }
}