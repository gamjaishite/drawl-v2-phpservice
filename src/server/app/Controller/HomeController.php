<?php

require_once __DIR__ . '/../App/Controller.php';
require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';

require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';

class HomeController
{
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();

        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function index(): void
    {
        View::render('home/index', [
            'title' => 'Drawl | Homepage',
            'styles' => [
                '/css/home.css',
            ],
            'js' => [
                '/js/home.js',
            ],
        ]);
    }
}
