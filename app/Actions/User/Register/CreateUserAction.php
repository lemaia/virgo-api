<?php

namespace App\Actions\User\Register;

use App\Models\User;

class CreateUserAction
{
    private const INITIAL_BALANCE_USD = 5_000_000; // 50k USD
    private const INITIAL_BALANCE_BTC = 50_000_000; // 0.5 BTC
    private const INITIAL_BALANCE_ETH = 500_000_000_000_000_000; // 0.5 ETH

    public function execute(CreateUserDto $dto): User
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'balance' => self::INITIAL_BALANCE_USD,
        ]);

        $user->assets()->createMany([
            ['symbol' => 'BTC', 'amount' => self::INITIAL_BALANCE_BTC],
            ['symbol' => 'ETH', 'amount' => self::INITIAL_BALANCE_ETH],
        ]);

        return $user;
    }
}
