<?php



class Watchlist extends Domain
{
    public int $id;
    public string $uuid;
    public string $title;
    public ?string $description;
    public string $category;
    public int $userId;
    public int $likeCount;
    public string $visibility;
    public string $createdAt;
    public string $updatedAt;


    public function toArray(): array
    {
        $array = [
            "title" => $this->title,
            "uuid" => $this->uuid,
            "description" => $this->description,
            "category" => $this->category,
            "user_id" => $this->userId,
            "visibility" => $this->visibility,
        ];

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["uuid"])) {
            $this->uuid = $data["uuid"];
        }

        if (isset($data["title"])) {
            $this->title = $data["title"];
        }

        if (isset($data["description"])) {
            $this->description = $data["description"];
        }

        if (isset($data["category"])) {
            $this->category = $data["category"];
        }

        if (isset($data["userId"])) {
            $this->userId = $data["userId"];
        }

        if (isset($data["likeCount"])) {
            $this->likeCount = $data["likeCount"];
        }

        if (isset($data["visibility"])) {
            $this->visibility = $data["visibility"];
        }

        if (isset($data["createdAt"])) {
            $this->createdAt = $data["createdAt"];
        }

        if (isset($data["updatedAt"])) {
            $this->updatedAt = $data["updatedAt"];
        }
    }
}

class WatchlistItem extends Domain
{
    public int $id;
    public string $uuid;
    public int $rank;
    public ?string $description;
    public int $watchlistId;
    public int $catalogId;
    public string $createdAt;
    public string $updatedAt;

    public function toArray(): array
    {
        $array = [
            'uuid' => $this->uuid,
            'rank' => $this->rank,
            'description' => $this->description,
            'watchlist_id' => $this->watchlistId,
            'catalog_id' => $this->catalogId
        ];

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["uuid"])) {
            $this->uuid = $data["uuid"];
        }

        if (isset($data["rank"])) {
            $this->rank = $data["rank"];
        }

        if (isset($data["description"])) {
            $this->description = $data["description"];
        }

        if (isset($data["watchlist_id"])) {
            $this->watchlistId = $data["watchlist_id"];
        }

        if (isset($data["catalog_id"])) {
            $this->catalogId = $data["catalog_id"];
        }

        if (isset($data["created_at"])) {
            $this->createdAt = $data["created_at"];
        }

        if (isset($data["updated_at"])) {
            $this->updatedAt = $data["updated_at"];
        }
    }
}
