<?php

namespace App\Actions\User\Register;

readonly class CreateUserDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
