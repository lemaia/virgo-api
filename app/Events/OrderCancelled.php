<?php

namespace App\Events;

use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('orderbook.' . $this->order->symbol->value),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order' => (new OrderResource($this->order))->resolve(),
        ];
    }
}
