<?php

namespace App\Actions\Order\Match;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Events\BalanceUpdated;
use App\Events\OrderbookUpdated;
use App\Events\OrderMatched;
use App\Helpers\MoneyHelper;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExecuteMatchAction
{
    public function execute(Order $buyOrder, Order $sellOrder): void
    {
        $eventData = DB::transaction(function () use ($buyOrder, $sellOrder) {
            $price = $sellOrder->price;
            $amount = $buyOrder->amount;
            $total = MoneyHelper::calculateTotal(
                price: $price,
                amount: $amount,
                asset: $buyOrder->symbol
            );

            $feePercent = config('trading.fee_percent');
            $fee = (int) (($total * $feePercent) / 10000);

            [$buyTrade, $sellTrade] = $this->createTrades(
                buyOrder: $buyOrder,
                sellOrder: $sellOrder,
                price: $price,
                amount: $amount,
                total: $total,
                fee: $fee,
            );

            [$buyer, $seller, $buyerAsset, $sellerAsset] = $this->transferFunds(
                buyOrder: $buyOrder,
                sellOrder: $sellOrder,
                total: $total,
                amount: $amount,
                fee: $fee,
            );

            $this->markOrdersAsFilled(buyOrder: $buyOrder, sellOrder: $sellOrder);

            return [
                'buyTrade' => $buyTrade,
                'sellTrade' => $sellTrade,
                'buyer' => $buyer,
                'seller' => $seller,
                'buyerAsset' => $buyerAsset,
                'sellerAsset' => $sellerAsset,
            ];
        });

        $this->dispatchEvents(
            buyOrder: $buyOrder,
            sellOrder: $sellOrder,
            eventData: $eventData
        );
    }

    private function dispatchEvents(Order $buyOrder, Order $sellOrder, array $eventData): void
    {
        $buyOrder->refresh();
        $sellOrder->refresh();

        OrderMatched::dispatch(
            $eventData['buyTrade'],
            $buyOrder,
            $eventData['buyer'],
            $eventData['buyerAsset'],
        );

        OrderMatched::dispatch(
            $eventData['sellTrade'],
            $sellOrder,
            $eventData['seller'],
            $eventData['sellerAsset'],
        );

        BalanceUpdated::dispatch($eventData['buyer']);
        BalanceUpdated::dispatch($eventData['seller']);

        OrderbookUpdated::dispatch($buyOrder, $sellOrder);
    }

    private function createTrades(
        Order $buyOrder,
        Order $sellOrder,
        int $price,
        int $amount,
        int $total,
        int $fee,
    ): array {
        $buyTrade = Trade::create([
            'order_id' => $buyOrder->id,
            'user_id' => $buyOrder->user_id,
            'side' => OrderType::BUY,
            'price' => $price,
            'amount' => $amount,
            'total' => $total,
            'fee' => $fee,
            'total_final' => $total + $fee,
        ]);

        $sellTrade = Trade::create([
            'order_id' => $sellOrder->id,
            'user_id' => $sellOrder->user_id,
            'side' => OrderType::SELL,
            'price' => $price,
            'amount' => $amount,
            'total' => $total,
            'fee' => $fee,
            'total_final' => $total - $fee,
        ]);

        return [$buyTrade, $sellTrade];
    }

    private function transferFunds(
        Order $buyOrder,
        Order $sellOrder,
        int $total,
        int $amount,
        int $fee,
    ): array {
        $buyer = User::findOrFail($buyOrder->user_id);
        $seller = User::findOrFail($sellOrder->user_id);

        $totalSpent = $total + $fee;
        [$totalLocked, $refund] = $this->calculateRefund(
            buyOrder: $buyOrder,
            amount: $amount,
            totalSpent: $totalSpent
        );

        $this->transferBalance(
            buyer: $buyer,
            seller: $seller,
            totalLocked: $totalLocked,
            refund: $refund,
            sellerAmount: $total - $fee
        );

        [$buyerAsset, $sellerAsset] = $this->transferAssets(
            buyer: $buyer,
            seller: $seller,
            buyOrder: $buyOrder,
            sellOrder: $sellOrder,
            amount: $amount
        );

        return [
            $buyer->refresh(),
            $seller->refresh(),
            $buyerAsset->refresh(),
            $sellerAsset->refresh(),
        ];
    }

    private function calculateRefund(Order $buyOrder, int $amount, int $totalSpent): array
    {
        $feePercent = config('trading.fee_percent');
        $lockedTotal = MoneyHelper::calculateTotal(
            price: $buyOrder->price,
            amount: $amount,
            asset: $buyOrder->symbol
        );
        $lockedFee = (int) (($lockedTotal * $feePercent) / 10000);
        $totalLocked = $lockedTotal + $lockedFee;

        return [$totalLocked, $totalLocked - $totalSpent];
    }

    private function transferBalance(User $buyer, User $seller, int $totalLocked, int $refund, int $sellerAmount): void
    {
        $buyer->decrement('locked_balance', $totalLocked);

        if ($refund > 0) {
            $buyer->increment('balance', $refund);
        }

        $seller->increment('balance', $sellerAmount);
    }

    private function transferAssets(User $buyer, User $seller, Order $buyOrder, Order $sellOrder, int $amount): array
    {
        $sellerAsset = Asset::where('user_id', $seller->id)
            ->where('symbol', $sellOrder->symbol)
            ->firstOrFail();

        $buyerAsset = Asset::firstOrCreate(
            ['user_id' => $buyer->id, 'symbol' => $buyOrder->symbol],
            ['amount' => 0, 'locked_amount' => 0],
        );

        $sellerAsset->decrement('locked_amount', $amount);
        $buyerAsset->increment('amount', $amount);

        return [$buyerAsset, $sellerAsset];
    }

    private function markOrdersAsFilled(Order $buyOrder, Order $sellOrder): void
    {
        $now = now();

        Order::whereIn('id', [$buyOrder->id, $sellOrder->id])->update([
            'status' => OrderStatus::FILLED->value,
            'filled_at' => $now,
        ]);
    }
}
