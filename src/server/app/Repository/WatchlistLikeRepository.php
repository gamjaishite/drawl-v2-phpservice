<?php

require_once __DIR__ . '/../App/Repository.php';

class WatchlistLikeRepository extends Repository
{
    protected string $table = "watchlist_like";

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }

    public function saveByWatchlistAndUser(string $watchlistId, string $userId)
    {
        $statement = $this->connection->prepare("INSERT INTO $this->table (watchlist_id, user_id) VALUES (:watchlist_id, :user_id)");
        $statement->bindValue(":watchlist_id", $watchlistId);
        $statement->bindValue(":user_id", $userId);
        $statement->execute();

        $statement->closeCursor();
    }

    public function findOneByWatchlistAndUser(string $watchlistId, string $userId)
    {
        $statement = $this->connection->prepare("SELECT * FROM $this->table WHERE watchlist_id = :watchlist_id AND user_id = :user_id");
        $statement->bindValue(":watchlist_id", $watchlistId);
        $statement->bindValue(":user_id", $userId);
        $statement->execute();

        try {
            if ($row = $statement->fetch()) {
                return $row;
            }
            return null;
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteByWatchlistAndUser(string $watchlistId, string $userId)
    {
        $statement = $this->connection->prepare("DELETE FROM $this->table WHERE watchlist_id = :watchlist_id AND user_id = :user_id");
        $statement->bindValue(":watchlist_id", $watchlistId);
        $statement->bindValue(":user_id", $userId);
        $statement->execute();

        $statement->closeCursor();
    }
}