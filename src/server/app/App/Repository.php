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

    public function save(Domain $domain)
    {
        $array = $domain->toArray();
        $domainKeyLength = count($array);
        $query = "INSERT INTO {$this->table} (";

        $countKey = 0;
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $query .= "$key";
            }

            if ($countKey < $domainKeyLength - 1) {
                $query .= ", ";
            }

            $countKey += 1;
        }

        $query .= ") VALUES (";
        $countKey = 0;
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $query .= ":$key";
            }

            if ($countKey < $domainKeyLength - 1) {
                $query .= ", ";
            }

            $countKey += 1;
        }

        $query .= ")";
        $statement = $this->connection->prepare($query);
        foreach ($array as $key => $value) {
            if ($key != 'id') {
                $statement->bindValue(":$key", $value);
            }
        }

        $statement->execute();

        try {
            $domain->id = $this->connection->lastInsertId();
            return $domain;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAll(
        array $projection = [],
        int $page = 1,
        int $pageSize = 10
    ): array {
        $query = "";
        $selectQuery = "SELECT ";
        $pageCountQuery = "SELECT COUNT(*) ";

        if (count($projection) === 0) {
            $selectQuery .= "*";
        } else {
            $countProjection = 0;
            foreach ($projection as $column) {
                $selectQuery .= "$column";
                if ($countProjection < count($projection) - 1) {
                    $selectQuery .= ", ";
                }
                $countProjection += 1;
            }
        }

        $query .= " FROM {$this->table}";

        $query .= $this->queryBuilder->query;

        if ($pageSize) {
            $query .= " LIMIT $pageSize";
        }

        if ($page) {
            $offset = ($page - 1) * $pageSize;
            $query .= " OFFSET $offset";
        }

        $selectStatement = $this->connection->prepare($selectQuery . $query);
        $selectStatement->execute();

        $pageCountStatement = $this->connection->prepare($pageCountQuery . $query);
        $pageCountStatement->execute();

        try {
            return [
                'items' => $selectStatement->fetchAll(),
                'page' => $page,
                'totalPage' => ceil($pageCountStatement->fetchColumn() / $pageSize)
            ];
        } finally {
            $selectStatement->closeCursor();
        }
    }

    public function findOne($key, $value, $projection = [])
    {
        $query = "SELECT ";

        if (count($projection) === 0) {
            $query .= "*";
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

        $query .= " FROM {$this->table} WHERE $key = :$key LIMIT 1";

        $statement = $this->connection->prepare($query);
        $statement->bindValue(":$key", $value);

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

    public function update(Domain $domain)
    {
        $domainKeyLength = count($domain->toArray());
        $query = "UPDATE {$this->table} SET ";
        $countKey = 0;
        foreach ($domain->toArray() as $key => $value) {
            $countKey += 1;

            if ($key != 'id') {
                $query .= "$key = :$key";
                if ($countKey < $domainKeyLength - 1) {
                    $query .= ", ";
                }
            }
        }

        $query .= " WHERE id = :id";

        $statement = $this->connection->prepare($query);
        $array = $domain->toArray();
        foreach ($array as $key => $value) {
            if ($key != "id") {
                $statement->bindValue(":$key", $value);
            }
        }
        $statement->bindValue(":id", $array['id'], \PDO::PARAM_INT);
        $statement->execute();

        try {
            return $domain;
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM {$this->table}");
    }

    public function deleteBy($key, $value): void
    {
        $statement = $this->connection->prepare("DELETE FROM {$this->table} WHERE $key = :$key");
        $statement->bindValue(":$key", $value);
        $statement->execute();

        $statement->closeCursor();
    }
}