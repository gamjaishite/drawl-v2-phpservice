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

    public function updateName(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET name = ? WHERE email = ?");
        $statement->execute([
            $user->name,
            $user->email,
        ]);
        return $user;
    }

    public function updatePassword(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET password = ? WHERE email = ?");
        $statement->execute([
            $user->password,
            $user->email,
        ]);
        return $user;
    }


    public function deleteBySession(string $email)
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE user_id IN
        (SELECT id FROM users WHERE email = ?)");
        $statement->execute([$email]);
        $statement->closeCursor();
    }
}
