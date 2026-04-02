<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateExpenseRequest extends FormRequest
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
            'category_id' => ['required', 'uuid', Rule::exists('categories', 'id')],
            'subcategory_id' => ['sometimes', 'nullable', 'uuid', Rule::exists('subcategories', 'id')],
            'description' => ['sometimes', 'nullable', 'string'],
            'value' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'A categoria é obrigatória',
            'category_id.uuid' => 'Categoria inválida',
            'category_id.exists' => 'Categoria não encontrada',

            'subcategory_id.uuid' => 'Subcategoria inválida',
            'subcategory_id.exists' => 'Subcategoria não encontrada',

            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser numérico',
            'value.min' => 'O valor não pode ser negativo',
        ];
    }
}
