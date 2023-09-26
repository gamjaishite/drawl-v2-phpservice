<?php

class UserRegisterRequest
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $password = null;
    public ?string $confirm_password = null;
    public ?string $email = null;
    public ?string $role = null;
}