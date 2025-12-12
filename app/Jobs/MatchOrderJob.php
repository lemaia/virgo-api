<?php

namespace App\Jobs;

use App\Actions\Order\Match\MatchOrderAction;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class MatchOrderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(MatchOrderAction $matchOrderAction): void
    {
        $lockKey = "match-order-lock:{$this->order->symbol->value}";

        Cache::lock($lockKey, 30)->block(25, function () use ($matchOrderAction) {
            $matchOrderAction->execute(order: $this->order);
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('MatchOrderJob failed', [
            'order_id' => $this->order->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}
