<?php

require_once __DIR__ . '/../App/Domain.php';

class Session extends Domain
{
    public int|string $id;
    public string $userId;
    public string $expired;
    public string $createdAt;
    public string $updatedAt;

    public function toArray(): array
    {
        $array = [
            "id" => $this->id,
            "user_id" => $this->userId,
            "expired" => $this->expired
        ];

        if (isset($this->createdAt)) {
            $array["created_at"] = $this->createdAt;
        }

        if (isset($this->updatedAt)) {
            $array["updated_at"] = $this->updatedAt;
        }

        return $array;
    }

    public function fromArray(array $data)
    {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }

        if (isset($data["user_id"])) {
            $this->userId = $data["user_id"];
        }

        if (isset($data["expired"])) {
            $this->expired = $data["expired"];
        }

        if (isset($data["created_at"])) {
            $this->createdAt = $data["created_at"];
        }

        if (isset($data["updated_at"])) {
            $this->updatedAt = $data["updated_at"];
        }
    }
}
