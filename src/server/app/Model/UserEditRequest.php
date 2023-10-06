<?php

class UserEditRequest
{
    public ?string $email = null;
    public ?string $name = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;
}
