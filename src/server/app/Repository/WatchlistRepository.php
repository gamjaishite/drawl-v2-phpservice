<?php

require_once __DIR__ . '/../App/Repository.php';
require_once __DIR__ . '/../Domain/Watchlist.php';

class WatchlistRepository extends Repository
{
    protected string $table = 'watchlists';

    public function __construct(\PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findAll(array $projection = [], int $page = 1, int $pageSize = 10): array
    {
        return [];
    }

    public function findOne($key, $value, $projection = [])
    {
        // TO DO: Implemented soon
    }
}

class WatchlistItemRepository extends Repository
{
    protected string $table = 'watchlist_items';

    public function __construct(\PDO $connection, )
    {
        parent::__construct($connection);
    }

    public function findAll(array $projection = [], int $page = 1, int $pageSize = 10): array
    {
        // TO DO: Implemented soon
        return [];
    }

    public function findOne($key, $value, $projection = [])
    {
        // TO DO: Implemented soon
    }
}