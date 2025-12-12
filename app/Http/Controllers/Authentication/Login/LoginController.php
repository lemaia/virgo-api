<?php

namespace App\Http\Controllers\Authentication\Login;

use App\Actions\Authentication\Login\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Resources\User\Profile\UserProfileResource;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $result = $action->execute(dto: $request->toDto());

        return $this->ok([
            'user' => new UserProfileResource($result['user']),
            'token' => $result['token'],
        ]);
    }
}
