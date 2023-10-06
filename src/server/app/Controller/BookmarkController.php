<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';

require_once __DIR__ . '/../Service/BookmarkService.php';

require_once __DIR__ . '/../Model/bookmark/BookmarkGetSelfRequest.php';

class BookmarkController
{
    private BookmarkService $bookmarkService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $watchlistSaveRepository = new WatchlistSaveRepository($connection);
        $this->bookmarkService = new BookmarkService($watchlistSaveRepository);
    }

    public function self()
    {
        $page = $_GET['page'] ?? 1;
        $pageSize = $_GET['pageSize'] ?? 10;

        $request = new BookmarkGetSelfRequest();
        $request->page = $page;
        $request->pageSize = $pageSize;

        $result = $this->bookmarkService->findSelf($request);

        function posterCompare($element1, $element2)
        {
            return $element1["rank"] - $element2["rank"];
        }

        $bookmarks = [];

        foreach ($result["items"] as $item) {
            $posters = json_decode($item["posters"], true);
            usort($posters, "posterCompare");
            $item["posters"] = $posters;

            array_push($bookmarks, $item);
        }

        $result["items"] = $bookmarks;

        View::render('profile/bookmark', [
            'title' => 'Bookmark',
            'data' => [
                'bookmarks' => $result,
            ],
        ]);
    }
}