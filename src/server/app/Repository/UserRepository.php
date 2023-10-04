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
        $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ?");
        $statement->execute([
            $user->name,
            $user->password
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

    public function deleteByEmail(string $email): void
    {
        $statement = $this->connection->prepare("DELETE FROM users WHERE email = ?");
        $statement->execute([$email]);
        $statement->closeCursor();
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}
