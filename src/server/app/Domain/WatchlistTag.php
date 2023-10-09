<?php

require_once __DIR__ . '/../App/Domain.php';

class WatchlistTag extends Domain
{
    public int|string $id;
    public int $tagId;
    public int $watchlistId;

    public function toArray(): array
    {
        $array = [
            "tag_id" => $this->tagId,
            "watchlist_id" => $this->watchlistId
        ];

        if (isset($this->id)) {
            $array["id"] = $this->id;
        }

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["tag_id"])) {
            $this->tagId = $data["tag_id"];
        }

        if (isset($data["watchlist_id"])) {
            $this->watchlistId = $data["watchlist_id"];
        }
    }
}