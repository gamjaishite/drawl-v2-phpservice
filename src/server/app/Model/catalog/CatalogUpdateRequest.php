<?php

class CatalogUpdateRequest
{
    public string $uuid;
    public string $title;
    public string $description;
    public $poster = null;
    public $trailer = null;
    public string $category;
}