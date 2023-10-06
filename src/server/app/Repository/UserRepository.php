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

    // Bisa lansung pakai method findOne
    public function findById(int $id): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, password, email, role FROM users WHERE id = ?");
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                $user->email = $row['email'];
                $user->role = $row['role'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    // Bisa lansung pakai method findOne
    public function findByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare("SELECT id, name, password, email, role FROM users WHERE email = ?");
        $statement->execute([$email]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                $user->email = $row['email'];
                $user->role = $row['role'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    // Bisa lansung pakai method delete yang ada di repository
    public function deleteByUUID(string $UUID): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE uuid = ?");
        $statement->execute([$UUID]);
        $statement->closeCursor();
    }

    public function deleteByEmail(string $email): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE email = ?");
        $statement->execute([$email]);
        $statement->closeCursor();
    }

    // Bisa langsung pakai method delete yang ada di repository
    public function deleteBySession(string $email)
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE user_id IN
        (SELECT id FROM users WHERE email = ?)");
        $statement->execute([$email]);
        $statement->closeCursor();
    }
}
