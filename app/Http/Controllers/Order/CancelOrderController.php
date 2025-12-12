<?php

namespace App\Http\Controllers\Order;

use App\Actions\Order\Cancel\CancelOrderAction;
use App\Exceptions\OrderCannotBeCancelledException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class CancelOrderController extends Controller
{
    /**
     * @throws OrderCannotBeCancelledException
     */
    public function __invoke(Order $order, CancelOrderAction $action): JsonResponse
    {
        $order = $action->execute(order: $order);

        return $this->ok(new OrderResource(resource: $order));
    }
}
