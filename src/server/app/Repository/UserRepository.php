<?php

require_once __DIR__ . '/../Domain/User.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO users(uuid, name, password, email) VALUES (?, ?, ?, ?)");
        $statement->execute([
            UUIDGenerator::uuid4(),
            $user->name,
            $user->password,
            $user->email,
        ]);
        return $user;
    }

    public function update(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE email = ?");
        $statement->execute([
            $user->name,
            $user->password,
            $user->email,
        ]);
        return $user;
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

    public function deleteBySession(string $email)
    {
        $statement = $this->connection->prepare("DELETE FROM sessions WHERE user_id IN
        (SELECT id FROM users WHERE email = ?)");
        $statement->execute([$email]);
        $statement->closeCursor();
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}
