<?php

require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Model/UserSignUpRequest.php';
require_once __DIR__ . '/../Model/UserSignUpResponse.php';
require_once __DIR__ . '/../Model/UserSignInRequest.php';
require_once __DIR__ . '/../Model/UserSignInResponse.php';
require_once __DIR__ . '/../Model/UserEditRequest.php';
require_once __DIR__ . '/../Model/UserEditResponse.php';


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

            $user = $this->userRepository->findOne("email", $request->email);
            if (isset($user)) {
                throw new ValidationException("User already exist");
            }

            $request->email = trim($request->email);
            $request->password = trim($request->password);
            $request->confirmPassword = trim($request->confirmPassword);

            $user = new User();
            $user->uuid = UUIDGenerator::uuid4();
            $user->name = explode("@", $request->email)[0];
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserSignUpResponse();
            $response->user = $user;

            Database::commitTransaction();

            return $response;
        } catch (Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function signIn(UserSignInRequest $request): UserSignInResponse
    {
        $this->validateUserSignInRequest($request);

        $user = $this->userRepository->findOne("email", $request->email);
        if ($user == null) {
            throw new ValidationException("Invalid email or password.");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserSignInResponse();
            $response->user = $user;

            return $response;
        } else {
            throw new ValidationException("Invalid email or password.");
        }
    }

    public function updateProfile(UserEditRequest $request): void
    {
        $this->validateUpdateProfileRequest($request);

        try {
            //code...
            Database::beginTransaction();
            $user = $this->userRepository->findOne("email", $request->email);

            $user->name = trim($request->name);
            $user->password = trim($request->newPassword);

            $this->userRepository->update($user);
        } catch (\Exception $exception) {
            //throw $th;
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function findByEmail(string $email): User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function delete(string $email)
    {
        $this->userRepository->deleteByEmail($email);
    }

    private function validateUserSignUpRequest(UserSignUpRequest $request)
    {
        if (
            $request->email == null ||
            $request->password == null ||
            $request->confirmPassword == null ||
            trim($request->email) == "" ||
            trim($request->password) == "" ||
            trim($request->confirmPassword) == ""
        ) {
            throw new ValidationException("Email, password, and confirm password cannot be blank.");
        }

        if ($request->password != $request->confirmPassword) {
            throw new ValidationException("Make sure both passwords are typed the same.");
        }
    }

    private function validateUserSignInRequest(UserSignInRequest $request)
    {

        if (
            $request->email == null ||
            $request->password == null ||
            trim($request->email) == "" ||
            trim($request->password) == ""
        ) {
            throw new ValidationException("Email and password cannot be blank.");
        }
    }

    public function validateUpdateProfileRequest(UserEditRequest $request)
    {
        if (
            ($request->oldPassword != null && trim($request->oldPassword) != "" && ($request->newPassword == null || trim($request->newPassword) == "")) ||
            ($request->newPassword != null && trim($request->newPassword) != "" && ($request->oldPassword == null || trim($request->oldPassword) == ""))
        ) {
            throw new ValidationException("To change password, please provide old and new password.");
        }

        if (
            $request->oldPassword == $request->newPassword
        ) {
            throw new ValidationException("New password cannot be the same as old password.");
        }

        if ($request->email == null || trim($request->email) == "") {
            throw new ValidationException("Email cannot be blank.");
        }
    }
}
