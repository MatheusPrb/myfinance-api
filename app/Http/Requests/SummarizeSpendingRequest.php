<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SummarizeSpendingRequest extends FormRequest
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
            'date_from' => [
                'nullable',
                'required_with:date_to',
                'date_format:Y-m-d',
            ],
            'date_to' => [
                'nullable',
                'required_with:date_from',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ];
    }
}
