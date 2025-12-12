<?php

namespace App\Http\Controllers\Authentication\Logout;

use App\Actions\Authentication\Logout\LogoutAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutAction $action): JsonResponse
    {
        $allDevices = $request->boolean('all_devices', true);

        $action->execute(user: $request->user(), allDevices: $allDevices);

        return $this->ok(message: 'Logged out successfully');
    }
}
