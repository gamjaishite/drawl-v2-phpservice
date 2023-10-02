<?php

require_once __DIR__ . '/../App/View.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Repository/SessionRepository.php';

require_once __DIR__ . '/../Service/UserService.php';
require_once __DIR__ . '/../Service/SessionService.php';

require_once __DIR__ . '/../Model/UserSignUpRequest.php';
require_once __DIR__ . '/../Model/UserSignInRequest.php';
class UserController
{
    private UserService $userService;
    private SessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function signUp()
    {
        View::render('user/signUp', [
            'title' => 'Sign Up',
            'styles' => [
                '/css/signUp.css',
            ],

        ]);
    }



    public function showEditProfile()
    {
        View::render('user/editProfile', [
            'title' => 'Drawl | Edit Profile',
            'styles' => [
                '/css/editProfile.css',
            ],
            'data' => [
                'name' => 'Breezy',
                'email' => 'sampleemail@gmail.com'
            ],
        ]);
    }

    public function postSignUp()
    {
        $request = new UserSignUpRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];

        try {
            $this->userService->signUp($request);
            // redirect to login
            View::redirect('/signin');
        } catch (ValidationException $exception) {
            View::render('user/signUp', [
                'title' => 'Sign Up',
                'error' => $exception->getMessage(),
                'styles' => [
                    '/css/signUp.css',
                ],
            ]);
        }
    }

    public function signIn()
    {
        View::render('user/signIn', [
            "title" => "Sign In",
            "styles" => [
                "/css/signIn.css",
            ],
        ]);
    }

    public function postSignIn()
    {
        $request = new UserSignInRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->signIn($request);
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('user/signIn', [
                "title" => "Sign In",
                "error" => $exception->getMessage(),
                "styles" => [
                    "/css/signIn.css",
                ],
            ]);
        }
    }
}
