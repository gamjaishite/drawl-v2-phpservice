<?php
require_once __DIR__ . '/../App/Repository.php';
require_once __DIR__ . '/../App/Domain.php';

require_once __DIR__ . '/../Domain/Session.php';

class SessionRepository extends Repository
{
    protected string $table = "sessions";

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findOne($key, $value, $projection = []): ?Session
    {
        $result = parent::findOne($key, $value, $projection);

        if ($result != null) {
            $session = new Session();
            $session->fromArray($result);

            return $session;
        } else {
            return null;
        }
    }
}