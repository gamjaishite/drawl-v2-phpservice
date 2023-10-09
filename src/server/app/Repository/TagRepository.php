<?php

require_once __DIR__ . '/../App/Repository.php';

require_once __DIR__ . '/../Domain/Tag.php';

class TagRepository extends Repository
{
    protected string $table = "tags";

    public function __construct(PDO $connection)
    {
        parent::__construct($connection);
    }

    public function findAll(array $projection = [], int|null $page = null, int|null $pageSize = null): array
    {
        $result = parent::findAll($projection, $page, $pageSize);

        $result['items'] = array_map(
            function ($row) {
                $tags = new Tag();
                $tags->fromArray($row);
                return $tags;
            },
            $result['items']
        );
        return $result;
    }

    public function findOne($key, $value, $projection = [])
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
}