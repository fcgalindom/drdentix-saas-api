<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:products,id'],
            'active_principle' => ['required', 'string'],
            'concentration' => ['required', 'string'],
            'amount' => ['required', 'integer'],
            'pharmaceutical_form' => ['required', 'string'],
            'commercial_presentation' => ['required', 'string'],
            'medication_unit' => ['required', 'string'],
            'batch' => ['required', 'string'],
            'health_register_invima' => ['required', 'string'],
            'expiration_date' => ['required', 'date'],
            'date_of_admission' => ['required', 'date'],
        ];
    }
}
