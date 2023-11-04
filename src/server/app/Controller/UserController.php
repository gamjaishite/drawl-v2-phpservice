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
require_once __DIR__ . '/../Model/user/SignInV2Request.php';
require_once __DIR__ . '/../Model/user/GetUserInfoRequest.php';

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
        $redirectTo = $_GET['redirect_to'] ?? null;

        View::render('user/signUp', [
            'title' => 'Sign Up',
            'redirectTo' => $redirectTo,
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

        $redirectTo = $_GET["redirect_to"] ?? null;

        try {
            $this->userService->signUp($request);

            View::redirect($redirectTo ?? '/signin');
        } catch (ValidationException $exception) {
            View::render('user/signUp', [
                'title' => 'Sign Up',
                'error' => $exception->getMessage(),
                'redirectTo' => $redirectTo,
                'styles' => [
                    '/css/signUp.css',
                ],
                'data' => [
                    'email' => $request->email,
                    'password' => $request->password,
                    'confirmPassword' => $request->confirmPassword
                ]
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
            'js' => [
                '/js/profile.js'
            ],
            'data' => ['name' => $currentUser->name, 'email' => $currentUser->email]
        ], $this->sessionService);
    }

    public function logout(): void
    {
        try {
            $this->sessionService->destroy();
            http_response_code(200);
            $response = [
                "status" => 200,
                "message" => "Logout success.",
            ];
        } catch (ValidationException $exception) {
            http_response_code(400);
            $response = [
                "status" => 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
    }

    public function postEditProfile(): void
    {
        $request = new UserEditRequest();
        $request->name = $_POST['name'];
        $request->email = $_POST["email"] ?? null; // or from session
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        $currentUser = $this->sessionService->current();

        try {
            $this->userService->update($currentUser, $request);
            View::redirect('/profile');
        } catch (ValidationException $exception) {
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
    }

    public function update(): void
    {
        $request = new UserEditRequest();

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if ($data === null) {
            http_response_code(400);
            $response = [
                "status" => 400,
                "message" => "Invalid request.",
            ];

            echo json_encode($response);
            return;
        }


        $request->name = $data->name;
        $request->oldPassword = $data->oldPassword;
        $request->newPassword = $data->newPassword;

        $currentUser = $this->sessionService->current();

        try {
            $this->userService->update($currentUser, $request);
            http_response_code(200);
            $response = [
                "status" => 200,
                "message" => "Successfully update user",
                "name" => $request->name,
            ];

            echo json_encode($response);
        } catch (ValidationException $exception) {
            http_response_code($exception->getCode() ?? 400);

            $response = [
                "status" => $exception->getCode() ?? 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
    }

    public function delete(): void
    {
        $currentUser = $this->sessionService->current();

        try {

            if (!$currentUser) {
                throw new ValidationException("Unauthorized.", 401);
            }
            $this->userService->deleteBySession($currentUser->email);
            $this->userService->deleteByEmail($currentUser->email);
            http_response_code(200);

            $response = [
                "status" => 200,
                "message" => "Successfully delete user",
            ];

            echo json_encode($response);
        } catch (ValidationException $exception) {
            http_response_code($exception->getCode() ?? 400);

            $response = [
                "status" => $exception->getCode() ?? 400,
                "message" => $exception->getMessage(),
            ];

            echo json_encode($response);
        } catch (\Exception $exception) {
            http_response_code(500);
            $response = [
                "status" => 500,
                "message" => "Something went wrong.",
            ];

            echo json_encode($response);
        }
    }

    // V2 Methods
    public function signInV2(): void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $request = new SignInV2Request();
        $request->email = $data->email ?? "";
        $request->password = $data->password ?? "";

        $response = $this->userService->signInV2($request);

        echo json_encode($response);
    }

    public function getUserInfo(): void
    {
        $userId = $_GET["userId"] ?? null;

        $request = new GetUserInfoRequest();
        $request->userId = $userId;

        $response = $this->userService->getUserInfo($request);

        echo json_encode($response);
    }
}