<?php

namespace App\Services;

class AuthContext
{
    public static function id(): int
    {
        return session()->get('user_id');
    }
}