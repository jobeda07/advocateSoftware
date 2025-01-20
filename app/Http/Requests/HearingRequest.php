<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HearingRequest extends FormRequest
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
           'caseId' => 'required|exists:court_cases,caseId',
            'court_id' =>'required|integer|exists:court_lists,id',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
