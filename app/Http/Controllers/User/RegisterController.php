<?php

namespace App\Http\Controllers\User;

use App\Actions\User\Register\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Resources\User\Profile\UserProfileResource;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, CreateUserAction $action): JsonResponse
    {
        $user = $action->execute(dto: $request->toDto());

        return $this->created(new UserProfileResource(resource: $user));
    }
}
