<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListSubcategoriesByCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'category_id' => $this->route('category'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'string', 'uuid'],
        ];
    }
}
