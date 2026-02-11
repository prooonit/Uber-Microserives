<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestrideRequest extends FormRequest
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
        return [
            
            'pickup_lat' => 'required|numeric|between:-90,90',
            'pickup_lng' => 'required|numeric|between:-180,180',
            'dropoff_lat' => 'required|numeric|between:-90,90',
            'dropoff_lng' => 'required|numeric|between:-180,180',
        ];
    }
    public function messages(): array
    {
        return [
            'pickup_lat.required' => 'Pickup latitude is required',
            'pickup_lat.numeric' => 'Pickup latitude must be a number',
            'pickup_lat.between' => 'Pickup latitude must be between -90 and 90',
            'pickup_lng.required' => 'Pickup longitude is required',
            'pickup_lng.numeric' => 'Pickup longitude must be a number',
            'pickup_lng.between' => 'Pickup longitude must be between -180 and 180',
            'dropoff_lat.required' => 'Dropoff latitude is required',
            'dropoff_lat.numeric' => 'Dropoff latitude must be a number',
            'dropoff_lat.between' => 'Dropoff latitude must be between -90 and 90',
            'dropoff_lng.required' => 'Dropoff longitude is required',
            'dropoff_lng.numeric' => 'Dropoff longitude must be a number',
            'dropoff_lng.between' => 'Dropoff longitude must be between -180 and 180',
        ];
    }

}
