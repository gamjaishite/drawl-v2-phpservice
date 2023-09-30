<?php

require_once __DIR__ . '/../App/Domain.php';

class Catalog extends Domain
{
    public int $id;
    public string $uuid;
    public string $title;
    public ?string $description = null;
    public string $poster;
    public ?string $trailer = null;
    public string $category;

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'poster' => $this->poster,
            'trailer' => $this->trailer,
            'category' => $this->category
        ];
    }

    public function fromArray(array $data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }

        if (isset($data['uuid'])) {
            $this->uuid = $data['uuid'];
        }

        if (isset($data['title'])) {
            $this->title = $data['title'];
        }

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        if (isset($data['poster'])) {
            $this->poster = $data['poster'];
        }

        if (isset($data['trailer'])) {
            $this->trailer = $data['trailer'];
        }

        if (isset($data['category'])) {
            $this->category = $data['category'];
        }
    }
}