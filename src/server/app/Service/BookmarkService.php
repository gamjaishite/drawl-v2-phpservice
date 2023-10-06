<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Domain/WatchlistSave.php';

require_once __DIR__ . '/../Repository/WatchlistSaveRepository.php';

require_once __DIR__ . '/../Model/bookmark/BookmarkGetSelfRequest.php';

class BookmarkService
{
    private WatchlistSaveRepository $watchlistSaveRepository;

    public function __construct(WatchlistSaveRepository $watchlistSaveRepository)
    {
        $this->watchlistSaveRepository = $watchlistSaveRepository;
    }

    public function findSelf(BookmarkGetSelfRequest $request)
    {
        $result = $this->watchlistSaveRepository->findByUser(2, $request->page, $request->pageSize);
        return $result;
    }
}