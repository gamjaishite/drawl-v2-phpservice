<?php

class WatchlistCreateRequest
{
    public ?int $userId;
    public ?string $title;
    public ?string $description;
    public ?string $visibility;
    public ?array $items;
}
