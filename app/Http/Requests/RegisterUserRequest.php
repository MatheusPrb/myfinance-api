<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class RegisterUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'name.string' => 'Nome inválido',

            'email.required' => 'O email é obrigatório',
            'email.rfc' => 'O email deve ser válido',

            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
            'password.letters' => 'A senha deve conter pelo menos uma letra',
            'password.numbers' => 'A senha deve conter pelo menos um número',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        // throw new HttpResponseException(
        //     ApiResponse::error('Os dados enviados são inválidos.', $validator->errors()->toArray(), 422)
        // );
    }
}
