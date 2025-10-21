<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInitialScreeningRequest extends FormRequest
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
            'initial_screening' => 'required|in:Pending,Approved,Rejected',
            'rejection_reason' => 'nullable|string|max:1000',
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
            'initial_screening.required' => 'Initial screening status is required.',
            'initial_screening.in' => 'Initial screening status must be Pending, Approved, or Rejected.',
            'rejection_reason.string' => 'Rejection reason must be a string.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters.',
        ];
    }
}
