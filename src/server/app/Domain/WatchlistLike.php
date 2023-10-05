<?php

class WatchlistLike extends Domain
{
    public int|string $watchlistId;
    public int|string $userId;

    public function toArray(): array
    {
        $array = [];

        if (isset($this->watchlistId)) {
            $array["watchlist_id"] = $this->watchlistId;
        }

        if (isset($this->userId)) {
            $array["user_id"] = $this->userId;
        }

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["watchlist_id"])) {
            $this->watchlistId = $data["watchlist_id"];
        }

        if (isset($data["user_id"])) {
            $this->userId = $data["user_id"];
        }
    }
}