<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourtCaseRequest extends FormRequest
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
            'clientId' => 'required|exists:clients,id',
            'client_type' =>'required|integer|exists:client_types,id',
            'case_type' => 'required|integer|exists:case_types,id',
            'case_category' => 'required|integer|exists:case_categories,id',
            'case_section' =>'required',
            'case_stage' =>'required|integer|exists:case_stages,id',
            'court' => 'required|integer|exists:court_lists,id',
            'fees' => 'required',
            'comments' => 'required',
            'opposition_phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'opposition_name' => 'required|string|max:150',
            'case_doc_name' => 'nullable|array',
            'case_doc_name.*' => 'nullable|string',
            'case_image.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'case_pdf.*' => 'nullable|mimes:pdf|max:2000',
        ];
    }
}
