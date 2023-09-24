<?php

function getDatabaseConfig(): array
{
    return [
        "database" => [
            "dev" => [
                "url" => 'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_NAME') . ';',
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
            ],
            "prod" => [],
        ],
    ];
}
