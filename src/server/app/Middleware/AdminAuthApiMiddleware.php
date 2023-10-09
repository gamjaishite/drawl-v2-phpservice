<?php

require_once __DIR__ . '/../App/Middleware.php';
require_once __DIR__ . '/../Service/SessionService.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';

class AdminAuthApiMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $userRepository = new UserRepository($connection);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function run(): void
    {
        $user = $this->sessionService->current();

        if ($user == null || $user->role != "ADMIN") {
            http_response_code(401);
            $array = [
                "status" => 401,
                "message" => "Unauthorized",
            ];
            echo json_encode($array);
            exit();
        }
    }
}