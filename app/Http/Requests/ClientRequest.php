<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'name' => 'required|string|max:150',
            'phone' =>'required',
            'alternative_phone' =>'required',
            'email'=>'nullable|email',
            'fathers_name' => 'required|string|max:150',
            'profession' => 'required|string|max:150',
            // 'division_id' => 'required',
            // 'district_id' => 'required',
            // 'thana_id' => 'required',
            //'address' => 'required|string|max:180',
            'reference' => 'required|string|max:500',
        ];
    }
}
