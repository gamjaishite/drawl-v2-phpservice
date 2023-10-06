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

    public function findOne($key, $value, $projection = []): ?Watchlist
    {
        $result = parent::findOne($key, $value, $projection);

        if ($result) {
            $watchlist = new Watchlist();
            $watchlist->fromArray($result);
            return $watchlist;
        } else {
            return null;
        }
    }

    public function findAllCustom(string $userId, string $search, string $category, string $sortBy, string $order, int $page = 1, int $pageSize = 10)
    {
        if ($category != "")
            $category = " AND category = '$category'";

        // Queries
        $selectQuery = "
            SELECT w.id AS watchlist_id, json_agg(json_build_object(
                'rank', rank,
                'poster', poster,
                'catalog_uuid', c.uuid
                )) AS posters, w.uuid AS watchlist_uuid, name AS creator, u.uuid AS creator_uuid, item_count, loved, saved, w.title, w.description, w.category, visibility, love_count, w.created_at AS created_at    
            ";
        $countQuery = "WITH rows AS (SELECT COUNT(*)";
        $mainQuery = "
            FROM (
                SELECT
                    id, uuid, title, description, category, visibility, like_count AS love_count, item_count, user_id, created_at,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_like
                            WHERE user_id = :user_id
                        ) THEN TRUE
                        ELSE FALSE
                    END AS loved,
                    CASE
                        WHEN id IN (
                            SELECT watchlist_id
                            FROM watchlist_save
                            WHERE user_id = :user_id
                        ) THEN TRUE
                        ELSE FALSE
                    END AS saved
                FROM
                    watchlists w
                WHERE
                    (w.title ILIKE :watchlist_title
                    AND visibility = 'PUBLIC'
                    $category)
                    OR
                    (visibility = 'PUBLIC' 
                    $category)
                ORDER BY
                    $sortBy $order,
                    created_at DESC
                ) AS w JOIN users AS u ON w.user_id = u.id
                JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
                JOIN catalogs AS c ON c.id = wi.catalog_id
            WHERE
                u.name ILIKE :creator
                OR w.title ILIKE :watchlist_title
            GROUP BY
                w.id, w.uuid, u.name, w.title, name, u.uuid, item_count, loved, saved, w.description, w.category, visibility, love_count, w.created_at
            ORDER BY
                $sortBy $order,
                w.created_at DESC
            LIMIT :limit
            OFFSET :offset
            ";

        $selectStatement = $this->connection->prepare($selectQuery . $mainQuery);
        $pageCountStatement = $this->connection->prepare($countQuery . $mainQuery . ") SELECT COUNT(*) FROM rows");

        // Binding select
        $selectStatement->bindValue(":user_id", $userId);
        $selectStatement->bindValue(":watchlist_title", '%' . $search . '%');
        $selectStatement->bindValue(":limit", $pageSize);
        $selectStatement->bindValue(":offset", ($page - 1) * $pageSize);
        $selectStatement->bindValue(":creator", '%' . $search . '%');

        // Binding count
        $pageCountStatement->bindValue(":user_id", $userId);
        $pageCountStatement->bindValue(":watchlist_title", '%' . $search . '%');
        $pageCountStatement->bindValue(":limit", PHP_INT_MAX);
        $pageCountStatement->bindValue(":offset", 0);
        $pageCountStatement->bindValue(":creator", '%' . $search . '%');

        $selectStatement->execute();
        $pageCountStatement->execute();

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
                id, uuid, title, description, category, visibility, like_count, item_count, user_id, updated_at, created_at,
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
                user_id = :user_id " . (!empty($visibility) ? "AND visibility = :visibility" : "") . "
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
            )) AS posters, w.uuid AS watchlist_uuid, name AS creator, u.uuid AS creator_uuid, item_count, like_status, w.title, w.description, w.category, visibility, like_count, w.updated_at AS updated_at, w.created_at AS created_at ";

        $pageCountQuery = "SELECT COUNT(*) ";

        $selectStatement = $this->connection->prepare($selectQuery . $query . "GROUP BY
        watchlist_id, watchlist_uuid, creator, u.uuid, w.title, w.uuid, name, item_count, like_status, w.id, w.description, w.category, visibility, like_count, w.updated_at, w.created_at;");
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

    public function findByUUID(string $uuid, int|null $user_id, int $page = 1, int $pageSize = 10)
    {
        $selectQuery = "
        WITH w AS (
            SELECT
                id, uuid, title, description, category, visibility, like_count, item_count, user_id, updated_at " .
            ($user_id === null ? "" : ", CASE
                    WHEN id IN (
                        SELECT watchlist_id
                        FROM watchlist_like
                        WHERE user_id = :user_id
                    ) THEN TRUE
                    ELSE FALSE
                END AS like_status,
                CASE
                    WHEN id IN (
                        SELECT watchlist_id
                        FROM watchlist_save
                        WHERE user_id = :user_id
                    ) THEN TRUE
                    ELSE FALSE
                END AS save_status ") . "
            FROM
                watchlists
            WHERE
                watchlists.uuid = :uuid
            LIMIT 1)
        SELECT w.id AS watchlist_id, json_agg(json_build_object(
            'rank', rank,
            'poster', poster,
            'catalog_uuid', c.uuid,
            'description', wi.description,
            'title', c.title,
            'category', c.category
            )) AS catalogs, w.uuid AS watchlist_uuid, name AS creator, item_count, w.title, w.description, w.category, visibility, like_count, w.updated_at AS updated_at"
            . ($user_id === null ? "" : ", like_status, save_status") . "
        FROM w JOIN users AS u ON w.user_id = u.id
            , (SELECT * FROM watchlist_items WHERE watchlist_id IN (SELECT id FROM w) ORDER BY rank LIMIT :limit OFFSET :offset) AS wi
            JOIN catalogs AS c ON c.id = wi.catalog_id
            GROUP BY
            watchlist_id, watchlist_uuid, creator, w.title, w.uuid, name, item_count, w.id, w.description, w.category, visibility, like_count, w.updated_at"
            . ($user_id === null ? "" : ", like_status, save_status ");

        $pageCountQuery = "
        SELECT COUNT(*) 
        FROM (
            SELECT id, user_id
            FROM
                watchlists
            WHERE
                watchlists.uuid = :uuid
            LIMIT 1) AS w JOIN users AS u ON w.user_id = u.id
            JOIN (SELECT * FROM watchlist_items ORDER BY rank) AS wi ON wi.watchlist_id = w.id
            JOIN catalogs AS c ON c.id = wi.catalog_id
        ";


        $selectStatement = $this->connection->prepare($selectQuery);
        $selectStatement->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $selectStatement->bindValue(':limit', $pageSize, PDO::PARAM_INT);
        $offset = ($page - 1) * $pageSize;
        $selectStatement->bindValue(':offset', $offset, PDO::PARAM_INT);
        if (!empty($user_id)) {
            $selectStatement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        }

        $pageCountStatement = $this->connection->prepare($pageCountQuery);
        $pageCountStatement->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        if (!empty($user_id)) {
            $pageCountStatement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        }

        $selectStatement->execute();
        $pageCountStatement->execute();

        try {
            $totalPage = $pageSize > 0 ? ceil($pageCountStatement->fetchColumn() / $pageSize) : 1;
            if ($rows = $selectStatement->fetch()) {
                $catalogs = json_decode($rows["catalogs"], true);
                $rows["catalogs"] = [
                    "items" => $catalogs,
                    "page" => max(1, $page),
                    'totalPage' => $totalPage
                ];
                return $rows;
            }

            return null;
        } finally {
            $selectStatement->closeCursor();
            $pageCountStatement->closeCursor();
        }
    }
}