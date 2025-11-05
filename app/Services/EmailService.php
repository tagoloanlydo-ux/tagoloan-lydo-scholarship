<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send account creation email to scholar
     *
     * @param string $email
     * @param string $scholarId
     * @return bool
     */
    public function sendAccountCreationEmail($email, $scholarId)
    {
        try {
            Mail::send('emails.account-creation', ['scholar_id' => $scholarId], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Scholar Account Created - LYDO Scholarship');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send account creation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send rejection email to applicant
     *
     * @param string $email
     * @param array $data
     * @return bool
     */
    public function sendRejectionEmail($email, array $data)
    {
        try {
            Mail::send('emails.scholar-rejection', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Renewal Status Update - LYDO Scholarship');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send approval email to applicant
     *
     * @param string $email
     * @param array $data
     * @return bool
     */
    public function sendApprovalEmail($email, array $data)
    {
        try {
            Mail::send('emails.scholar-approval', $data, function ($message) use ($email) {
                $message->to($email)
                        ->subject('Renewal Status Update - LYDO Scholarship');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send plain email
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendPlainEmail($email, $subject, $message)
    {
        try {
            Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $message], function ($mail) use ($email, $subject) {
                $mail->to($email)
                     ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send plain email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk emails
     *
     * @param array $emails
     * @param string $subject
     * @param string $message
     * @return array
     */
    public function sendBulkEmails(array $emails, $subject, $message)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($emails as $email) {
            if ($this->sendPlainEmail($email, $subject, $message)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $email;
            }
        }

        return $results;
    }

    /**
     * Send OTP email for password reset
     *
     * @param string $email
     * @param string $otp
     * @return bool
     */
    public function sendOtpEmail($email, $otp)
    {
        try {
            Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($email){
                $message->to($email);
                $message->subject('LYDO Scholarship Password Reset OTP');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send registration link email
     *
     * @param string $email
     * @param string $registrationLink
     * @return bool
     */
    public function sendRegistrationLinkEmail($email, $registrationLink)
    {
        try {
            Mail::send('emails.registration-link', ['registrationLink' => $registrationLink], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Complete Your Scholar Registration - LYDO Scholarship');
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send registration link email: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Send document update request email
     *
     * @param string $email
     * @param array $data
     * @return bool
     */
    public function sendDocumentUpdateRequest($email, $data)
    {
        $subject = 'Document Update Required - Scholarship Renewal';

        $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8fafc; padding: 20px; border-radius: 0 0 10px 10px; border: 1px solid #e2e8f0; }
            .comment-box { background: #fff; border-left: 4px solid #ef4444; padding: 15px; margin: 15px 0; }
            .button { display: inline-block; padding: 12px 24px; background: #7c3aed; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Document Update Required</h2>
            </div>
            <div class='content'>
                <p>Dear {$data['applicant_fname']} {$data['applicant_lname']},</p>

                <p>Your <strong>{$data['document_type']}</strong> has been marked as requiring an update for your scholarship renewal application.</p>

                <div class='comment-box'>
                    <strong>Reviewer's Comment:</strong>
                    <p>{$data['comment']}</p>
                </div>

                <p>Please log in to your scholarship portal and upload a new version of this document. The document that needs updating is: <strong>{$data['document_type']}</strong>.</p>

                <p><strong>Action Required:</strong> Upload the updated document within 7 days to continue with your renewal process. Once updated, the document will be re-reviewed by our staff.</p>

                <p>If you have any questions, please contact the scholarship office.</p>

                <p>Best regards,<br>Scholarship Management Team</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return $this->sendEmail($email, $subject, $message);
}

    /**
     * Send plain email (helper method)
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return bool
     */
    private function sendEmail($email, $subject, $message)
    {
        try {
            Mail::send([], [], function ($mail) use ($email, $subject, $message) {
                $mail->to($email)
                     ->subject($subject)
                     ->html($message);
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        }
    }
}
