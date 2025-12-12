<?php

namespace App\Models;

use App\Enums\Asset;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'status',
        'open_at',
        'filled_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'symbol' => Asset::class,
            'side' => OrderType::class,
            'status' => OrderStatus::class,
            'open_at' => 'datetime',
            'filled_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }
}
