<?php

require_once __DIR__ . '/../Domain/Catalog.php';
require_once __DIR__ . '/../Utils/FilterBuilder.php';

class CatalogRepository
{
    private \PDO $connection;
    private FilterBuilder $filterBuilder;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->filterBuilder = new FilterBuilder();
    }

    public function save(Catalog $catalog): Catalog
    {
        $statement = $this->connection->prepare("INSERT INTO catalogs (uuid, title, description, poster, trailer, category) VALUES (?, ?, ?, ?, ?, ?)");
        $statement->execute([
            $catalog->uuid,
            $catalog->title,
            $catalog->description,
            $catalog->poster,
            $catalog->trailer,
            $catalog->category
        ]);

        try {
            $catalog->id = $this->connection->lastInsertId();
            return $catalog;
        } finally {
            $statement->closeCursor();
        }
    }

    public function update(Catalog $catalog): Catalog
    {
        $statement = $this->connection->prepare("UPDATE catalogs SET uuid = ?, title = ?, description = ?, poster = ?, trailer = ?, category = ? WHERE id = ?");
        $statement->execute([
            $catalog->uuid,
            $catalog->title,
            $catalog->description,
            $catalog->poster,
            $catalog->trailer,
            $catalog->category,
            $catalog->id
        ]);

        try {
            return $catalog;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAll(
        array $filter = [],
        int $page = 1,
        int $pageSize = 10
    ): array {
        $query = "SELECT id, uuid, title, description, poster, trailer, category FROM catalogs";

        $filterCount = 0;
        foreach ($filter as $key => $value) {
            if ($filterCount == 0) {
                $this->filterBuilder->whereEquals($key, $value);
            } else {
                $this->filterBuilder->andWhereEquals($key, $value);
            }
            $filterCount += 1;
        }

        $query .= $this->filterBuilder->filterQuery;

        if ($pageSize) {
            $query .= " LIMIT $pageSize";
        }

        if ($page) {
            $offset = ($page - 1) * $pageSize;
            $query .= " OFFSET $offset";
        }

        $statement = $this->connection->prepare($query);

        $statement->execute();

        try {
            $catalogs = [];
            while ($row = $statement->fetch()) {
                $catalog = new Catalog();
                $catalog->id = $row['id'];
                $catalog->uuid = $row['uuid'];
                $catalog->title = $row['title'];
                $catalog->description = $row['description'];
                $catalog->poster = $row['poster'];
                $catalog->trailer = $row['trailer'];
                $catalog->category = $row['category'];

                $catalogs[] = $catalog;
            }

            return $catalogs;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findById(int $id): ?Catalog
    {
        $statement = $this->connection->prepare("SELECT id, uuid, title, description, poster, trailer, category FROM catalogs WHERE id = ?");
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $catalog = new Catalog();
                $catalog->id = $row['id'];
                $catalog->uuid = $row['uuid'];
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

    public function findByCategory(Catalog $catalog)
    {
        $statement = $this->connection->prepare("SELECT id, uuid, title, description, poster, trailer, category FROM catalogs WHERE category = ?");
        $statement->execute([$catalog->category]);

        try {
            $catalogs = [];
            while ($row = $statement->fetch()) {
                $catalog = new Catalog();
                $catalog->id = $row['id'];
                $catalog->uuid = $row['uuid'];
                $catalog->title = $row['title'];
                $catalog->description = $row['description'];
                $catalog->poster = $row['poster'];
                $catalog->trailer = $row['trailer'];
                $catalog->category = $row['category'];

                $catalogs[] = $catalog;
            }

            return $catalogs;
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM catalogs");
    }

    public function deleteById(int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM catalogs WHERE id = ?");
        $statement->execute([$id]);

        $statement->closeCursor();
    }

    public function countPage($pageSize = 10): int
    {
        $statement = $this->connection->prepare("SELECT COUNT(*) FROM catalogs");
        $statement->execute();

        try {
            $count = $statement->fetchColumn();
            return ceil($count / $pageSize);
        } finally {
            $statement->closeCursor();
        }
    }
}