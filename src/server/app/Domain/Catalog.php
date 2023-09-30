<?php

class Catalog
{
    public int $id;
    public string $uuid;
    public string $title;
    public ?string $description = null;
    public string $poster;
    public ?string $trailer = null;
    public string $category;
}