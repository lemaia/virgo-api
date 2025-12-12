<?php

namespace App\Events;

use App\Helpers\MoneyHelper;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Trade\TradeResource;
use App\Http\Resources\User\Profile\UserAssetResource;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Trade $trade,
        public Order $order,
        public User $user,
        public ?Asset $asset = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'trade' => (new TradeResource($this->trade))->resolve(),
            'order' => (new OrderResource($this->order))->resolve(),
            'balance' => MoneyHelper::format($this->user->balance, 'USD'),
            'locked_balance' => MoneyHelper::format($this->user->locked_balance, 'USD'),
            'asset' => $this->asset ? (new UserAssetResource($this->asset))->resolve() : null,
        ];
    }
}
