<?php

namespace App\Http\Requests\User;

use App\Actions\User\Register\CreateUserDto;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toDto(): CreateUserDto
    {
        return new CreateUserDto(
            name: $this->name,
            email: $this->email,
            password: $this->password,
        );
    }
}
