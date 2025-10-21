<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|in:Applicants,Scholars',
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
            'announce_title.required' => 'Announcement title is required.',
            'announce_title.string' => 'Announcement title must be a string.',
            'announce_title.max' => 'Announcement title cannot exceed 255 characters.',
            'announce_content.required' => 'Announcement content is required.',
            'announce_content.string' => 'Announcement content must be a string.',
            'announce_type.required' => 'Announcement type is required.',
            'announce_type.in' => 'Announcement type must be either Applicants or Scholars.',
        ];
    }
}
