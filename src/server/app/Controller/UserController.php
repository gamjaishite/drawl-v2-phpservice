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
require_once __DIR__ . '/../Model/session/SessionCreateRequest.php';

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
        ], $this->sessionService);
    }

    public function signIn(): void
    {
        View::render('user/signIn', [
            "title" => "Sign In",
            "styles" => [
                "/css/signIn.css",
            ],
        ], $this->sessionService);
    }

    public function profile(): void
    {
        View::render('user/editProfile', [
            'title' => 'Profile',
            'styles' => [
                '/css/editProfile.css',
            ],
            'data' => [
                'name' => 'Breezy',
                'email' => 'sampleemail@gmail.com'
            ],
        ], $this->sessionService);
    }

    public function postSignUp(): void
    {
        $request = new UserSignUpRequest();

        $request->email = $_POST['email'];
        $request->password = $_POST['password'];
        $request->confirmPassword = $_POST['passwordConfirm'];

        try {
            $this->userService->signUp($request);

            View::redirect('/signin');
        } catch (ValidationException $exception) {
            View::render('user/signUp', [
                'title' => 'Sign Up',
                'error' => $exception->getMessage(),
                'styles' => [
                    '/css/signUp.css',
                ],
            ], $this->sessionService);
        }
    }


    public function postSignIn(): void
    {
        $request = new UserSignInRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];

        try {
            $response = $this->userService->signIn($request);

            $sessionCreateRequest = new SessionCreateRequest();
            $sessionCreateRequest->userId = $response->user->id;

            $this->sessionService->create($sessionCreateRequest);

            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('user/signIn', [
                "title" => "Sign In",
                "data" => [
                    "email" => $request->email,
                ],
                "error" => $exception->getMessage(),
                "styles" => [
                    "/css/signIn.css",
                ],
            ], $this->sessionService);
        }
    }


    public function showEditProfile(): void
    {
        $currentUser = $this->sessionService->current();
        View::render('user/editProfile', [
            'title' => 'Drawl | Edit Profile',
            'styles' => [
                '/css/editProfile.css',
            ],
            'data' => ['name' => $currentUser->name, 'email' => $currentUser->email]
        ], $this->sessionService);
    }

    public function postEditProfile(): void
    {
        $request = new UserEditRequest();
        $request->name = $_POST['name'];
        $request->email = $_POST["email"] ?? null; // or from session
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        $currentUser = $this->sessionService->current();


        if (isset($_POST['update_button'])) {
            //update action
            try {
                $this->userService->update($currentUser, $request);
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
                        'name' => $currentUser->name,
                        'email' => $currentUser->email
                    ],
                ], $this->sessionService);
            }
        } else if (isset($_POST['delete_button'])) {
            //delete action
            $this->userService->deleteBySession($currentUser->email);
            $this->userService->deleteByEmail($currentUser->email);

            $this->sessionService->destroy();
            View::redirect('/signin');
        } else if (isset($_POST['logout_button'])) {
            $this->sessionService->destroy();
            View::redirect("/signin");
        }
    }
}
