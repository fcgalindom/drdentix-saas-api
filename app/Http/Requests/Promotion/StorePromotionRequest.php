<?php

namespace App\Http\Requests\Promotion;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:promotions,id'],
            'date_start' => ['required', 'date'],
            'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            'details' => ['required', 'string', 'max:600'],
            'discount' => ['required', 'integer'],
            'limit_patients' => ['required', 'integer', 'max:300'],
        ];
    }
}
