<?php

namespace App\Http\Controllers\Order;

use App\Actions\Order\List\ListUserOrdersAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListUserOrdersController extends Controller
{
    public function __invoke(Request $request, ListUserOrdersAction $action): JsonResponse
    {
        $orders = $action->execute(userId: $request->user()->id);

        return $this->ok(OrderResource::collection($orders));
    }
}
