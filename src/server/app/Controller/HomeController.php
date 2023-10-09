<?php
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Service/SessionService.php';
require_once __DIR__ . '/../Service/TagService.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';
require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';
require_once __DIR__ . '/../Repository/WatchlistTagRepository.php';

require_once __DIR__ . '/../Model/WatchlistsGetRequest.php';

class HomeController
{
    private SessionService $sessionService;
    private WatchlistService $watchlistService;
    private TagService $tagService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);

        $watchlistRepository = new WatchlistRepository($connection);
        $watchlistItemRepository = new WatchlistItemRepository($connection);
        $watchlistLikeRepository = new WatchlistLikeRepository($connection);
        $watchlistSaveRepository = new WatchlistSaveRepository($connection);
        $watchlistTagRepository = new WatchlistTagRepository($connection);
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository, $watchlistLikeRepository, $watchlistSaveRepository, $watchlistTagRepository);

        $tagRepository = new TagRepository($connection);
        $this->tagService = new TagService($tagRepository);
    }

    public function index(): void
    {
        $data = $this->getWatchlist();

        View::render('home/index', [
            'title' => 'Homepage',
            'data' => $data,
            'styles' => [
                '/css/home.css',
            ],
            'js' => [
                '/js/home.js',
            ],
        ], $this->sessionService);
    }

    public function watchlists(): void
    {
        $data = $this->getWatchlist();

        foreach ($data["items"] as $item) {
            $uuid = $item["watchlist_uuid"];
            $posters = $item["posters"];
            $visibility = $item["visibility"];
            $title = $item["title"];
            $category = $item["category"];
            $creator = $item["creator"];
            $createdAt = $item["created_at"];
            $description = $item["description"];
            $itemCount = $item["item_count"];
            $loveCount = $item["love_count"];
            $loved = $item["loved"];
            $saved = $item["saved"];
            $self = ($data["userUUID"] == $item["creator_uuid"]);
            $userUUID = $data["userUUID"];

            require __DIR__ . '/../View/components/card/watchlistCard.php';
        }

        if (count($data["items"]) > 0) {
            $currentPage = $data["page"];
            $totalPage = $data["pageTotal"];
            require __DIR__ . '/../View/components/pagination.php';
        }
    }

    private function getWatchlist(): array
    {
        // Get current user
        $user = $this->sessionService->current();

        $tags = $this->tagService->findAll();
        $tagsInit = [];

        foreach ($tags["items"] as $tag) {
            array_push($tagsInit, $tag->name);
        }

        // Get watchlists
        $request = new WatchlistsGetRequest();
        $request->category = $_GET["category"] ?? "";
        $request->tags = $_GET["tags"] ?? "";
        $request->sortBy = $_GET["sortBy"] ?? "";
        $request->order = $_GET["order"] ?? "";
        $request->page = $_GET["page"] ?? 1;
        $request->tag = $_GET["tag"] ?? "";
        $request->tagsInit = $tagsInit;
        $request->search = isset($_GET["search"]) ? strtolower($_GET["search"]) : "";
        $request->userId = $user->id ?? -1;


        $result = $this->watchlistService->findAll($request);

        function posterCompare($element1, $element2)
        {
            return $element1["rank"] - $element2["rank"];
        }

        $data = [
            "items" => [],
            "page" => $result["page"],
            "pageTotal" => $result["pageTotal"],
            "userUUID" => $user->uuid ?? "",
            "tags" => $tagsInit
        ];

        foreach ($result["items"] as $item) {
            $posters = json_decode($item["posters"], true);
            $tags = json_decode($item["tags"], true);
            $tags = array_filter($tags, function ($value) {
                return $value["id"] !== null;
            });
            usort($posters, "posterCompare");
            $item["posters"] = $posters;
            $item["tags"] = $tags;

            array_push($data["items"], $item);
        }

        return $data;
    }
}
