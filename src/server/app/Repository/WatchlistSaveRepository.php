<?php

require_once __DIR__ . '/../App/Repository.php';

class WatchlistSaveRepository extends Repository
{
    protected string $table = 'watchlist_save';

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findByUser(string $userId, int $page, int $pageSize)
    {
        $selectQuery = "
    SELECT
        w.id AS watchlist_id,
        json_agg(
            json_build_object(
                'rank', rank,
                'poster', poster,
                'catalog_uuid', c.uuid
            )
        ) AS posters,
        w.uuid AS watchlist_uuid, name AS creator, item_count,
        like_status, w.title, w.description, w.category, visibility, 
        like_count, w.created_at AS created_at, w.updated_at AS updated_at
    FROM
        (
        SELECT
            w.id, uuid, title, description, category, 
            visibility, like_count, item_count, w.user_id, 
            created_at, updated_at, 
            CASE WHEN w.id IN(
                SELECT watchlist_id
                FROM watchlist_like
                WHERE user_id = :user_id
            ) THEN TRUE ELSE FALSE
            END AS like_status
        FROM watchlists w
        JOIN watchlist_save wv ON
            w.id = wv.watchlist_id
        WHERE wv.user_id = :user_id
        LIMIT :limit OFFSET :offset
    ) AS w
    JOIN users AS u ON
        w.user_id = u.id
    JOIN(
        SELECT *
        FROM watchlist_items
        WHERE rank < 5
    ) AS wi
    ON wi.watchlist_id = w.id
    JOIN catalogs AS c
    ON c.id = wi.catalog_id
    GROUP BY w.id, w.uuid, creator,
        w.title, name, item_count, like_status, w.description,
        w.category, visibility, like_count, w.created_at, w.updated_at
    ";

        $pageCountQuery = "
    SELECT COUNT(*)
    FROM (
        SELECT w.id, w.user_id
        FROM watchlists w 
        JOIN watchlist_save wv 
        ON w.id = wv.watchlist_id
        WHERE wv.user_id = :user_id
    ) AS w JOIN users AS u 
    ON w.user_id = u.id
    JOIN (SELECT * FROM watchlist_items WHERE rank < 5) AS wi ON wi.watchlist_id = w.id
    JOIN catalogs AS c ON c.id = wi.catalog_id   
    ";

        $selectStatement = $this->connection->prepare($selectQuery);
        $selectStatement->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $selectStatement->bindParam(":limit", $pageSize, PDO::PARAM_INT);
        $selectStatement->bindValue(":offset", ($page - 1) * $pageSize, PDO::PARAM_INT);
        $selectStatement->execute();

        $pageCountStatement = $this->connection->prepare($pageCountQuery);
        $pageCountStatement->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $pageCountStatement->execute();

        try {
            $result = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
            $pageCount = $pageCountStatement->fetchColumn();

            return [
                'items' => $result,
                'page' => max(1, $page),
                'totalPage' => $pageSize > 0 ? ceil($pageCount / $pageSize) : 1
            ];
        } finally {
            $selectStatement->closeCursor();
            $pageCountStatement->closeCursor();
        }
    }
}