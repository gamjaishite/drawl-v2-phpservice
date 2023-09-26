<?php

require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Service/UserService.php';
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Model/UserRegisterRequest.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function signUp()
    {
        View::render('user/signUp', [
            'title' => 'Drawl | Sign Up',
            'styles' => [
                './css/signUp.css',
            ],
        ]);
    }

    public function postSignUp()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            // redirect to login
            View::redirect('/signIn');
        } catch (ValidationException $exception) {
            View::render('user/signUp', [
                'title' => 'Drawl | Sign Up',
                'error' => $exception->getMessage(),
                'styles' => [
                    './css/signUp.css',
                ],
            ]);
        }
    }
}
