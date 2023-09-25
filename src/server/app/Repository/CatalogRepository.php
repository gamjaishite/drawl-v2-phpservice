<?php

require_once __DIR__ . '/../Domain/Catalog.php';

class CatalogRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Catalog $catalog): Catalog
    {
        $statement = $this->connection->prepare("INSERT INTO catalogs(title, description, poster, trailer, category) VALUES (?, ?, ?, ?, ?)");
        $statement->execute([
            $catalog->title,
            $catalog->description,
            $catalog->poster,
            $catalog->trailer,
            $catalog->category,
        ]);
        return $catalog;
    }

    public function findById(string $id): ?Catalog
    {
        $statement = $this->connection->prepare("SELECT id, title, description, poster, trailer, category FROM catalogs WHERE id = ?");
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $catalog = new Catalog();
                $catalog->id = $row['id'];
                $catalog->title = $row['title'];
                $catalog->description = $row['description'];
                $catalog->poster = $row['poster'];
                $catalog->trailer = $row['trailer'];
                $catalog->category = $row['category'];

                return $catalog;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM catalogs");
    }
}