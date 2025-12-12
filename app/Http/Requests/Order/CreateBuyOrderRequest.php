<?php

namespace App\Http\Requests\Order;

use App\Actions\Order\Create\CreateOrderDto;
use App\Enums\Asset;
use App\Helpers\MoneyHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBuyOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minAmount = match ($this->symbol) {
            Asset::BTC->value => '0.00000001',
            Asset::ETH->value => '0.000000000000000001',
            default => '0.00000001',
        };

        return [
            'symbol' => ['required', 'string', Rule::enum(Asset::class)],
            'price' => ['required', 'numeric', 'min:0.01'],
            'amount' => ['required', 'numeric', "min:$minAmount"],
        ];
    }

    public function toDto(): CreateOrderDto
    {
        $asset = Asset::from($this->symbol);

        return new CreateOrderDto(
            userId: $this->user()->id,
            symbol: $asset,
            price: MoneyHelper::toSmallestUnit(value: $this->price, asset: 'USD'),
            amount: MoneyHelper::toSmallestUnit(value: $this->amount, asset: $asset),
        );
    }
}
