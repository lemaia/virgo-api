<?php

namespace App\Actions\Authentication\Login;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    public function execute(LoginDto $dto): array
    {
        $user = User::with('assets')->where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        $token = explode('|', $user->createToken('api-token')->plainTextToken)[1];

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
