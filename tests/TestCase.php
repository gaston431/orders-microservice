<?php

namespace Tests;

use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getAdminToken(): string
    {
        return JWT::encode([
            'sub' => 1,
            'role' => 'admin',
            'exp' => time() + 3600,
            'name' => 'Admin Usuario',
            'email' => 'admin@example.com',
        ], env('JWT_SECRET'), 'HS256');
    }
}
