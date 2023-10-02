<?php

require_once __DIR__ . '/../App/Repository.php';

class WatchlistRepository extends Repository
{
    public function __construct(\PDO $connection)
    {
        parent::__construct($connection);
    }
}
