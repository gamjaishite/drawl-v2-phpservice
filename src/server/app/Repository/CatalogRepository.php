<?php

require_once __DIR__ . '/../App/Repository.php';
require_once __DIR__ . '/../Domain/Catalog.php';
require_once __DIR__ . '/../Utils/FilterBuilder.php';

class CatalogRepository extends Repository
{
    private FilterBuilder $filterBuilder;
    protected string $table = 'catalogs';

    public function __construct(\PDO $connection)
    {
        parent::__construct($connection);
        $this->filterBuilder = new FilterBuilder();
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
        array $search = [],
        array $projection = [],
        int $page = 1,
        int $pageSize = 10
    ): array {
        $result = parent::findAll($filter, $search, $projection, $page, $pageSize);

        $result['items'] = array_map(
            function ($row) {
                $catalog = new Catalog();
                $catalog->fromArray($row);
                return $catalog;
            },
            $result['items']
        );
        return $result;
    }

    public function findOne($key, $value, $projection = []): ?Catalog
    {
        $result = parent::findOne($key, $value, $projection);

        if ($result) {
            $catalog = new Catalog();
            $catalog->fromArray($result);
            return $catalog;
        } else {
            return null;
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
}