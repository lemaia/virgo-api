<?php

namespace App\Actions\Order\Create;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Events\BalanceUpdated;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientAssetException;
use App\Jobs\MatchOrderJob;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;

class CreateSellOrderAction
{
    /**
     * @throws InsufficientAssetException
     */
    public function execute(CreateOrderDto $dto): Order
    {
        $asset = $this->ensureUserHasSufficientAsset(dto: $dto);
        $this->lockAssetAmount(asset: $asset, amount: $dto->amount);

        $order = Order::create([
            'user_id' => $dto->userId,
            'symbol' => $dto->symbol,
            'side' => OrderType::SELL,
            'price' => $dto->price,
            'amount' => $dto->amount,
            'status' => OrderStatus::OPEN,
            'open_at' => now(),
        ]);

        $user = User::findOrFail($dto->userId);

        OrderCreated::dispatch($order);
        BalanceUpdated::dispatch($user);
        MatchOrderJob::dispatch($order);

        return $order;
    }

    /**
     * @throws InsufficientAssetException
     */
    private function ensureUserHasSufficientAsset(CreateOrderDto $dto): Asset
    {
        $asset = Asset::where('user_id', $dto->userId)
            ->where('symbol', $dto->symbol)
            ->first();

        if (!$asset || $asset->amount < $dto->amount) {
            throw new InsufficientAssetException();
        }

        return $asset;
    }

    private function lockAssetAmount(Asset $asset, int $amount): void
    {
        $asset->decrement('amount', $amount);
        $asset->increment('locked_amount', $amount);
    }
}
