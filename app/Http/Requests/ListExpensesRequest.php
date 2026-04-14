<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListExpensesRequest extends FormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'date_from' => ['sometimes', 'required_with:date_to', 'date', 'date_format:Y-m-d'],
            'date_to' => ['sometimes', 'required_with:date_from', 'date', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'category_id' => ['nullable', 'uuid'],
            'subcategory_id' => ['sometimes', 'uuid'],
        ];
    }
}
