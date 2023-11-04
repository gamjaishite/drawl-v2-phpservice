<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/UUIDGenerator.php';

require_once __DIR__ . '/../Repository/UserRepository.php';

require_once __DIR__ . '/../Model/UserSignUpRequest.php';
require_once __DIR__ . '/../Model/UserSignUpResponse.php';
require_once __DIR__ . '/../Model/UserSignInRequest.php';
require_once __DIR__ . '/../Model/UserSignInResponse.php';
require_once __DIR__ . '/../Model/UserEditRequest.php';
require_once __DIR__ . '/../Model/UserEditResponse.php';

require_once __DIR__ . '/../Common/ValidationResult.php';
require_once __DIR__ . '/../Common/CustomResponse.php';


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
            $user->username = "user" . UUIDGenerator::uuid4();
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

    public function update(User $currentuser, UserEditRequest $request)
    {
        $this->validateEditProfileRequest($currentuser, $request);
        try {
            //code...
            Database::beginTransaction();

            if (!($request->name == null || trim($request->name == ""))) {
                $currentuser->name = trim($request->name);
                $this->userRepository->updateName($currentuser);
            }

            if (
                !($request->oldPassword == null || trim($request->oldPassword) == "")
                && !($request->newPassword == null || trim($request->newPassword) == "")
            ) {
                $currentuser->password = password_hash(trim($request->newPassword), PASSWORD_BCRYPT);
                $this->userRepository->updatePassword($currentuser);
            }
            Database::commitTransaction();
        } catch (\Exception $exception) {
            //throw $th;
            Database::rollbackTransaction();
            throw $exception;
        }
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

        if (strlen($request->password) < 8) {
            throw new ValidationException("Password is too short, minimum 8 characters");
        }

        if (strlen($request->password) > 255) {
            throw new ValidationException("Password is too long, maximum 255 characters");
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

    public function validateEditProfileRequest(User $currentuser, UserEditRequest $request)
    {
        if (
            ($request->oldPassword == null || trim($request->oldPassword) == "")
            && ($request->newPassword == null || trim($request->newPassword) == "")
            && ($request->name == null || trim($request->name == ""))
        ) {
            throw new ValidationException("Data cannot be empty.");
        } else if (
            !($request->name == null || trim($request->name == ""))
            && !($request->oldPassword == null || trim($request->oldPassword) == "")
            && ($request->newPassword == null || trim($request->newPassword) == "")
        ) {
            throw new ValidationException("New password cannot be blank.");
        } else if (
            !($request->name == null || trim($request->name == ""))
            && ($request->oldPassword == null || trim($request->oldPassword) == "")
            && !($request->newPassword == null || trim($request->newPassword) == "")
        ) {
            throw new ValidationException("Old password cannot be blank.");
        } else if (
            ($request->name == null || trim($request->name == ""))
            && !($request->oldPassword == null || trim($request->oldPassword) == "")
            && ($request->newPassword == null || trim($request->newPassword) == "")
        ) {
            throw new ValidationException("New password cannot be blank.");
        } else if (
            ($request->name == null || trim($request->name == ""))
            && ($request->oldPassword == null || trim($request->oldPassword) == "")
            && !($request->newPassword == null || trim($request->newPassword) == "")
        ) {
            throw new ValidationException("Old password cannot be blank.");
        }

        if (
            (!($request->oldPassword == null || trim($request->oldPassword) == "")
                && !($request->newPassword == null || trim($request->newPassword) == "")) &&
            ($request->oldPassword == $request->newPassword)
        ) {
            throw new ValidationException("New password cannot be the same as old password.");
        }

        if (
            !($request->oldPassword == null || trim($request->oldPassword) == "") &&
            !password_verify($request->oldPassword, $currentuser->password)
        ) {
            throw new ValidationException("Old password is incorrect.");
        }
    }

    public function findByEmail(string $email): User
    {
        return $this->userRepository->findOne('email', $email);
    }

    public function deleteByEmail(string $email)
    {
        $this->userRepository->deleteBy('email', $email);
    }

    public function deleteBySession(string $email)
    {
        $this->userRepository->deleteBySession($email);
    }


    // V2 Methods
    public function signInV2(SignInV2Request $request): CustomResponse
    {
        $response = new CustomResponse();

        $validateResult = $this->validateSignInV2($request);
        if (!$validateResult->success) {
            $response->status = 400;
            $response->message = $validateResult->message;

            http_response_code($response->status);

            return $response;
        }

        $user = $this->userRepository->findOne('email', $request->email);
        if ($user == null) {
            $response->status = 400;
            $response->message = "Invalid email or password";

            http_response_code($response->status);

            return $response;
        }

        if (!password_verify($request->password, $user->password)) {
            $response->status = 400;
            $response->message = "Invalid email or password";

            http_response_code($response->status);

            return $response;
        }

        $response->status = 200;
        $response->message = "Sign In Success";
        $response->data = [
            "uuid" => $user->uuid,
            "email" => $user->email,
            "name" => $user->name,
            "username" => $user->username,
            "verified" => $user->verified,
            "blocked" => $user->blocked,
            "blockedUntil" => $user->blockedUntil
        ];

        http_response_code($response->status);

        return $response;
    }

    public function getUserInfo(GetUserInfoRequest $request): CustomResponse
    {
        $response = new CustomResponse();

        $validateResult = $this->validateGetUserInfo($request);
        if (!$validateResult->success) {
            $response->status = 400;
            $response->message = $validateResult->message;

            http_response_code($response->status);

            return $response;
        }

        $user = $this->userRepository->findOne('uuid', $request->userId);

        if ($user == null) {
            $response->status = 404;
            $response->message = "User not found";

            http_response_code($response->status);

            return $response;
        }

        $response->status = 200;
        $response->message = "Success";
        $response->data = [
            "uuid" => $user->uuid,
            "email" => $user->email,
            "name" => $user->name,
            "username" => $user->username,
            "verified" => $user->verified,
            "blocked" => $user->blocked,
            "blockedUntil" => $user->blockedUntil
        ];

        http_response_code($response->status);

        return $response;
    }

    private function validateSignInV2(SignInV2Request $request): ValidationResult
    {
        $result = new ValidationResult();
        if ($request->email == null || trim($request->email) == "") {
            $result->success = false;
            $result->message = "Email is required";
            return $result;
        }
        if ($request->password == null || trim($request->password) == "") {
            $result->success = false;
            $result->message = "Password is required";
            return $result;
        }

        $result->success = true;
        $result->message = "";

        return $result;
    }

    private function validateGetUserInfo(GetUserInfoRequest $request): ValidationResult
    {
        $result = new ValidationResult();

        if ($request->userId == null || trim($request->userId) == "") {
            $result->success = false;
            $result->message = "User ID is required";
            return $result;
        }

        $result->success = true;
        $result->message = "";

        return $result;
    }
}