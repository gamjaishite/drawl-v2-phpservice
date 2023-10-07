<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Domain/WatchlistSave.php';

require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';

require_once __DIR__ . '/../Model/bookmark/BookmarkGetRequest.php';

class BookmarkService
{
    private WatchlistSaveRepository $watchlistSaveRepository;

    public function __construct(WatchlistSaveRepository $watchlistSaveRepository)
    {
        $this->watchlistSaveRepository = $watchlistSaveRepository;
    }

    public function findByUser(BookmarkGetRequest $request)
    {
        $result = $this->watchlistSaveRepository->findByUser($request->userId, $request->page, $request->pageSize);
        return $result;
    }
}