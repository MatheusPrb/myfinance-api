<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ShowExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'uuid:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O identificador do gasto é obrigatório',
            'id.string' => 'Identificador do gasto inválido',
        ];
    }
}
