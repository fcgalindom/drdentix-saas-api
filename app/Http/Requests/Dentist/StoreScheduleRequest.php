<?php

namespace App\Http\Requests\Dentist;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dentist_id' => ['required', 'exists:dentists,id'],
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.day' => ['required', 'integer', 'between:1,6'],
            'schedules.*.attend' => ['required', 'boolean'],
            'schedules.*.hour_start' => ['nullable', 'date_format:H:i'],
            'schedules.*.hour_end' => ['nullable', 'date_format:H:i'],
            'schedules.*.break' => ['nullable', 'boolean'],
            'schedules.*.break_start' => ['nullable', 'date_format:H:i'],
            'schedules.*.break_end' => ['nullable', 'date_format:H:i'],
        ];
    }
}
