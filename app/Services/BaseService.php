<?php

namespace App\Services;

abstract class BaseService
{
    protected function userId(): int
    {
        return AuthContext::id();
    }
}