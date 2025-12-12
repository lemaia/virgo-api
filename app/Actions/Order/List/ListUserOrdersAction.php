<?php

namespace App\Actions\Order\List;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class ListUserOrdersAction
{
    public function execute(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with('trades')
            ->orderByRaw('CASE WHEN status = ? THEN 1 ELSE 0 END', [OrderStatus::CANCELLED->value])
            ->orderByDesc('created_at')
            ->get();
    }
}
