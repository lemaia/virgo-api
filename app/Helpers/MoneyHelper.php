<?php

namespace App\Helpers;

use App\Enums\Asset;

class MoneyHelper
{
    public static function format(int $amount, Asset|string $asset): array
    {
        $decimals = self::getDecimals(asset: $asset);
        $value = $amount / pow(10, $decimals);

        return [
            'raw' => $amount,
            'decimals' => $decimals,
            'formatted' => number_format(num: $value, decimals: $decimals),
        ];
    }

    public static function toSmallestUnit(float|string $value, Asset|string $asset): int
    {
        $decimals = self::getDecimals(asset: $asset);

        return (int) round((float) $value * pow(10, $decimals));
    }

    public static function getDecimals(Asset|string $asset): int
    {
        $assetValue = $asset instanceof Asset ? $asset->value : $asset;

        return match ($assetValue) {
            'USD' => 2,
            Asset::BTC->value => 8,
            Asset::ETH->value => 18,
            default => 8,
        };
    }

    public static function calculateTotal(int $price, int $amount, Asset|string $asset): int
    {
        $assetDecimals = self::getDecimals($asset);

        return (int) (($price * $amount) / pow(10, $assetDecimals));
    }
}
