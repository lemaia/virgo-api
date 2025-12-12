<?php

namespace App\Http\Resources\User\Profile;

use App\Helpers\MoneyHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'balance' => MoneyHelper::format(amount: $this->balance, asset: 'USD'),
            'locked_balance' => MoneyHelper::format(amount: $this->locked_balance, asset: 'USD'),
            'assets' => UserAssetResource::collection(resource: $this->assets),
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
        ];
    }
}
