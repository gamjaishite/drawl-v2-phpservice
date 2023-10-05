<?php
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';

require_once __DIR__ . '/../Model/WatchlistsGetRequest.php';

class HomeController
{
    private SessionService $sessionService;
    private WatchlistService $watchlistService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);

        $watchlistRepository = new WatchlistRepository($connection);
        $watchlistItemRepository = new WatchlistItemRepository($connection);
        $watchlistLikeRepository = new WatchlistLikeRepository($connection);
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository, $watchlistLikeRepository);
    }

    public function index(): void
    {
        // Get watchlists
        $ajax = isset($_GET["ajax"]) && (($_GET["ajax"] == "true" ?? false));

        $request = new WatchlistsGetRequest();
        $request->category = $_GET["category"] ?? "";
        $request->tags = $_GET["tags"] ?? "";
        $request->sortBy = $_GET["sortBy"] ?? "";
        $request->order = $_GET["order"] ?? "";
        $request->page = $_GET["page"] ?? 1;
        $request->search = isset($_GET["search"]) ? strtolower($_GET["search"]) : "";


        $result = $this->watchlistService->findAll($request);

        function posterCompare($element1, $element2)
        {
            return $element1["rank"] - $element2["rank"];
        }

        $data = [
            "items" => [],
            "page" => $result["page"],
            "pageTotal" => $result["pageTotal"]
        ];

        foreach ($result["items"] as $item) {
            $posters = json_decode($item["posters"], true);
            usort($posters, "posterCompare");
            $item["posters"] = $posters;

            array_push($data["items"], $item);
        }

        if ($ajax) {
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

                require __DIR__ . '/../View/components/card/watchlistCard.php';
            }

            if (count($data["items"]) > 0) {
                $currentPage = $data["page"];
                $totalPage = $data["pageTotal"];
                require __DIR__ . '/../View/components/pagination.php';
            }
            return;
        }


        View::render('home/index', [
            'title' => 'Homepage',
            'data' => $data,
            'styles' => [
                '/css/home.css',
            ],
            'js' => [
                '/js/home.js',
            ],
        ]);
    }
}
