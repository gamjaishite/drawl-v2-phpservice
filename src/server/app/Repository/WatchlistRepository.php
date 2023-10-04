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

    public function findOne($key, $value, $projection = [])
    {
        // TO DO: Implemented soon
    }

    public function findAllCustom(string $userId, int $page = 1, int $pageSize = 10)
    {
        $statement = $this->connection->prepare("
            SELECT w.id AS watchlist_id, json_agg(json_build_object(
                'rank', rank,
                'poster', poster,
                'catalog_uuid', c.uuid
                )) AS posters, w.uuid AS watchlist_uuid, name AS creator, item_count, like_status, save_status, w.title, w.description, w.category, visibility, like_count, w.updated_at AS updated_at
            FROM (
                SELECT
                    id, uuid, title, description, category, visibility, like_count, item_count, user_id, updated_at,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_like
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS like_status,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_save
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS save_status
                FROM
                    watchlists
                WHERE
                    visibility = 'PUBLIC'
                ORDER BY
                    like_count DESC
                LIMIT ?
                OFFSET ?) AS w JOIN users AS u ON w.user_id = u.id
                JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
                JOIN catalogs AS c ON c.id = wi.catalog_id
            GROUP BY
                watchlist_id, watchlist_uuid, creator, w.title, w.uuid, name, item_count, like_status, save_status, w.id, w.description, w.category, visibility, like_count, w.updated_at
            ORDER BY
                like_count DESC
            ;        
        ");
        $statement->execute([$userId, $userId, $pageSize, ($page - 1) * $pageSize]);

        try {
            $rows = $statement->fetchAll();
            return $rows;
        } finally {
            $statement->closeCursor();
        }

    }
}