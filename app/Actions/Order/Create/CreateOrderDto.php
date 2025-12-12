<?php

namespace App\Actions\Order\Create;

use App\Enums\Asset;

readonly class CreateOrderDto
{
    public function __construct(
        public int $userId,
        public Asset $symbol,
        public int $price,
        public int $amount,
    ) {}
}
