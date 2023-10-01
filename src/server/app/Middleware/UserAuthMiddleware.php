<?php

require_once __DIR__ . '/../App/Middleware.php';
require_once __DIR__ . '/../Service/SessionService.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/UserRepository.php';

class UserAuthMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $userRepository = new UserRepository(Database::getConnection());
        $sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function run(): void
    {
        $user = $this->sessionService->current();
        if (!isset($user)) {
            header("Location: /signin");
            exit();
        }
    }
}
