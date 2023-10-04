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

    public function findAll(array $projection = [], int|null $page = null, int|null $pageSize = null): array
    {
        $result = parent::findAll($projection, $page, $pageSize);

        $result['items'] = array_map(
            function ($row) {
                $watchlist = new Watchlist();
                $watchlist->fromArray($row);

                return $watchlist;
            },
            $result['items']
        );

        return $result;
    }

    public function findAllWithUser(int $userId, array $projection = [], int $page = 1, int $pageSize = 10)
    {
        $this->currentQuery = "WITH userWatchlist AS (SELECT watchlist_id FROM watchlist_save WHERE user_id = $userId) ";
        $case = "CASE WHEN userWatchlist.watchlist_id IS NULL THEN FALSE ELSE TRUE END AS saved";
        $projection[] = $case;
        return $this->query()->join("user_id", "users", "id")->get($projection, $page, $pageSize);
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

    public function findOne($key, $value, $projection = [])
    {
        // TO DO: Implemented soon
    }
}