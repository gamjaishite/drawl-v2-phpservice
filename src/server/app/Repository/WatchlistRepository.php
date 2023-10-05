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
                    $sortBy $order,
                    updated_at DESC
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
                $sortBy $order,
                w.updated_at DESC
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
                    $sortBy $order,
                    updated_at DESC
                ) AS w JOIN users AS u ON w.user_id = u.id
                JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
                JOIN catalogs AS c ON c.id = wi.catalog_id
            WHERE
                u.name ILIKE ?
                OR w.title ILIKE ?
            GROUP BY
                w.id, w.uuid, u.name, w.title, name, item_count, loved, saved, w.description, w.category, visibility, love_count, w.updated_at
            ORDER BY
                $sortBy $order,
                w.updated_at DESC
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

    public function findUserBookmarks(int $userId, string|null $visibility, int $page = null, int $pageSize = null)
    {
        $query = "
        FROM (
            SELECT
                id, uuid, title, description, category, visibility, like_count, item_count, user_id, updated_at,
                CASE
                    WHEN id IN (
                        SELECT watchlist_id
                        FROM watchlist_like
                        WHERE user_id = :user_id
                    ) THEN TRUE
                    ELSE FALSE
                END AS like_status
            FROM
                watchlists
            WHERE
                user_id = :user_id 
                AND visibility = :visibility
                LIMIT :limit
                OFFSET :offset
            ) AS w JOIN users AS u ON w.user_id = u.id
            JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
            JOIN catalogs AS c ON c.id = wi.catalog_id
        ";


        $selectQuery = "SELECT  w.id AS watchlist_id, json_agg(json_build_object(
            'rank', rank,
            'poster', poster,
            'catalog_uuid', c.uuid
            )) AS posters, w.uuid AS watchlist_uuid, name AS creator, item_count, like_status, w.title, w.description, w.category, visibility, like_count, w.updated_at AS updated_at ";

        $pageCountQuery = "SELECT COUNT(*) ";

        if (empty($visibility)) {
            $query = str_replace("AND visibility = ':visibility'", "", $query);
        }
        $selectStatement = $this->connection->prepare($selectQuery . $query . "GROUP BY
        watchlist_id, watchlist_uuid, creator, w.title, w.uuid, name, item_count, like_status, w.id, w.description, w.category, visibility, like_count, w.updated_at;");
        $selectStatement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $selectStatement->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $offset = ($page - 1) * $pageSize;
        $selectStatement->bindValue(':offset', $offset, PDO::PARAM_INT);

        $pageCountStatement = $this->connection->prepare($pageCountQuery . $query);
        $pageCountStatement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $pageCountStatement->bindValue(':limit', PHP_INT_MAX, PDO::PARAM_INT);
        $pageCountStatement->bindValue(':offset', 0, PDO::PARAM_INT);
        if (!empty($visibility)) {
            $selectStatement->bindValue(':visibility', $visibility, PDO::PARAM_STR);
            $pageCountStatement->bindValue(':visibility', $visibility, PDO::PARAM_STR);
        }


        $selectStatement->execute();
        $pageCountStatement->execute();

        try {
            $rows = $selectStatement->fetchAll();
            return [
                'items' => $rows,
                'page' => max(1, $page),
                'totalPage' => $pageSize > 0 ? ceil($pageCountStatement->fetchColumn() / $pageSize) : 1
            ];
        } finally {
            $selectStatement->closeCursor();
            $pageCountStatement->closeCursor();
        }
    }
}