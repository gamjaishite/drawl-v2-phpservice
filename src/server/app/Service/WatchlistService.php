<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Domain/Watchlist.php';
require_once __DIR__ . '/../Domain/WatchlistItem.php';
require_once __DIR__ . '/../Domain/WatchlistLike.php';

require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistItemRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';

require_once __DIR__ . '/../Model/WatchlistsGetRequest.php';
require_once __DIR__ . '/../Model/WatchlistCreateRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetSelfRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistLikeRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';

class WatchlistService
{
    private WatchlistRepository $watchlistRepository;
    private WatchlistItemRepository $watchlistItemRepository;
    private WatchlistLikeRepository $watchlistLikeRepository;

    public function __construct(WatchlistRepository $watchlistRepository, WatchlistItemRepository $watchlistItemRepository, WatchlistLikeRepository $watchlistLikeRepository)
    {
        $this->watchlistRepository = $watchlistRepository;
        $this->watchlistItemRepository = $watchlistItemRepository;
        $this->watchlistLikeRepository = $watchlistLikeRepository;
    }


    public function create(WatchlistCreateRequest $watchlistCreateRequest)
    {
        $this->validateWatchlistCreateRequest($watchlistCreateRequest);

        try {
            Database::beginTransaction();

            // Create watchlist
            $watchlist = new Watchlist();
            $watchlist->uuid = UUIDGenerator::uuid4();
            $watchlist->title = $watchlistCreateRequest->title;
            $watchlist->description = $watchlistCreateRequest->description;
            $watchlist->visibility = $watchlistCreateRequest->visibility;
            $watchlist->category = "DRAMA";
            $watchlist->userId = 1;
            $watchlist->itemCount = count($watchlistCreateRequest->items);

            // check watchlist category by travers through items
            $cntDrama = 0;
            $cntAnime = 0;
            foreach ($watchlistCreateRequest->items as $key => $value) {
                $currCategory = explode("__", $key)[2];
                if ($currCategory == "ANIME")
                    $cntAnime++;
                if ($currCategory == "DRAMA")
                    $cntDrama++;
            }

            if ($cntDrama != 0 && $cntAnime != 0)
                $watchlist->category = "MIXED";
            else if ($cntAnime != 0)
                $watchlist->category = "ANIME";

            $watchlistNew = $this->watchlistRepository->save($watchlist);

            // save the items
            $currRank = 1;
            foreach ($watchlistCreateRequest->items as $key => $value) {
                $watchlist_item = new WatchlistItem();
                $watchlist_item->uuid = UUIDGenerator::uuid4();
                $watchlist_item->rank = $currRank;
                $watchlist_item->description = $value;
                $watchlist_item->watchlistId = $watchlistNew->id;
                $watchlist_item->catalogId = intval(explode("__", $key)[0]);

                $this->watchlistItemRepository->save($watchlist_item);

                $currRank++;
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function findAll(WatchlistsGetRequest $watchlistsGetRequest)
    {
        if (!in_array(strtoupper(trim($watchlistsGetRequest->category)), ["", "MIXED", "ANIME", "DRAMA"])) {
            $watchlistsGetRequest->category = "";
        }
        if (!isset($watchlistsGetRequest->order) || !in_array(strtoupper(trim($watchlistsGetRequest->order)), ["ASC", "DESC"])) {
            $watchlistsGetRequest->order = "DESC";
        }
        if (!isset($watchlistsGetRequest->sortBy) || !in_array(strtoupper(trim($watchlistsGetRequest->sortBy)), ["DATE", "LOVE"])) {
            $watchlistsGetRequest->sortBy = "LOVE";
        }

        if ($watchlistsGetRequest->sortBy == "LOVE")
            $watchlistsGetRequest->sortBy = "love_count";
        if ($watchlistsGetRequest->sortBy == "DATE")
            $watchlistsGetRequest->sortBy = "w.updated_at";

        $result = $this->watchlistRepository->findAllCustom(1, $watchlistsGetRequest->search, $watchlistsGetRequest->category, $watchlistsGetRequest->sortBy, $watchlistsGetRequest->order, $watchlistsGetRequest->page, 2);
        return $result;
    }

    public function findUserBookmarks(WatchlistsGetSelfRequest $request)
    {
        if (!isset($request->visibility) || !in_array(strtoupper(trim($request->visibility)), ["ALL", "PUBLIC", "PRIVATE"]) || strtoupper($request->visibility) == "ALL") {
            $request->visibility = "";
        }

        $result = $this->watchlistRepository->findUserBookmarks(1, strtoupper($request->visibility), 1, 10);
        return $result;
    }

    public function like(WatchlistLikeRequest $watchlistLikeRequest): void
    {
        $this->validateWatchlistLikeRequest($watchlistLikeRequest);

        try {
            Database::beginTransaction();

            // 1. Get watchlist by UUID
            $watchlist = $this->watchlistRepository->findOne("uuid", $watchlistLikeRequest->watchlistUUID, ["id"]);
            if ($watchlist == null) {
                throw new ValidationException("Watchlist not found.");
            }

            // 2. Insert or delete a row from watchlist_like table
            $watchlistLike = $this->watchlistLikeRepository->findOneByWatchlistAndUser($watchlist->id, $watchlistLikeRequest->userId);
            if ($watchlistLike == null) {
                $this->watchlistLikeRepository->saveByWatchlistAndUser($watchlist->id, $watchlistLikeRequest->userId);
            } else {
                $this->watchlistLikeRepository->deleteByWatchlistAndUser($watchlist->id, $watchlistLikeRequest->userId);
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function findByUUID(WatchlistsGetOneRequest $request)
    {
        $result = $this->watchlistRepository->findByUUID($request->uuid, null, $request->page, $request->pageSize);
        return $result;
    }

    private function validateWatchlistCreateRequest(WatchlistCreateRequest $watchlistCreateRequest)
    {
        if (!isset($watchlistCreateRequest->title) || trim($watchlistCreateRequest->title) == "") {
            throw new ValidationException("Title is required");
        }
        if (!isset($watchlistCreateRequest->visibility) || !in_array($watchlistCreateRequest->visibility, ["PUBLIC", "PRIVATE"])) {
            throw new ValidationException("Invalid visibility");
        }
        if (!isset($watchlistCreateRequest->items) || count($watchlistCreateRequest->items) == 0) {
            throw new ValidationException("Watchlist must contain 1 item");
        }
        if (count($watchlistCreateRequest->items) > 50) {
            throw new ValidationException("Watchlist contains maximum 50 items");
        }
    }

    private function validateWatchlistLikeRequest(WatchlistLikeRequest $watchlistLikeRequest)
    {
        if (!isset($watchlistLikeRequest->watchlistUUID) || trim($watchlistLikeRequest->watchlistUUID) == "") {
            throw new ValidationException("Watchlist UUID is required");
        }
    }
}