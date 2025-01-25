<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CaseTaskRequest extends FormRequest
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
     * @return array<string, 
     */
    public function rules(): array
    {
        return [
            'caseId'=>'required|exists:court_cases,caseId',
            'title'=>'required',
            'details'=>'required',
            'priority'=>'required|in:Low,Medium,High',
            'date'=>'required',
            'assign_to'=>'required',
        ];
    }
}
