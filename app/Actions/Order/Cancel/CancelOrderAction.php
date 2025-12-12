<?php

namespace App\Actions\Order\Cancel;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Events\BalanceUpdated;
use App\Events\OrderCancelled;
use App\Exceptions\OrderCannotBeCancelledException;
use App\Helpers\MoneyHelper;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;

class CancelOrderAction
{
    /**
     * @throws OrderCannotBeCancelledException
     */
    public function execute(Order $order): Order
    {
        $this->ensureOrderCanBeCancelled(order: $order);

        $this->unlockFunds(order: $order);

        $order->update([
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        $user = User::findOrFail($order->user_id);
        BalanceUpdated::dispatch($user->refresh());
        OrderCancelled::dispatch($order);

        return $order;
    }

    /**
     * @throws OrderCannotBeCancelledException
     */
    private function ensureOrderCanBeCancelled(Order $order): void
    {
        if ($order->status !== OrderStatus::OPEN) {
            throw new OrderCannotBeCancelledException();
        }
    }

    private function unlockFunds(Order $order): void
    {
        if ($order->side === OrderType::BUY) {
            $this->unlockBalance(order: $order);
        } else {
            $this->unlockAsset(order: $order);
        }
    }

    private function unlockBalance(Order $order): void
    {
        $user = User::findOrFail($order->user_id);

        $total = MoneyHelper::calculateTotal($order->price, $order->amount, $order->symbol);
        $feePercent = config('trading.fee_percent');
        $fee = (int) (($total * $feePercent) / 10000);
        $totalWithFee = $total + $fee;

        $user->decrement('locked_balance', $totalWithFee);
        $user->increment('balance', $totalWithFee);
    }

    private function unlockAsset(Order $order): void
    {
        $asset = Asset::where('user_id', $order->user_id)
            ->where('symbol', $order->symbol)
            ->firstOrFail();

        $asset->decrement('locked_amount', $order->amount);
        $asset->increment('amount', $order->amount);
    }
}
