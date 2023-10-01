<?php

class UserSignUpRequest
{
    public ?string $name = null;
    public ?string $password = null;
    public ?string $confirm_password = null;
    public ?string $email = null;
    public ?string $role = null;
}
