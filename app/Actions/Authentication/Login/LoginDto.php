<?php

namespace App\Actions\Authentication\Login;

readonly class LoginDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
