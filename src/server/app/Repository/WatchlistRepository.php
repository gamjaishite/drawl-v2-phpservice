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

    public function findAllCustom(string $userId, string $search, string $category, string $sortBy, string $order, int $page = 1, int $pageSize = 10)
    {
        if ($category != "") $category = " AND category = '$category'";

        $selectStatement = $this->connection->prepare("
            SELECT w.id AS watchlist_id, json_agg(json_build_object(
                'rank', rank,
                'poster', poster,
                'catalog_uuid', c.uuid
                )) AS posters, w.uuid AS watchlist_uuid, name AS creator, item_count, loved, saved, w.title, w.description, w.category, visibility, love_count, w.updated_at AS updated_at
            FROM (
                SELECT
                    id, uuid, title, description, category, visibility, like_count AS love_count, item_count, user_id, updated_at,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_like
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS loved,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_save
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS saved
                FROM
                    watchlists w
                WHERE
                    (w.title ILIKE ? 
                    AND visibility = 'PUBLIC')
                    $category
                ORDER BY
                    $sortBy $order
                LIMIT ?
                OFFSET ?) AS w JOIN users AS u ON w.user_id = u.id
                JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
                JOIN catalogs AS c ON c.id = wi.catalog_id
            WHERE
                u.name ILIKE ? 
                OR w.title ILIKE ?
            GROUP BY
                w.id, watchlist_uuid, creator, w.title, name, item_count, loved, saved, w.description, w.category, visibility, love_count, w.updated_at
            ORDER BY
                $sortBy $order
            ;        
        ");

        $pageCountStatement = $this->connection->prepare("
            WITH rows AS (SELECT COUNT(*)
            FROM (
                SELECT
                    id, uuid, title, description, category, visibility, like_count AS love_count, item_count, user_id, updated_at,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_like
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS loved,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_save
                            WHERE user_id = ?
                        ) THEN TRUE
                        ELSE FALSE
                    END AS saved
                FROM
                    watchlists as w
                WHERE
                    visibility = 'PUBLIC' $category
                    AND w.title ILIKE ?
                ORDER BY 
                    $sortBy $order
                ) AS w JOIN users AS u ON w.user_id = u.id
                JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
                JOIN catalogs AS c ON c.id = wi.catalog_id
            WHERE
                u.name ILIKE ?
                OR w.title ILIKE ?
            GROUP BY
                w.id, w.uuid, u.name, w.title, name, item_count, loved, saved, w.description, w.category, visibility, love_count, w.updated_at
            ORDER BY
                $sortBy $order
            )
            SELECT COUNT(*) FROM rows;        
        ");

        $selectStatement->execute([$userId, $userId, '%' . $search . '%', $pageSize, ($page - 1) * $pageSize, '%' . $search . '%', '%' . $search . '%']);
        $pageCountStatement->execute([$userId, $userId, '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);

        try {
            return [
                "items" => $selectStatement->fetchAll(),
                "page" => $page,
                "pageTotal" => ceil($pageCountStatement->fetchColumn() / $pageSize)
            ];
        } finally {
            $selectStatement->closeCursor();
            $pageCountStatement->closeCursor();
        }

    }
}

