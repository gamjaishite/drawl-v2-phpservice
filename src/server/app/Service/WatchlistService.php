<?php
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Domain/Watchlist.php';
require_once __DIR__ . '/../Domain/WatchlistItem.php';
require_once __DIR__ . '/../Domain/WatchlistLike.php';
require_once __DIR__ . '/../Domain/WatchlistTag.php';

require_once __DIR__ . '/../Repository/WatchlistRepository.php';
require_once __DIR__ . '/../Repository/WatchlistItemRepository.php';
require_once __DIR__ . '/../Repository/WatchlistLikeRepository.php';
require_once __DIR__ . '/../Repository/WatchlistTagRepository.php';

require_once __DIR__ . '/../Model/WatchlistsGetRequest.php';
require_once __DIR__ . '/../Model/WatchlistCreateRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneByUserRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistLikeRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistSaveRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistGetOneRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistEditRequest.php';
require_once __DIR__ . '/../Model/watchlist/WatchlistDeleteRequest.php';

class WatchlistService
{
    private WatchlistRepository $watchlistRepository;
    private WatchlistItemRepository $watchlistItemRepository;
    private WatchlistLikeRepository $watchlistLikeRepository;
    private WatchlistSaveRepository $watchlistSaveRepository;
    private WatchlistTagRepository $watchlistTagRepository;

    public function __construct(WatchlistRepository $watchlistRepository, WatchlistItemRepository $watchlistItemRepository, WatchlistLikeRepository $watchlistLikeRepository, WatchlistSaveRepository $watchlistSaveRepository, WatchlistTagRepository $watchlistTagRepository)
    {
        $this->watchlistRepository = $watchlistRepository;
        $this->watchlistItemRepository = $watchlistItemRepository;
        $this->watchlistLikeRepository = $watchlistLikeRepository;
        $this->watchlistSaveRepository = $watchlistSaveRepository;
        $this->watchlistTagRepository = $watchlistTagRepository;
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
                $watchlistItem = new WatchlistItem();
                $watchlistItem->uuid = UUIDGenerator::uuid4();
                $watchlistItem->rank = $currRank;
                $watchlistItem->description = $item["description"];
                $watchlistItem->watchlistId = $watchlistNew->id;
                $watchlistItem->catalogId = $item["id"];

                $this->watchlistItemRepository->save($watchlistItem);

                $currRank++;
            }

            // save the tags
            foreach ($watchlistCreateRequest->tags as $tag) {
                $watchlistTag = new WatchlistTag();
                $watchlistTag->tagId = $tag["id"];
                $watchlistTag->watchlistId = $watchlist->id;

                $this->watchlistTagRepository->save($watchlistTag);
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

            // delete all tags with corresponding watchlistId
            $this->watchlistTagRepository->deleteBy("watchlist_id", $watchlistNew->id);

            // save the tags
            foreach ($watchlistEditRequest->tags as $tag) {
                $watchlistTag = new WatchlistTag();
                $watchlistTag->tagId = $tag["id"];
                $watchlistTag->watchlistId = $watchlist->id;

                $this->watchlistTagRepository->save($watchlistTag);
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
        if (!isset($watchlistsGetRequest->tag) || !in_array(strtoupper(trim($watchlistsGetRequest->tag)), $watchlistsGetRequest->tagsInit)) {
            $watchlistsGetRequest->tag = "";
        }

        if ($watchlistsGetRequest->sortBy == "LOVE")
            $watchlistsGetRequest->sortBy = "love_count";
        if ($watchlistsGetRequest->sortBy == "DATE")
            $watchlistsGetRequest->sortBy = "w.created_at";

        $result = $this->watchlistRepository->findAllCustom($watchlistsGetRequest->userId, $watchlistsGetRequest->search, $watchlistsGetRequest->category, $watchlistsGetRequest->sortBy, $watchlistsGetRequest->order, $watchlistsGetRequest->tag, $watchlistsGetRequest->page, 2);
        return $result;
    }

    public function findByUser(WatchlistGetOneByUserRequest $request)
    {
        if (!isset($request->visibility) || !in_array(strtoupper(trim($request->visibility)), ["ALL", "PUBLIC", "PRIVATE"]) || strtoupper($request->visibility) == "ALL") {
            $request->visibility = "";
        }

        $result = $this->watchlistRepository->findByUser($request->userId, strtoupper($request->visibility), 1, 10);
        return $result;
    }

    public function findByUUID(WatchlistsGetOneRequest $request)
    {
        $result = $this->watchlistRepository->findByUUID($request->uuid, $request->userId, $request->page, $request->pageSize);
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

    public function deleteByUUID(WatchlistDeleteRequest $watchlistDeleteRequest)
    {
        $this->watchlistRepository->deleteBy("uuid", $watchlistDeleteRequest->watchlistUUID);
    }

    private function validateWatchlistCreateEditRequest(WatchlistCreateRequest|WatchlistEditRequest $watchlistCreateUpdateRequest)
    {
        if (!isset($watchlistCreateUpdateRequest->title) || trim($watchlistCreateUpdateRequest->title) == "") {
            throw new ValidationException("Title is required.");
        }
        if (strlen($watchlistCreateUpdateRequest->title) > 40) {
            throw new ValidationException("Title is too long. Maximum 40 chars.");
        }
        if (!isset($watchlistCreateUpdateRequest->visibility) || !in_array($watchlistCreateUpdateRequest->visibility, ["PUBLIC", "PRIVATE"])) {
            throw new ValidationException("Visibility is invalid.");
        }
        if (isset($watchlistCreateUpdateRequest->description) && strlen($watchlistCreateUpdateRequest->description) > 255) {
            throw new ValidationException("Description is too long. Maximum 255 characters.");
        }
        if (!isset($watchlistCreateUpdateRequest->items) || count($watchlistCreateUpdateRequest->items) == 0) {
            throw new ValidationException("Watchlist must contain 1 item.");
        }
        if (count($watchlistCreateUpdateRequest->items) > 50) {
            throw new ValidationException("Too many items. Maximum 50 items.");
        }

        foreach ($watchlistCreateUpdateRequest->items ?? [] as $item) {
            if (strlen($item["description"]) > 255) {
                throw new ValidationException("Description is too long for item ${item["title"]}. Maximum 255 chars.");
            }
        }

        $selectedTags = [];
        foreach ($watchlistCreateUpdateRequest->tags ?? [] as $tag) {
            $found = false;
            foreach ($watchlistCreateUpdateRequest->initialTags ?? [] as $initTag) {
                if ($tag["id"] == $initTag->id && !in_array($initTag->id, $selectedTags)) {
                    array_push($selectedTags, $initTag->id);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new ValidationException("Tags is invalid.");
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