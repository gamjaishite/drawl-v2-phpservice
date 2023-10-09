<?php

require_once __DIR__ . '/../App/Domain.php';

class Tag extends Domain
{
    public int|string $id;
    public string $name;
    public string $createdAt;

    public function toArray(): array
    {
        $array = [
            "name" => $this->name,
        ];

        if (isset($this->id)) {
            $array["id"] = $this->id;
        }

        if (isset($this->createdAt)) {
            $array["created_at"] = $this->createdAt;
        }

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["name"])) {
            $this->name = $data["name"];
        }

        if (isset($data["created_at"])) {
            $this->createdAt = $data["created_at"];
        }
    }
}