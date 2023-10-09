<?php

require_once __DIR__ . '/../App/Repository.php';

class WatchlistTagRepository extends Repository
{
    protected string $table = "watchlist_tag";

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }
}