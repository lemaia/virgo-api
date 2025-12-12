<?php

namespace App\Events;

use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderbookUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $buyOrder,
        public Order $sellOrder,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('orderbook.' . $this->buyOrder->symbol->value),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'buy_order' => (new OrderResource($this->buyOrder))->resolve(),
            'sell_order' => (new OrderResource($this->sellOrder))->resolve(),
        ];
    }
}
