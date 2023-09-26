<?php

class Catalog
{
    public int $id;
    public string $title;
    public ?string $description = null;
    public string $poster;
    public ?string $trailer = null;
    public string $category;

    public function __toString()
    {
        return "Catalog: {id: $this->id, title: $this->title, description: $this->description, poster: $this->poster, trailer: $this->trailer, category: $this->category}";
    }
}