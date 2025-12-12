<?php

namespace App\Http\Resources\Order;

use App\Enums\OrderStatus;
use App\Helpers\MoneyHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total = MoneyHelper::calculateTotal($this->price, $this->amount, $this->symbol);

        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'symbol' => $this->symbol,
            'side' => $this->side,
            'price' => MoneyHelper::format(amount: $this->price, asset: 'USD'),
            'amount' => MoneyHelper::format(amount: $this->amount, asset: $this->symbol),
            'total' => MoneyHelper::format(amount: $total, asset: 'USD'),
            'status' => $this->status,
            'open_at' => $this->open_at,
            'filled_at' => $this->filled_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,
        ];

        if ($this->status === OrderStatus::FILLED) {
            $trade = $this->trades->first();
            $data['fee'] = $trade ? MoneyHelper::format(amount: $trade->fee, asset: 'USD') : null;
        }

        return $data;
    }
}
