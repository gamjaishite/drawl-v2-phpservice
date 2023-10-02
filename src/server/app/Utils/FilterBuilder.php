<?php

class FilterBuilder
{
    public string $filterQuery;

    public function __construct()
    {
        $this->filterQuery = "";
    }

    public function whereEquals(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " WHERE $key = '$value'";
        return $this;
    }

    public function whereContains(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " WHERE $key ILIKE '%$value%'";
        return $this;
    }

    public function andWhereEquals(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " AND ";
        return $this->whereEquals($key, $value);
    }

    public function andWhereContains(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " AND ";
        return $this->whereContains($key, $value);
    }

    public function orWhereEquals(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " OR ";
        return $this->whereEquals($key, $value);
    }

    public function orWhereContains(string $key, $value): FilterBuilder
    {
        $this->filterQuery .= " OR ";
        return $this->whereContains($key, $value);
    }
}
