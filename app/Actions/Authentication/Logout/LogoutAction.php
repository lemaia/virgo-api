<?php

namespace App\Actions\Authentication\Logout;

use App\Models\User;

class LogoutAction
{
    public function execute(User $user, bool $allDevices = true): void
    {
        if ($allDevices) {
            $user->tokens()->delete();
        } else {
            $user->currentAccessToken()->delete();
        }
    }
}
