<?php

class WatchlistItemRepository extends Repository
{
    protected string $table = 'watchlist_items';

    public function __construct(\PDO $connection, )
    {
        parent::__construct($connection);
    }

    public function findAll(array $projection = [], int|null $page = 1, int|null $pageSize = 10): array
    {
        // TO DO: Implemented soon
        return [];
    }

    public function findOne($key, $value, $projection = [])
    {
        // TO DO: Implemented soon
    }
}