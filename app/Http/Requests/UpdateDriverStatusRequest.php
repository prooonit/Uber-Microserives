<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth already handled by middleware
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:ONLINE,OFFLINE'],

            // lat/lng are OPTIONAL but must be valid if present
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'lat.between' => 'Latitude must be between -90 and 90',
            'lng.between' => 'Longitude must be between -180 and 180',
        ];
    }
}
