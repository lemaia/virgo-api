<?php

namespace App\Http\Resources\User\Profile;

use App\Helpers\MoneyHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'amount' => MoneyHelper::format(amount: $this->amount, asset: $this->symbol),
            'locked_amount' => MoneyHelper::format(amount: $this->locked_amount, asset: $this->symbol),
        ];
    }
}
