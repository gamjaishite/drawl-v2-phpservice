<?php

require_once __DIR__ . '/../Model/UserSignUpRequest.php';
require_once __DIR__ . '/../Model/UserSignUpResponse.php';
require_once __DIR__ . '/../Model/UserSignInRequest.php';
require_once __DIR__ . '/../Model/UserSignInResponse.php';
require_once __DIR__ . '/../Model/UserEditRequest.php';
require_once __DIR__ . '/../Model/UserEditResponse.php';

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Config/Database.php';

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function signUp(UserSignUpRequest $request): UserSignUpResponse
    {
        $this->validateUserSignUpRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findByEmail($request->email);
            if ($user != null) {
                throw new ValidationException("User already exist");
            }

            $user = new User();
            $user->name = explode("@", $request->email)[0];
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserSignUpResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserSignUpRequest(UserSignUpRequest $request)
    {
        if (
            $request->email == null | $request->password == null ||
            trim($request->email) == "" || trim($request->password) == ""
        ) {
            throw new ValidationException("Email and password cannot be blank");
        }

        // more validations goes here
        if ($request->password != $request->confirm_password) {
            throw new ValidationException("Make sure both passwords are typed the same");
        }
    }

    public function signIn(UserSignInRequest $request): UserSignInResponse
    {
        $this->validateUserSignInRequest($request);

        $user = $this->userRepository->findByEmail($request->email);
        if ($user == null) {
            throw new ValidationException("Email or password not valid");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserSignInResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Email or  password not valid");
        }
    }

    private function validateUserSignInRequest(UserSignInRequest $request)
    {
        if (
            $request->email == null || $request->password == null ||
            trim($request->email) == "" || trim($request->password) == ""
        ) {
            throw new ValidationException("Email and password cannot be blank");
        }

        // more validations goes here
    }

    public function update(string $email, UserEditRequest $request)
    {
    }

    public function delete(string $email)
    {
    }
}
