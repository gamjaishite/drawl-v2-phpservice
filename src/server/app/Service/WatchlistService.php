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
require_once __DIR__ . '/../Model/watchlist/WatchlistSaveRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistEditRequest.php';

class WatchlistService
{
    private WatchlistRepository $watchlistRepository;
    private WatchlistItemRepository $watchlistItemRepository;
    private WatchlistLikeRepository $watchlistLikeRepository;
    private WatchlistSaveRepository $watchlistSaveRepository;

    public function __construct(WatchlistRepository $watchlistRepository, WatchlistItemRepository $watchlistItemRepository, WatchlistLikeRepository $watchlistLikeRepository, WatchlistSaveRepository $watchlistSaveRepository)
    {
        $this->watchlistRepository = $watchlistRepository;
        $this->watchlistItemRepository = $watchlistItemRepository;
        $this->watchlistLikeRepository = $watchlistLikeRepository;
        $this->watchlistSaveRepository = $watchlistSaveRepository;
    }


    public function create(WatchlistCreateRequest $watchlistCreateRequest)
    {
        $this->validateWatchlistCreateEditRequest($watchlistCreateRequest);

        try {
            Database::beginTransaction();

            // Create watchlist
            $watchlist = new Watchlist();
            $watchlist->uuid = UUIDGenerator::uuid4();
            $watchlist->title = $watchlistCreateRequest->title;
            $watchlist->description = $watchlistCreateRequest->description;
            $watchlist->visibility = $watchlistCreateRequest->visibility;
            $watchlist->category = "DRAMA";
            $watchlist->userId = $watchlistCreateRequest->userId;
            $watchlist->itemCount = count($watchlistCreateRequest->items);

            // check watchlist category by travers through items
            $cntDrama = 0;
            $cntAnime = 0;
            foreach ($watchlistCreateRequest->items as $item) {
                if ($item["category"] == "ANIME")
                    $cntAnime++;
                if ($item["category"] == "DRAMA")
                    $cntDrama++;
            }

            if ($cntDrama != 0 && $cntAnime != 0)
                $watchlist->category = "MIXED";
            else if ($cntAnime != 0)
                $watchlist->category = "ANIME";

            $watchlistNew = $this->watchlistRepository->save($watchlist);

            // save the items
            $currRank = 1;
            foreach ($watchlistCreateRequest->items as $item) {
                $watchlist_item = new WatchlistItem();
                $watchlist_item->uuid = UUIDGenerator::uuid4();
                $watchlist_item->rank = $currRank;
                $watchlist_item->description = $item["description"];
                $watchlist_item->watchlistId = $watchlistNew->id;
                $watchlist_item->catalogId = $item["id"];

                $this->watchlistItemRepository->save($watchlist_item);

                $currRank++;
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function edit(WatchlistEditRequest $watchlistEditRequest)
    {
        $this->validateWatchlistCreateEditRequest($watchlistEditRequest);

        try {
            Database::beginTransaction();

            // Create watchlist
            $watchlist = new Watchlist();
            $watchlist->id = $watchlistEditRequest->watchlist["watchlist_id"];
            $watchlist->uuid = $watchlistEditRequest->watchlist["watchlist_uuid"];
            $watchlist->title = $watchlistEditRequest->title;
            $watchlist->description = $watchlistEditRequest->description;
            $watchlist->visibility = $watchlistEditRequest->visibility;
            $watchlist->category = "DRAMA";
            $watchlist->userId = $watchlistEditRequest->userId;
            $watchlist->itemCount = count($watchlistEditRequest->items);

            // check watchlist category by travers through items
            $cntDrama = 0;
            $cntAnime = 0;
            foreach ($watchlistEditRequest->items as $item) {
                if ($item["category"] == "ANIME")
                    $cntAnime++;
                if ($item["category"] == "DRAMA")
                    $cntDrama++;
            }

            if ($cntDrama != 0 && $cntAnime != 0)
                $watchlist->category = "MIXED";
            else if ($cntAnime != 0)
                $watchlist->category = "ANIME";

            $watchlistNew = $this->watchlistRepository->update($watchlist);

            // delete all items with corresponding watchlistId
            $this->watchlistItemRepository->deleteBy("watchlist_id", $watchlistNew->id);

            // save the items
            $currRank = 1;
            foreach ($watchlistEditRequest->items as $item) {
                $watchlist_item = new WatchlistItem();
                $watchlist_item->uuid = UUIDGenerator::uuid4();
                $watchlist_item->rank = $currRank;
                $watchlist_item->description = $item["description"];
                $watchlist_item->watchlistId = $watchlistNew->id;
                $watchlist_item->catalogId = $item["id"];

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
            $watchlistsGetRequest->sortBy = "w.created_at";

        $result = $this->watchlistRepository->findAllCustom($watchlistsGetRequest->userId, $watchlistsGetRequest->search, $watchlistsGetRequest->category, $watchlistsGetRequest->sortBy, $watchlistsGetRequest->order, $watchlistsGetRequest->page, 2);
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

    public function findByUUID(WatchlistsGetOneRequest $request)
    {
        $result = $this->watchlistRepository->findByUUID($request->uuid, null, $request->page, $request->pageSize);
        return $result;
    }


    public function like(WatchlistLikeRequest $watchlistLikeRequest): void
    {
        $this->validateWatchlistLikeAndSaveRequest($watchlistLikeRequest);

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

    public function bookmark(WatchlistSaveRequest $watchlistSaveRequest): void
    {
        $this->validateWatchlistLikeAndSaveRequest($watchlistSaveRequest);

        try {
            Database::beginTransaction();

            // 1. Get watchlist by UUID
            $watchlist = $this->watchlistRepository->findOne("uuid", $watchlistSaveRequest->watchlistUUID, ["id"]);
            if ($watchlist == null) {
                throw new ValidationException("Watchlist not found.");
            }

            // 2. Insert or delete a row from watchlist_save table
            $watchlistLike = $this->watchlistSaveRepository->findOneByWatchlistAndUser($watchlist->id, $watchlistSaveRequest->userId);
            if ($watchlistLike == null) {
                $this->watchlistSaveRepository->saveByWatchlistAndUser($watchlist->id, $watchlistSaveRequest->userId);
            } else {
                $this->watchlistSaveRepository->deleteByWatchlistAndUser($watchlist->id, $watchlistSaveRequest->userId);
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateWatchlistCreateEditRequest(WatchlistCreateRequest|WatchlistEditRequest $watchlistCreateUpdateRequest)
    {
        if (!isset($watchlistCreateUpdateRequest->title) || trim($watchlistCreateUpdateRequest->title) == "") {
            throw new ValidationException("Title is required");
        }
        if (!isset($watchlistCreateUpdateRequest->visibility) || !in_array($watchlistCreateUpdateRequest->visibility, ["PUBLIC", "PRIVATE"])) {
            throw new ValidationException("Invalid visibility");
        }
        if (isset($watchlistCreateUpdateRequest->description) && strlen($watchlistCreateUpdateRequest->description) > 255) {
            throw new ValidationException("Description is too long. Maximum 255 characters");
        }
        if (!isset($watchlistCreateUpdateRequest->items) || count($watchlistCreateUpdateRequest->items) == 0) {
            throw new ValidationException("Watchlist must contain 1 item");
        }
        if (count($watchlistCreateUpdateRequest->items) > 50) {
            throw new ValidationException("Watchlist contains maximum 50 items");
        }

        foreach ($watchlistCreateUpdateRequest->items ?? [] as $item) {
            if (strlen($item["description"]) > 255) {
                throw new ValidationException("Item description for" . $item["title"] . "is too long. Maximum 255 characters.");
            }
        }
    }

    private function validateWatchlistLikeAndSaveRequest(WatchlistLikeRequest|WatchlistSaveRequest $watchlistRequest)
    {
        if (!isset($watchlistRequest->watchlistUUID) || trim($watchlistRequest->watchlistUUID) == "") {
            throw new ValidationException("Watchlist UUID is required");
        }
    }
}