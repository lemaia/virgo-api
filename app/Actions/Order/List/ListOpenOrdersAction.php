<?php

namespace App\Actions\Order\List;

use App\Enums\Asset;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use Illuminate\Support\Collection;

class ListOpenOrdersAction
{
    /**
     * @return array{buy: array, sell: array}
     */
    public function execute(Asset $symbol): array
    {
        $orders = Order::where('symbol', $symbol)
            ->where('status', OrderStatus::OPEN)
            ->get();

        return [
            'buy' => $this->groupByPrice($orders->where('side', OrderType::BUY), 'desc', $symbol),
            'sell' => $this->groupByPrice($orders->where('side', OrderType::SELL), 'asc', $symbol),
        ];
    }

    private function groupByPrice(Collection $orders, string $sortDirection, Asset $symbol): array
    {
        return $orders->groupBy('price')
            ->map(fn ($group) => [
                'price' => $group->first()->price,
                'amount' => $group->sum('amount'),
                'symbol' => $symbol,
            ])
            ->sortBy('price', SORT_REGULAR, $sortDirection === 'desc')
            ->values()
            ->all();
    }
}
