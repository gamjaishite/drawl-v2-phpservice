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
        parent::__construct($connection, new Catalog());
        $this->filterBuilder = new FilterBuilder();
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