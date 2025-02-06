<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11','unique:users,phone'],
            'email'=>'nullable|email|unique:users,email',
            'join_date' => 'required',
            'designation' => 'required|string|max:150',
            'address' => 'required|string|max:180',
            'expertise_in' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'roles' => 'required|exists:roles,name',
        ];
    }
}
