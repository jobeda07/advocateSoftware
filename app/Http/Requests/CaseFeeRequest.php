<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaseFeeRequest extends FormRequest
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
           'case_id' => 'required|exists:court_cases,id',
            'amount' =>'required|integer',
            'payment_type' =>'required',
        ];
    }
}
