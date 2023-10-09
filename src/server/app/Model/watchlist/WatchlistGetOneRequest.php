<?php

class WatchlistsGetOneRequest
{
    public ?int $userId = null;
    public string $uuid;
    public int $page = 1;
    public int $pageSize = 10;
}