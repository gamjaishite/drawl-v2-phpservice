<?php

require_once __DIR__ . '/../App/Domain.php';

class User extends Domain
{
    public int|string $id;
    public string $uuid;
    public string $name;
    public string $username;
    public bool $verified;
    public bool $blocked;
    public ?string $blockedUntil;
    public string $password;
    public string $email;
    public string $role;
    public string $createdAt;
    public string $updatedAt;

    public function toArray(): array
    {
        $array = [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "password" => $this->password,
            "email" => $this->email,
            "username" => $this->username,
        ];

        if (isset($this->id)) {
            $array["id"] = $this->id;
        }

        if (isset($this->role)) {
            $array["role"] = $this->role;
        }

        if (isset($this->verified)) {
            $array["verified"] = $this->verified;
        }

        if (isset($this->blocked)) {
            $array["blocked"] = $this->blocked;
        }

        if (isset($this->blockedUntil)) {
            $array["blocked_until"] = $this->blockedUntil;
        }

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

        if (isset($data["uuid"])) {
            $this->uuid = $data["uuid"];
        }

        if (isset($data["name"])) {
            $this->name = $data["name"];
        }

        if (isset($data["username"])) {
            $this->username = $data["username"];
        }

        if (isset($data["password"])) {
            $this->password = $data["password"];
        }

        if (isset($data["email"])) {
            $this->email = $data["email"];
        }

        if (isset($data["role"])) {
            $this->role = $data["role"];
        }

        if (isset($data["verified"])) {
            $this->verified = $data["verified"];
        }

        if (isset($data["blocked"])) {
            $this->blocked = $data["blocked"];
        }


        if (isset($data["created_at"])) {
            $this->createdAt = $data["created_at"];
        }

        if (isset($data["updated_at"])) {
            $this->updatedAt = $data["updated_at"];
        }
        
        $this->blockedUntil = $data["blocked_until"];
    }
}