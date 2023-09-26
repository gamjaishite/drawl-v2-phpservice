<?php

class Database
{
    private static ?\PDO $pdo = null;

    public static function getConnection(string $env = 'dev'): \PDO
    {
        if (self::$pdo == null) {
            try {
                // create new POD
                require_once __DIR__ . '/../../config/database.php';
                $config = getDatabaseConfig();
                self::$pdo = new \PDO(
                    $config['database'][$env]['url'],
                    $config['database'][$env]['username'],
                    $config['database'][$env]['password']
                );
            } catch (\PDOException $e) {
                // if ($e->getCode() == 7 && $env == 'dev') {
                //     self::$pdo = new \PDO(
                //         $config['database'][$env]['host_url'],
                //         $config['database'][$env]['username'],
                //         $config['database'][$env]['password']
                //     );

                //     $statement = self::$pdo->prepare("CREATE DATABASE " . $config['database'][$env]['name']);
                //     $statement->execute();
                // } else {
                throw $e;
                // }
            }
        }
        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollback();
    }
}