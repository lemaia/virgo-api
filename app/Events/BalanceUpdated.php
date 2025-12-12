<?php

namespace App\Events;

use App\Helpers\MoneyHelper;
use App\Http\Resources\User\Profile\UserAssetResource;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BalanceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        $this->user->load('assets');

        return [
            'balance' => MoneyHelper::format($this->user->balance, 'USD'),
            'locked_balance' => MoneyHelper::format($this->user->locked_balance, 'USD'),
            'assets' => UserAssetResource::collection($this->user->assets)->resolve(),
        ];
    }
}
