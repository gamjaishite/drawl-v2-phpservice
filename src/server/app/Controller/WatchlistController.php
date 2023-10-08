<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/CatalogRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistItemRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';
require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';
require_once __DIR__ . '/../Repository/TagRepository.php';
require_once __DIR__ . '/../Repository/WatchlistTagRepository.php';

require_once __DIR__ . '/../Service/CatalogService.php';
require_once __DIR__ . '/../Service/WatchlistService.php';
require_once __DIR__ . '/../Service/SessionService.php';
require_once __DIR__ . '/../Service/TagService.php';

require_once __DIR__ . '/../Model/CatalogCreateRequest.php';
require_once __DIR__ . '/../Model/WatchlistAddItemRequest.php';
require_once __DIR__ . '/../Model/WatchlistCreateRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneByUserRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistLikeRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistSaveRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistEditRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistDeleteRequest.php';

class WatchlistController
{
    private CatalogService $catalogService;
    private WatchlistService $watchlistService;
    private SessionService $sessionService;
    private TagService $tagService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $catalogRepository = new CatalogRepository($connection);
        $this->catalogService = new CatalogService($catalogRepository);

        $watchlistRepository = new WatchlistRepository($connection);
        $watchlistItemRepository = new WatchlistItemRepository($connection);
        $watchlistLikeRepository = new WatchlistLikeRepository($connection);
        $watchlistSaveRepository = new WatchlistSaveRepository($connection);
        $watchlistTagRepository = new WatchlistTagRepository($connection);
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository, $watchlistLikeRepository, $watchlistSaveRepository, $watchlistTagRepository);

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);

        $tagRepository = new TagRepository($connection);
        $this->tagService = new TagService($tagRepository);
    }

    public function create(): void
    {
        $tags = $this->tagService->findAll();

        View::render('watchlist/createUpdate', [
            'title' => 'Create Watchlist',
            'description' => 'Create new watchlist',
            "data" => [
                "tags" => $tags["items"],
            ],
            'styles' => [
                '/css/watchlistCreate.css',
                '/css/components/watchlist/watchlistItem.css',
                '/css/components/modal/watchlistAddItem.css',
                '/css/components/modal/watchlistAddSearchItem.css',
            ],
            'js' => [
                '/js/watchlistCreate.js',
                '/js/watchlist/createUpdate.js',
                '/js/components/modal/watchlistAddItem.js',
                '/js/components/watchlist/watchlistItem.js',
            ]
        ], $this->sessionService);
    }

    public function postCreate(): void
    {
        $user = $this->sessionService->current();

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $tags = $this->tagService->findAll();

        $request = new WatchlistCreateRequest();
        $request->title = $data["title"];
        $request->description = $data["description"];
        $request->visibility = $data["visibility"];
        $request->items = $data["items"];
        $request->tags = $data["tags"];
        $request->userId = $user->id;
        $request->initialTags = $tags["items"];

        try {
            $this->watchlistService->create($request);

            $response = [
                "status" => 200,
                "message" => "Watchlist successfully created",
                "redirectTo" => "/profile/watchlist",
            ];

            print_r(json_encode($response));
        } catch (ValidationException $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => $exception->getMessage()
            ];

            print_r(json_encode($response));
        }
    }

    public function edit(string $uuid): void
    {
        $user = $this->sessionService->current();

        $tags = $this->tagService->findAll();

        $getRequest = new WatchlistsGetOneRequest();
        $getRequest->uuid = $uuid;
        $getRequest->page = 1;
        $getRequest->pageSize = 100;

        $watchlist = $this->watchlistService->findByUUID($getRequest);

        if ($watchlist == null || $user->uuid !== $watchlist["creator_uuid"]) {
            View::redirect("/404");
        }

        View::render('watchlist/createUpdate', [
            'title' => 'Edit Watchlist',
            'description' => 'Edit watchlist',
            'edit' => true,
            'data' => [
                "title" => $watchlist["title"],
                "description" => $watchlist["description"],
                "visibility" => $watchlist["visibility"],
                "catalogs" => $watchlist["catalogs"],
                "tagsSelected" => $watchlist["tags"],
                "tags" => $tags["items"],
            ],
            'styles' => [
                '/css/watchlistCreate.css',
                '/css/components/watchlist/watchlistItem.css',
                '/css/components/modal/watchlistAddItem.css',
                '/css/components/modal/watchlistAddSearchItem.css',
            ],
            'js' => [
                '/js/watchlistCreate.js',
                '/js/watchlist/createUpdate.js',
                '/js/components/modal/watchlistAddItem.js',
                '/js/components/watchlist/watchlistItem.js',
            ]
        ], $this->sessionService);
    }

    public function putEdit(): void
    {
        $user = $this->sessionService->current();

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $getRequest = new WatchlistsGetOneRequest();
        $getRequest->uuid = $data["watchlistUUID"];
        $getRequest->page = 1;
        $getRequest->pageSize = 100;

        $watchlist = $this->watchlistService->findByUUID($getRequest);

        if ($watchlist == null || $user->uuid !== $watchlist["creator_uuid"]) {
            http_response_code(400);

            $response = [
                "status" => 400,
                "message" => "Watchlist not found.",
            ];

            print_r(json_encode($response));
        }

        $tags = $this->tagService->findAll();

        $request = new WatchlistEditRequest();
        $request->watchlist = $watchlist;
        $request->userId = $user->id;
        $request->title = $data["title"];
        $request->description = $data["description"];
        $request->visibility = $data["visibility"];
        $request->items = $data["items"];
        $request->tags = $data["tags"];
        $request->initialTags = $tags["items"];

        try {
            $this->watchlistService->edit($request);

            $response = [
                "status" => 200,
                "message" => "Watchlist edited successfully",
                "redirectTo" => "/watchlist/{$watchlist["watchlist_uuid"]}",
            ];

            print_r(json_encode($response));
        } catch (Exception $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => "Internal server error. Please try again later."
            ];

            print_r(json_encode($response));
        }
    }

    public function detail(string $uuid): void
    {
        $user = $this->sessionService->current();
        $request = new WatchlistsGetOneRequest();
        $request->uuid = $uuid;
        $request->page = $_GET["page"] ?? 1;
        $request->userId = $user ? $user->id : -1;

        $result = $this->watchlistService->findByUUID($request);
        if ($result == null) {
            View::redirect('/404');
        }

        View::render('watchlist/detail', [
            'title' => 'Watchlist',
            'styles' => [
                '/css/watchlist-detail.css',
            ],
            'js' => [
                '/js/watchlist/detail.js',
                '/js/watchlist/delete.js',
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
            $tags = json_decode($item["tags"], true);
            $tags = array_filter($tags, function ($value) {
                return $value["id"] !== null;
            });
            usort($posters, "posterCompare");
            $item["posters"] = $posters;
            $item["tags"] = $tags;

            array_push($watchlists, $item);
        }

        $result["items"] = $watchlists;

        View::render('profile/watchlist', [
            'title' => 'My Watchlist',
            'description' => 'My watchlist',
            'styles' => [
                '/css/watchlist-self.css',
            ],
            'js' => [
                '/js/profile/watchlist.js',
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
            print_r(json_encode($response));
            return;
        }

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $watchlistLikeRequest = new WatchlistLikeRequest();
        $watchlistLikeRequest->watchlistUUID = $data["watchlistUUID"] ?? "";
        $watchlistLikeRequest->userId = $user->id;

        try {
            $this->watchlistService->like($watchlistLikeRequest);
            http_response_code(200);
            $response = [
                "status" => 200,
                "message" => "Success",
            ];

            print_r(json_encode($response));
        } catch (ValidationException $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => $exception->getMessage(),
            ];

            print_r(json_encode($response));
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
            http_response_code(200);

            $response = [
                "status" => 200,
                "message" => "Success",
            ];

            print_r(json_encode($response));
        } catch (ValidationException $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => $exception->getMessage(),
            ];

            print_r(json_encode($response));
        }
    }

    public function delete()
    {
        $user = $this->sessionService->current();

        $dataRaw = file_get_contents("php://input");
        $data = json_decode($dataRaw, true);

        $getRequest = new WatchlistsGetOneRequest();
        $getRequest->uuid = $data["watchlistUUID"];
        $getRequest->page = 1;
        $getRequest->pageSize = 100;

        $watchlist = $this->watchlistService->findByUUID($getRequest);

        if ($watchlist == null || $user->uuid !== $watchlist["creator_uuid"]) {
            http_response_code(400);

            $response = [
                "status" => 400,
                "message" => "Watchlist not found.",
            ];

            print_r(json_encode($response));
        }

        $request = new WatchlistDeleteRequest();
        $request->watchlistUUID = $data["watchlistUUID"];

        try {
            $this->watchlistService->deleteByUUID($request);

            $response = [
                "status" => 200,
                "message" => "Watchlist deleted successfully",
                "redirectTo" => "/profile/watchlist",
            ];

            print_r(json_encode($response));
        } catch (Exception $exception) {
            http_response_code(500);

            $response = [
                "status" => 500,
                "message" => "Failed to delete watchlist. " . $exception->getMessage(),
            ];

            print_r(json_encode($response));
        }
    }
}