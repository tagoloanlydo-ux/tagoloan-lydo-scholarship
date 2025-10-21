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
}
