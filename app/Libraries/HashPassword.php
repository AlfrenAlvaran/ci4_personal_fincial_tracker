<?php
namespace App\Libraries;
class HashPassword
{
    public static function make(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function check(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT);
    }
}