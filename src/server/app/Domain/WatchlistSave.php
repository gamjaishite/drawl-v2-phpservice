<?php

require_once __DIR__ . '/../App/Domain.php';

class WatchlistSave extends Domain
{
    public int|string $id;
    public int $user_id;
    public int $watchlist_id;

    public function toArray(): array
    {
        $array = [
            'user_id' => $this->user_id,
            'watchlist_id' => $this->watchlist_id,
        ];

        if (isset($this->id)) {
            $array['id'] = $this->id;
        }

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["user_id"])) {
            $this->user_id = $data["user_id"];
        }

        if (isset($data["watchlist_id"])) {
            $this->watchlist_id = $data["watchlist_id"];
        }
    }
}