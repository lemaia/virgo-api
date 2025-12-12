<?php

namespace App\Http\Resources\Trade;

use App\Helpers\MoneyHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $symbol = $this->order->symbol ?? 'BTC';

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'side' => $this->side,
            'price' => MoneyHelper::format(amount: $this->price, asset: 'USD'),
            'amount' => MoneyHelper::format(amount: $this->amount, asset: $symbol),
            'total' => MoneyHelper::format(amount: $this->total, asset: 'USD'),
            'fee' => MoneyHelper::format(amount: $this->fee, asset: 'USD'),
            'total_final' => MoneyHelper::format(amount: $this->total_final, asset: 'USD'),
            'created_at' => $this->created_at,
        ];
    }
}
