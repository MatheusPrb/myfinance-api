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
            'date_from' => ['sometimes', 'required_with:date_to', 'date', 'date_format:Y-m-d'],
            'date_to' => ['sometimes', 'required_with:date_from', 'date', 'date_format:Y-m-d'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $from = $this->input('date_from');
        $to = $this->input('date_to');

        if (is_string($from) && is_string($to) && $from > $to) {
            $this->merge([
                'date_from' => $to,
                'date_to' => $from,
            ]);
        }
    }
}
