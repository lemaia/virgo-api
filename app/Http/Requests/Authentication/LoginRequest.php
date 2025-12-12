<?php

namespace App\Http\Requests\Authentication;

use App\Actions\Authentication\Login\LoginDto;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(): LoginDto
    {
        return new LoginDto(
            email: $this->email,
            password: $this->password,
        );
    }
}
