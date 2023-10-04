<?php
require_once __DIR__ . '/../Utils/QueryBuilder.php';

/**
 * ABC for Repository
 * 
 * Provides basic CRUD operations
 */
abstract class Repository
{
    protected \PDO $connection;
    protected string $table;
    protected QueryBuilder $queryBuilder;
    protected string $currentQuery = "";

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
        $this->queryBuilder = new QueryBuilder($this);
    }

    public function query()
    {
        $this->queryBuilder->query = "";
        return $this->queryBuilder;
    }

    public function getTable()
    {
        return $this->table;
    }

    protected function reset()
    {
        $this->queryBuilder->query = "";
        $this->currentQuery = "";
    }

    public function save(Domain $domain)
    {
        $array = $domain->toArray();
        $domainKeyLength = count($array);
        $this->currentQuery = "INSERT INTO {$this->table} (";

        $countKey = 0;
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $this->currentQuery .= "$key";
            }

            if ($countKey < $domainKeyLength - 1) {
                $this->currentQuery .= ", ";
            }

            $countKey += 1;
        }

        $this->currentQuery .= ") VALUES (";
        $countKey = 0;
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $this->currentQuery .= ":$key";
            }

            if ($countKey < $domainKeyLength - 1) {
                $this->currentQuery .= ", ";
            }

            $countKey += 1;
        }

        $this->currentQuery .= ")";
        $statement = $this->connection->prepare($this->currentQuery);
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $statement->bindValue(":$key", $value);
            }
        }

        $statement->execute();

        $this->reset();

        try {
            $domain->id = $this->connection->lastInsertId();
            return $domain;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAll(array $projection = [], int|null $page = null, int|null $pageSize = null): array
    {
        $selectQuery = "SELECT ";
        $pageCountQuery = "SELECT COUNT(*) ";

        if (count($projection) === 0) {
            $selectQuery .= "*";
        } else {
            $countProjection = 0;
            foreach ($projection as $column) {
                $selectQuery .= "$column AS " . str_replace(".", "_", $column);
                if ($countProjection < count($projection) - 1) {
                    $selectQuery .= ", ";
                }
                $countProjection += 1;
            }
        }

        $this->currentQuery .= " FROM {$this->table}";

        $this->currentQuery .= $this->queryBuilder->query;

        if ($pageSize) {
            $this->currentQuery .= " LIMIT $pageSize";
        }

        if ($page) {
            $offset = ($page - 1) * $pageSize;
            $this->currentQuery .= " OFFSET $offset";
        }

        $selectStatement = $this->connection->prepare($selectQuery . $this->currentQuery);
        $selectStatement->execute();
        $pageCountStatement = $this->connection->prepare($pageCountQuery . $this->currentQuery);
        $pageCountStatement->execute();

        $this->reset();

        try {
            return [
                'items' => $selectStatement->fetchAll(),
                'page' => $page ?? 1,
                'totalPage' => $pageSize ? ceil($pageCountStatement->fetchColumn() / $pageSize) : 1
            ];
        } finally {
            $selectStatement->closeCursor();
        }
    }

    public function findOne($key, $value, $projection = [])
    {
        $this->currentQuery = "SELECT ";

        if (count($projection) === 0) {
            $this->currentQuery .= "*";
        } else {
            $countProjection = 0;
            foreach ($projection as $column) {
                $query .= "$column";
                if ($countProjection < count($projection) - 1) {
                    $query .= ", ";
                }
                $countProjection += 1;
            }
        }

        $this->currentQuery .= " FROM {$this->table} WHERE $key = :$key LIMIT 1";

        $statement = $this->connection->prepare($this->currentQuery);
        $statement->bindValue(":$key", $value);

        $statement->execute();

        $this->reset();

        try {
            if ($row = $statement->fetch()) {
                return $row;
            }
            return null;
        } finally {
            $statement->closeCursor();
        }
    }

    public function update(Domain $domain)
    {
        $domainKeyLength = count($domain->toArray());
        $this->currentQuery = "UPDATE {$this->table} SET ";
        $countKey = 0;
        foreach ($domain->toArray() as $key => $value) {
            $countKey += 1;

            $this->currentQuery .= "$key = :$key";
            if ($countKey < $domainKeyLength) {
                $this->currentQuery .= ", ";
            }
        }

        $this->currentQuery .= " WHERE id = :id";

        $statement = $this->connection->prepare($this->currentQuery);
        $array = $domain->toArray();
        foreach ($array as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        $statement->bindValue(":id", $array['id'], \PDO::PARAM_INT);
        $statement->execute();
        $this->reset();
        try {
            return $domain;
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM {$this->table}");
        $this->reset();
    }

    public function deleteBy($key, $value): void
    {
        $statement = $this->connection->prepare("DELETE FROM {$this->table} WHERE $key = :$key");
        $statement->bindValue(":$key", $value);
        $statement->execute();
        $this->reset();

        $statement->closeCursor();
    }
}