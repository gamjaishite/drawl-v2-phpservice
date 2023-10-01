<?php

class UUIDGenerator
{
    public static function uuid(int $half = 16)
    {
        return bin2hex(random_bytes($half));
    }

    public static function uuid4(): string
    {
        return vsprintf('%s%s%s%s', str_split(self::uuid(8), 4));
    }
}
