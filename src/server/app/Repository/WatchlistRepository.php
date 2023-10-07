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

    public function findAllCustom(string $userId, string $search, string $category, string $sortBy, string $order, string $tag, int $page = 1, int $pageSize = 10)
    {
        if ($category != "")
            $category = " AND category = '$category'";

        // Queries
        $selectFirstQuery = "
            WITH first_agg AS (
            SELECT w.id AS watchlist_id, jsonb_agg(jsonb_build_object(
                'rank', rank,
                'poster', poster,
                'catalog_uuid', c.uuid
                )) AS posters, w.uuid AS watchlist_uuid, u.name AS creator, u.uuid AS creator_uuid, item_count, loved, saved, w.title, w.description, w.category, visibility, love_count, w.created_at AS created_at    
            ";
        $selectSecondQuery = "
            )
            SELECT 
                jsonb_agg(jsonb_build_object(
                'id', t.id,
                'name', t.name
                )) AS tags, w.watchlist_id, w.posters as posters, w.watchlist_uuid, w.creator, w.creator_uuid, w.item_count, w.loved, w.saved, w.title, w.description, w.category, w.visibility, w.love_count, w.created_at
            FROM first_agg AS w
            LEFT JOIN watchlist_tag as wt ON wt.watchlist_id = w.watchlist_id
            LEFT JOIN tags as t ON t.id = wt.tag_id
            GROUP BY
                w.watchlist_id, w.watchlist_uuid, w.posters, w.creator, w.creator_uuid, w.item_count, w.loved, w.saved, w.title, w.description, w.category, w.visibility, w.love_count, w.created_at
            ORDER BY
                $sortBy $order,
                w.created_at DESC
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
                w.id, w.uuid, u.name, w.title, u.name, u.uuid, item_count, loved, saved, w.description, w.category, visibility, love_count, w.created_at
            ORDER BY
                $sortBy $order,
                w.created_at DESC
            ";

        if ($tag) {
            $selectStatement = $this->connection->prepare("WITH outer_query AS (" .
                $selectFirstQuery . $mainQuery . $selectSecondQuery .
                ") SELECT * FROM outer_query as o
                   WHERE EXISTS 
                   (SELECT 1 
                    FROM jsonb_array_elements(o.tags) 
                    AS elem WHERE elem @> '{\"name\": \"$tag\"}'::jsonb)
                    LIMIT :limit
                    OFFSET :offset
                    ");
            $pageCountStatement = $this->connection->prepare("WITH outer_query AS (" .
                $selectFirstQuery . $mainQuery . $selectSecondQuery .
                ") SELECT COUNT(*) FROM outer_query as o
                   WHERE EXISTS 
                   (SELECT 1 
                    FROM jsonb_array_elements(o.tags) 
                    AS elem WHERE elem @> '{\"name\": \"$tag\"}'::jsonb)
                    LIMIT :limit
                    OFFSET :offset
                    ");
        } else {
            $selectStatement = $this->connection->prepare($selectFirstQuery . $mainQuery . $selectSecondQuery . "                     LIMIT :limit
                    OFFSET :offset");
            $pageCountStatement = $this->connection->prepare($countQuery . $mainQuery . ") SELECT COUNT(*) FROM rows");
        }


        // Binding select
        $selectStatement->bindValue(":user_id", $userId);
        $selectStatement->bindValue(":watchlist_title", '%' . $search . '%');
        $selectStatement->bindValue(":limit", $pageSize);
        $selectStatement->bindValue(":offset", ($page - 1) * $pageSize);
        $selectStatement->bindValue(":creator", '%' . $search . '%');

        // Binding count
        $pageCountStatement->bindValue(":user_id", $userId);
        $pageCountStatement->bindValue(":watchlist_title", '%' . $search . '%');
        if ($tag) {
            $pageCountStatement->bindValue(":limit", PHP_INT_MAX);
            $pageCountStatement->bindValue(":offset", 0);
        }
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

    public function findByUser(int $userId, string|null $visibility, int $page = null, int $pageSize = null)
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
                END AS liked
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
        $selectFirstQuery = "
        WITH first_agg AS (
        SELECT w.id AS watchlist_id, jsonb_agg(jsonb_build_object(
            'rank', rank,
            'poster', poster,
            'catalog_uuid', c.uuid
            )) AS posters, w.uuid AS watchlist_uuid, name AS creator, u.uuid AS creator_uuid, item_count, liked, w.title, w.description, w.category, visibility, like_count, w.updated_at AS updated_at, w.created_at AS created_at";
        $selectSecondQuery = "
            )
            SELECT 
                jsonb_agg(jsonb_build_object(
                'id', t.id,
                'name', t.name
                )) AS tags, fa.watchlist_id, fa.posters as posters, fa.watchlist_uuid, fa.creator, fa.creator_uuid, fa.item_count, fa.liked, fa.title, fa.description, fa.category, fa.visibility, fa.like_count, fa.created_at
            FROM first_agg AS fa
            LEFT JOIN watchlist_tag as wt ON wt.watchlist_id = fa.watchlist_id
            LEFT JOIN tags as t ON t.id = wt.tag_id
            GROUP BY
                fa.watchlist_id, fa.watchlist_uuid, fa.posters, fa.creator, fa.creator_uuid, fa.item_count, fa.liked, fa.title, fa.description, fa.category, fa.visibility, fa.like_count, fa.created_at
            ORDER BY
                fa.created_at DESC
        ";
        $pageCountQuery = "SELECT COUNT(*) ";

        $selectStatement = $this->connection->prepare($selectFirstQuery . $query . "GROUP BY
        watchlist_id, watchlist_uuid, creator, u.uuid, w.title, w.uuid, u.name, item_count, liked, w.id, w.description, w.category, visibility, like_count, w.updated_at, w.created_at" . $selectSecondQuery);
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
        WITH first_agg AS (
        WITH w AS (
            SELECT
                id, uuid, title, description, category, visibility, like_count, item_count, user_id, created_at
                ,CASE
                    WHEN id IN (
                        SELECT watchlist_id
                        FROM watchlist_like
                        WHERE user_id = :user_id
                    ) THEN TRUE
                    ELSE FALSE
                END AS liked,
                CASE
                    WHEN id IN (
                        SELECT watchlist_id
                        FROM watchlist_save
                        WHERE user_id = :user_id
                    ) THEN TRUE
                    ELSE FALSE
                END AS saved
            FROM
                watchlists
            WHERE
                watchlists.uuid = :uuid
            LIMIT 1
            )
        SELECT w.id AS watchlist_id, jsonb_agg(jsonb_build_object(
            'rank', rank,
            'poster', poster,
            'catalog_uuid', c.uuid,
            'catalog_id', c.id,
            'description', wi.description,
            'title', c.title,
            'category', c.category
            )) AS catalogs, w.uuid AS watchlist_uuid, name AS creator, item_count, w.title, w.description, w.category, visibility, like_count, w.created_at, u.uuid AS creator_uuid
            ,liked, saved
        FROM w JOIN users AS u ON w.user_id = u.id
            , (SELECT * FROM watchlist_items WHERE watchlist_id IN (SELECT id FROM w) ORDER BY rank LIMIT :limit OFFSET :offset) AS wi
            JOIN catalogs AS c ON c.id = wi.catalog_id
            GROUP BY
            watchlist_id, watchlist_uuid, creator, w.title, w.uuid, name, item_count, w.id, w.description, w.category, visibility, like_count, w.created_at, u.uuid
            ,liked, saved
        ) 
        SELECT 
            jsonb_agg(jsonb_build_object(
            'id', t.id,
            'name', t.name
            )) AS tags, fa.watchlist_id, fa.catalogs, fa.watchlist_uuid, fa.creator, fa.creator_uuid, fa.item_count, fa.liked, fa.saved, fa.title, fa.description, fa.category, fa.visibility, fa.like_count, fa.created_at
        FROM first_agg AS fa
        LEFT JOIN watchlist_tag as wt ON wt.watchlist_id = fa.watchlist_id
        LEFT JOIN tags as t ON t.id = wt.tag_id
        GROUP BY
            fa.watchlist_id, fa.watchlist_uuid, fa.catalogs, fa.creator, fa.creator_uuid, fa.item_count, fa.liked, fa.saved, fa.title, fa.description, fa.category, fa.visibility, fa.like_count, fa.created_at
        ORDER BY
            fa.created_at DESC
        ";

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
        $selectStatement->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $selectStatement->bindParam(':limit', $pageSize, PDO::PARAM_INT);
        $offset = ($page - 1) * $pageSize;
        $selectStatement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $selectStatement->bindParam(':user_id', $user_id, PDO::PARAM_INT);


        $pageCountStatement = $this->connection->prepare($pageCountQuery);
        $pageCountStatement->bindParam(':uuid', $uuid, PDO::PARAM_STR);

        $selectStatement->execute();
        $pageCountStatement->execute();

        try {
            function catalogCompare($element1, $element2)
            {
                return $element1["rank"] - $element2["rank"];
            }

            $totalPage = $pageSize > 0 ? ceil($pageCountStatement->fetchColumn() / $pageSize) : 1;
            if ($rows = $selectStatement->fetch()) {
                $catalogs = json_decode($rows["catalogs"], true);
                $tags = json_decode($rows["tags"], true);
                $tags = array_filter($tags, function ($value) {
                    return $value["id"] !== null;
                });
                usort($catalogs, "catalogCompare");
                $rows["tags"] = $tags;

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