<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';

require_once __DIR__ . '/../Service/BookmarkService.php';
require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Model/bookmark/BookmarkGetRequest.php';

class BookmarkController
{
    private BookmarkService $bookmarkService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $watchlistSaveRepository = new WatchlistSaveRepository($connection);
        $this->bookmarkService = new BookmarkService($watchlistSaveRepository);

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function self()
    {
        $page = $_GET['page'] ?? 1;
        $pageSize = $_GET['pageSize'] ?? 10;

        $user = $this->sessionService->current();
        $request = new BookmarkGetRequest();
        $request->userId = $user ? $user->id : null;
        $request->page = $page;
        $request->pageSize = $pageSize;

        $result = $this->bookmarkService->findByUser($request);

        function posterCompare($element1, $element2)
        {
            return $element1["rank"] - $element2["rank"];
        }

        $bookmarks = [];

        foreach ($result["items"] as $item) {
            $posters = json_decode($item["posters"], true);
            $tags = json_decode($item["tags"], true);
            $tags = array_filter($tags, function ($value) {
                return $value["id"] !== null;
            });
            usort($posters, "posterCompare");
            $item["posters"] = $posters;
            $item["tags"] = $tags;

            array_push($bookmarks, $item);
        }

        $result["items"] = $bookmarks;

        View::render('profile/bookmark', [
            'title' => 'Bookmark',
            'data' => [
                'bookmarks' => $result,
                'userUUID' => $user ? $user->uuid : null,
            ],
        ], $this->sessionService);
    }
}