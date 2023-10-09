<?php

class WatchlistsGetRequest
{
    public ?int $userId;
    public ?string $search;
    public ?string $category;
    public ?string $tag;
    public ?array $tagsInit;
    public ?string $sortBy;
    public ?string $order;
    public ?int $page;
}
