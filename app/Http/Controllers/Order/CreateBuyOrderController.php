<?php

namespace App\Http\Controllers\Order;

use App\Actions\Order\Create\CreateBuyOrderAction;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateBuyOrderRequest;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\JsonResponse;

class CreateBuyOrderController extends Controller
{
    /**
     * @throws InsufficientBalanceException
     */
    public function __invoke(CreateBuyOrderRequest $request, CreateBuyOrderAction $action): JsonResponse
    {
        $order = $action->execute(dto: $request->toDto());

        return $this->created(new OrderResource(resource: $order));
    }
}
