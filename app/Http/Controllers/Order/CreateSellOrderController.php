<?php

namespace App\Http\Controllers\Order;

use App\Actions\Order\Create\CreateSellOrderAction;
use App\Exceptions\InsufficientAssetException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateSellOrderRequest;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\JsonResponse;

class CreateSellOrderController extends Controller
{
    /**
     * @throws InsufficientAssetException
     */
    public function __invoke(CreateSellOrderRequest $request, CreateSellOrderAction $action): JsonResponse
    {
        $order = $action->execute(dto: $request->toDto());

        return $this->created(new OrderResource(resource: $order));
    }
}
