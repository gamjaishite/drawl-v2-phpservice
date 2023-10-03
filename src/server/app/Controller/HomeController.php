<?php
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';
require_once __DIR__ . '/../Repository/WatchlistRepository.php';

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
        $this->watchlistService = new WatchlistService($watchlistRepository, $watchlistItemRepository);
    }

    public function index(): void
    {
        // Get watchlists
        $request = new WatchlistsGetRequest();
        $request->category = $_GET["category"] ?? "";
        $request->tags = $_GET["tags"] ?? "";
        $request->sortBy = $_GET["sortBy"] ?? "";
        $request->order = $_GET["order"] ?? "";


        View::render('home/index', [
            'title' => 'Homepage',
            'styles' => [
                '/css/home.css',
            ],
            'js' => [
                '/js/home.js',
            ],
        ]);
    }
}
