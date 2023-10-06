<?php
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';

class ErrorPageController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function fourohfour(): void
    {
        View::render('404', [
            'title' => '404',
            'styles' => [
                '/css/error-page.css',
            ],
        ], $this->sessionService);
    }

    public function fivehundred(): void
    {
        View::render('500', [
            'title' => '500',
            'styles' => [
                '/css/error-page.css',
            ],
        ], $this->sessionService);
    }
}