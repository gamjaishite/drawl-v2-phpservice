<?php

abstract class Domain
{
    public int|string $id;
    public string $table;
    public function __construct()
    {
        $this->table = get_class($this) . 's';
    }

    abstract public function toArray(): array;
    abstract public function fromArray(array $data);
}