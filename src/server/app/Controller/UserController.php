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
require_once __DIR__ . '/../Model/UserEditRequest.php';

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

    public function signUp(): void
    {
        View::render('user/signUp', [
            'title' => 'Sign Up',
            'styles' => [
                '/css/signUp.css',
            ],

        ]);
    }


    //showEditProfile($email)
    // $user = $this->userService->findByEmail($email);
    // 'data' => [$user->name, $user->email]
    public function showEditProfile(): void
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

    public function postSignUp(): void
    {
        $request = new UserSignUpRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];
        $request->confirm_password = $_POST['passwordConfirm'];

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

    public function signIn(): void
    {
        View::render('user/signIn', [
            "title" => "Sign In",
            "styles" => [
                "/css/signIn.css",
            ],
        ]);
    }

    public function postSignIn(): void
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

    public function postEditProfile(string $email): void
    {
        $request = new UserEditRequest();
        $request->name = $_POST['name'];
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->update($email, $request);
            View::redirect('/editProfile');
        } catch (ValidationException $exception) {
            //throw $th;
            View::render('user/editProfile', [
                'title' => 'Drawl | Edit Profile',
                'error' => $exception->getMessage(),
                'styles' => [
                    '/css/editProfile.css',
                ],
                'data' => [
                    'name' => $request->name,
                    'email' => $email
                ],
            ]);
        }
    }

    public function postDeleteProfile(string $email): void
    {
        $this->userService->delete($email);
        View::redirect('/signin');
    }
}
