<?php

namespace App\Actions\Order\Create;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Events\BalanceUpdated;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientBalanceException;
use App\Helpers\MoneyHelper;
use App\Jobs\MatchOrderJob;
use App\Models\Order;
use App\Models\User;

class CreateBuyOrderAction
{
    /**
     * @throws InsufficientBalanceException
     */
    public function execute(CreateOrderDto $dto): Order
    {
        [$user, $totalWithFee] = $this->ensureUserHasSufficientBalance(dto: $dto);
        $this->lockBalance(user: $user, amount: $totalWithFee);

        $order = Order::create([
            'user_id' => $dto->userId,
            'symbol' => $dto->symbol,
            'side' => OrderType::BUY,
            'price' => $dto->price,
            'amount' => $dto->amount,
            'status' => OrderStatus::OPEN,
            'open_at' => now(),
        ]);

        OrderCreated::dispatch($order);
        BalanceUpdated::dispatch($user->refresh());
        MatchOrderJob::dispatch($order);

        return $order;
    }

    /**
     * @throws InsufficientBalanceException
     */
    private function ensureUserHasSufficientBalance(CreateOrderDto $dto): array
    {
        $user = User::findOrFail($dto->userId);

        $total = MoneyHelper::calculateTotal($dto->price, $dto->amount, $dto->symbol);
        $feePercent = config('trading.fee_percent');
        $fee = (int) (($total * $feePercent) / 10000);
        $totalWithFee = $total + $fee;

        if ($user->balance < $totalWithFee) {
            throw new InsufficientBalanceException();
        }

        return [$user, $totalWithFee];
    }

    private function lockBalance(User $user, int $amount): void
    {
        $user->decrement('balance', $amount);
        $user->increment('locked_balance', $amount);
    }
}
