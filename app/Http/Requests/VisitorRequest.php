<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitorRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'case_type' => 'required|exists:case_types,id',
            'case_category_id' => 'required|exists:case_categories,id',
            'priority' => 'required|in:Low,Medium,High',
            'fees' => 'required|integer',
            'reference' => 'nullable|max:500',
            'remark' => 'nullable',
        ];
    }
}
