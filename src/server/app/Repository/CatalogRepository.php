<?php

require_once __DIR__ . '/../App/Repository.php';
require_once __DIR__ . '/../Domain/Catalog.php';
require_once __DIR__ . '/../Utils/FilterBuilder.php';

class CatalogRepository extends Repository
{
    protected string $table = 'catalogs';

    public function __construct(\PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findAll(array $projection = [], int|null $page = null, int|null $pageSize = null): array
    {
        $result = parent::findAll($projection, $page, $pageSize);

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
}