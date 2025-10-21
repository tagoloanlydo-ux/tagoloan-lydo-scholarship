<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
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
            'email_type' => 'required|in:account_creation,rejection,registration_link,bulk_email',
            'email' => 'required_if:email_type,account_creation,rejection,registration_link|email|nullable',
            'emails' => 'required_if:email_type,bulk_email|array|nullable',
            'emails.*' => 'email',
            'subject' => 'required_if:email_type,bulk_email|string|max:255|nullable',
            'message' => 'required_if:email_type,bulk_email|string|nullable',
            'scholar_id' => 'required_if:email_type,account_creation|exists:tbl_scholar,scholar_id|nullable',
            'application_id' => 'required_if:email_type,rejection|exists:tbl_application,application_id|nullable',
            'registration_link' => 'required_if:email_type,registration_link|url|nullable',
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
            'email_type.required' => 'Email type is required.',
            'email_type.in' => 'Invalid email type.',
            'email.required_if' => 'Email is required for this email type.',
            'email.email' => 'Please provide a valid email address.',
            'emails.required_if' => 'Email list is required for bulk email.',
            'emails.array' => 'Emails must be an array.',
            'emails.*.email' => 'All email addresses must be valid.',
            'subject.required_if' => 'Subject is required for bulk email.',
            'subject.string' => 'Subject must be a string.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'message.required_if' => 'Message is required for bulk email.',
            'message.string' => 'Message must be a string.',
            'scholar_id.required_if' => 'Scholar ID is required for account creation email.',
            'scholar_id.exists' => 'Invalid scholar ID.',
            'application_id.required_if' => 'Application ID is required for rejection email.',
            'application_id.exists' => 'Invalid application ID.',
            'registration_link.required_if' => 'Registration link is required.',
            'registration_link.url' => 'Registration link must be a valid URL.',
        ];
    }
}
