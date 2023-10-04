<?php

require_once __DIR__ . '/../App/Repository.php';


class QueryBuilder
{
    public string $query;
    protected Repository $repository;
    public function __construct(Repository $repository)
    {
        $this->query = "";
        $this->repository = $repository;
    }

    public function whereEquals(string $key, $value): QueryBuilder
    {
        if (is_int($value))
            $this->query .= " WHERE $key = $value";
        else if (is_string($value))
            $this->query .= " WHERE $key = '$value'";
        return $this;
    }

    public function whereContains(string $key, $value): QueryBuilder
    {
        $this->query .= " WHERE $key LIKE '%$value%'";
        return $this;
    }

    public function andWhereEquals(string $key, $value): QueryBuilder
    {
        $this->query .= " AND ";
        return $this->whereEquals($key, $value);
    }

    public function andWhereContains(string $key, $value): QueryBuilder
    {
        $this->query .= " AND ";
        return $this->whereContains($key, $value);
    }

    public function orWhereEquals(string $key, $value): QueryBuilder
    {
        $this->query .= " OR ";
        return $this->whereEquals($key, $value);
    }

    public function orWhereContains(string $key, $value): QueryBuilder
    {
        $this->query .= " OR ";
        return $this->whereContains($key, $value);
    }

    public function join(string $foreignKey, string $table, string $key): QueryBuilder
    {
        $this->query .= " JOIN $table ON " . $this->repository->getTable() . ".$foreignKey = $table.$key";
        return $this;
    }

    public function get(
        array $projection = [],
        int $page = null,
        int $pageSize = null
    ) {
        return $this->repository->findAll($projection, $page, $pageSize);
    }

}