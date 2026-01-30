<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return 
            [
            'subscription' => 'required|array',
            'browser' => 'nullable|string'
        ]
        ;
    }
    public function messages(): array
    {
        return [
            'subscription.required' => 'Subscription data is required',
            'subscription.array' => 'Subscription must be a valid JSON object',
            'browser.string' => 'Browser must be a string',
        ];
    }   
}
