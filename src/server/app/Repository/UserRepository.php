<?php

require_once __DIR__ . '/../App/Repository.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Domain/User.php';

class UserRepository extends Repository
{
    protected string $table = "users";

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findOne($key, $value, $projection = []): ?User
    {
        $result = parent::findOne($key, $value, $projection);

        if ($result != null) {
            $user = new User();
            $user->fromArray($result);
            
            return $user;
        } else {
            return null;
        }
    }

    // Bisa langsung pake function yang ada di Repository.php
    public function deleteByEmail(string $email): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE email = ?");
        $statement->execute([$email]);
        $statement->closeCursor();
    }
}
