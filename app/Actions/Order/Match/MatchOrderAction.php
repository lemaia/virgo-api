<?php

namespace App\Actions\Order\Match;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use Throwable;

readonly class MatchOrderAction
{
    public function __construct(
        private ExecuteMatchAction $executeMatchAction,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Order $order): Order
    {
        $matchingOrder = $this->findMatchingOrder(order: $order);

        if (!$matchingOrder) {
            return $order;
        }

        $side = $order->side instanceof OrderType ? $order->side : OrderType::from($order->side);
        $buyOrder = $side === OrderType::BUY ? $order : $matchingOrder;
        $sellOrder = $side === OrderType::SELL ? $order : $matchingOrder;

        $this->executeMatchAction->execute(
            buyOrder: $buyOrder,
            sellOrder: $sellOrder,
        );

        return $order->refresh();
    }

    private function findMatchingOrder(Order $order): ?Order
    {
        $side = $order->side instanceof OrderType ? $order->side : OrderType::from($order->side);

        $query = Order::where('symbol', $order->symbol)
            ->where('status', OrderStatus::OPEN)
            ->where('amount', $order->amount)
            ->where('user_id', '!=', $order->user_id);

        if ($side === OrderType::BUY) {
            return $query
                ->where('side', OrderType::SELL)
                ->where('price', '<=', $order->price)
                ->orderBy('price', 'asc')
                ->orderBy('created_at', 'asc')
                ->first();
        }

        return $query
            ->where('side', OrderType::BUY)
            ->where('price', '>=', $order->price)
            ->orderBy('price', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();
    }
}
