<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'latitude' => 'required|string|max:150',
            'longitude' => 'required|string|max:150',
            'address' => 'required|string|max:150',
            'email' => 'required|email|string|max:50',
            'phone' => 'required|numeric|digits:11',
            'facebook_link' => 'required|string|max:150',
            'location_details' => 'required|string',
            'image' => 'required|image|mimes:png,jpg|max:1000'
        ];
    }
}
