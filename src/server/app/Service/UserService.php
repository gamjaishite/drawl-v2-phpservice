<?php

require_once __DIR__ . '/../Model/UserRegisterRequest.php';
require_once __DIR__ . '/../Model/UserRegisterResponse.php';
require_once __DIR__ . '/../Model/UserSignInRequest.php';
require_once __DIR__ . '/../Model/UserSignInResponse.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Config/Database.php';

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user != null) {
                throw new ValidationException("User already exist");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }

    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null | $request->password == null ||
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, name, password cannot be blank");
        }

        // more validations goes here
    }

    public function signIn(UserSignInRequest $request): UserSignInResponse
    {
        $this->validateUserSignInRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password not valid");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserSignInResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Id or password not valid");
        }
    }

    private function validateUserSignInRequest(UserSignInRequest $request)
    {
        if ($request->id == null || $request->password == null ||
            trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id and password cannot be blank");
        }

        // more validations goes here
    }
}
