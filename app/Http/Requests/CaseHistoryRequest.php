<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaseHistoryRequest extends FormRequest
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
            'hearing_date_time' => 'required',
            'activity' => 'required|max:250',
            'court_decition' => 'required|max:250',
            'remarks' => 'required',
            'case_history_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'case_history_pdf' => 'nullable|mimes:pdf|max:2000',
        ];
    }
}
