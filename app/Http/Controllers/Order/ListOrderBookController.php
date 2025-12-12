<?php

namespace App\Http\Controllers\Order;

use App\Actions\Order\List\ListOpenOrdersAction;
use App\Enums\Asset;
use App\Helpers\MoneyHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ListOrderBookController extends Controller
{
    public function __invoke(string $symbol, ListOpenOrdersAction $action): JsonResponse
    {
        $asset = Asset::from($symbol);

        $orderBook = $action->execute(symbol: $asset);

        return $this->ok([
            'buy' => $this->formatPriceLevels($orderBook['buy']),
            'sell' => $this->formatPriceLevels($orderBook['sell']),
        ]);
    }

    private function formatPriceLevels(array $levels): array
    {
        return array_map(fn ($level) => [
            'price' => MoneyHelper::format($level['price'], 'USD'),
            'amount' => MoneyHelper::format($level['amount'], $level['symbol']->value),
        ], $levels);
    }
}
