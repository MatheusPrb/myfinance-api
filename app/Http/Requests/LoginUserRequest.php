<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'O email deve ser válido',

            'password.required' => 'A senha é obrigatória',
            'password.string' => 'Senha inválida',
        ];
    }
}
