<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
            'status' => 'required|in:Approved,Rejected,Pending',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'application_id.required' => 'Application ID is required.',
            'application_id.exists' => 'Invalid application ID.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status value.',
            'remarks.required' => 'Remarks are required.',
            'remarks.string' => 'Remarks must be a string.',
            'remarks.max' => 'Remarks cannot exceed 255 characters.',
        ];
    }
}
