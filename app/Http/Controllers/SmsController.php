<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Announce;

class SmsController extends Controller
{
    public function sendSmsToApplicants(Request $request)
    {
        $request->validate([
            'selected_emails' => 'required|string',
            'message' => 'nullable|string|max:160',
            'sms_type' => 'required|string|in:plain,schedule',
            'schedule_what' => 'nullable|string|max:255',
            'schedule_where' => 'nullable|string|max:255',
            'schedule_date' => 'nullable|date',
            'schedule_time' => 'nullable|string',
        ]);

        $selectedEmails = explode(',', $request->selected_emails);
        $selectedEmails = array_map('trim', $selectedEmails);
        $message = $request->message;
        $smsType = $request->sms_type;

        try {
            // Get applicant contact numbers for the selected emails
            $applicants = DB::table('tbl_applicant as a')
                ->join('tbl_application as app', 'a.applicant_id', '=', 'app.applicant_id')
                ->join('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
                ->whereIn('a.applicant_email', $selectedEmails)
                ->whereIn('ap.initial_screening', ['Approved', 'Reviewed'])
                ->select(
                    'a.applicant_email',
                    'a.applicant_contact_number',
                    'a.applicant_fname',
                    'a.applicant_lname',
                    'ap.initial_screening'
                )
                ->get();

            if ($applicants->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid applicants found for the selected emails.']);
            }

            $sentSmsCount = 0;
            $sentEmailCount = 0;
            $failedSmsCount = 0;
            $failedEmailCount = 0;
            $results = [];

            // Create announcement if it's a schedule type
            if ($smsType === 'schedule' && $request->schedule_what) {
                $this->createScheduleAnnouncement($request);
            }

            foreach ($applicants as $applicant) {
                $fullName = $applicant->applicant_fname . ' ' . $applicant->applicant_lname;
                
                // Send SMS
                $smsResult = $this->sendApplicantSms($applicant, $message, $request);
                if ($smsResult['success']) {
                    $sentSmsCount++;
                    $results[] = "✓ SMS sent to {$fullName} ({$smsResult['mobile']}) - Status: {$applicant->initial_screening}";
                } else {
                    $failedSmsCount++;
                    $results[] = "✗ Failed to send SMS to {$fullName}: {$smsResult['message']}";
                }

                // Send Email (only for schedule type)
                if ($smsType === 'schedule') {
                    $emailResult = $this->sendScheduleEmail($applicant, $message, $request);
                    if ($emailResult['success']) {
                        $sentEmailCount++;
                        $results[] = "✓ Email sent to {$fullName} ({$applicant->applicant_email})";
                    } else {
                        $failedEmailCount++;
                        $results[] = "✗ Failed to send email to {$fullName}: {$emailResult['message']}";
                    }
                }

                // Add small delay between messages to avoid rate limiting
                usleep(500000); // 0.5 second delay
            }

            $summary = "SMS sending completed. ";
            $summary .= "SMS: {$sentSmsCount} sent, {$failedSmsCount} failed. ";
            
            if ($smsType === 'schedule') {
                $summary .= "Emails: {$sentEmailCount} sent, {$failedEmailCount} failed.";
            }
            
            Log::info("Applicant Notification Summary: " . $summary);
            
            return response()->json([
                'success' => true, 
                'message' => $summary,
                'details' => $results,
                'stats' => [
                    'sms_sent' => $sentSmsCount,
                    'sms_failed' => $failedSmsCount,
                    'email_sent' => $sentEmailCount,
                    'email_failed' => $failedEmailCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Applicant notification sending error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send SMS to applicant
     */
    private function sendApplicantSms($applicant, $message, Request $request)
    {
        $mobile = $this->formatPhoneNumber($applicant->applicant_contact_number);
        
        if (!$mobile) {
            return [
                'success' => false,
                'message' => 'Invalid phone number format'
            ];
        }

        $personalizedMessage = $this->buildSmsMessage($message, $applicant, $request);

        // Send real SMS
        $smsResult = $this->sendRealSms($mobile, $personalizedMessage);

        if ($smsResult['success']) {
            Log::info("SMS Success: {$applicant->applicant_fname} {$applicant->applicant_lname} - {$mobile} - {$applicant->initial_screening}");
            return [
                'success' => true,
                'mobile' => $mobile,
                'message' => 'SMS sent successfully'
            ];
        } else {
            Log::error("SMS Failed: {$applicant->applicant_fname} {$applicant->applicant_lname} - {$mobile} - Error: {$smsResult['message']}");
            return [
                'success' => false,
                'message' => $smsResult['message']
            ];
        }
    }

    /**
     * Send schedule email to applicant
     */
    private function sendScheduleEmail($applicant, $message, Request $request)
    {
        try {
            $fullName = $applicant->applicant_fname . ' ' . $applicant->applicant_lname;
            $email = $applicant->applicant_email;

            // Build email content
            $emailData = [
                'applicant_name' => $fullName,
                'base_message' => $message,
                'schedule_what' => $request->schedule_what,
                'schedule_where' => $request->schedule_where,
                'schedule_date' => $request->schedule_date ? \Carbon\Carbon::parse($request->schedule_date)->format('F d, Y') : null,
                'schedule_time' => $request->schedule_time ? \Carbon\Carbon::parse($request->schedule_time)->format('h:i A') : null,
            ];

            $subject = "Schedule Notification: " . ($request->schedule_what ?: 'Important Update');

            Mail::send('emails.schedule-notification', $emailData, function ($mail) use ($email, $subject, $fullName) {
                $mail->to($email)
                     ->subject($subject);
            });

            Log::info("Schedule email sent to: {$fullName} ({$email})");
            return ['success' => true];

        } catch (\Exception $e) {
            Log::error("Failed to send schedule email to {$applicant->applicant_email}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create announcement for schedule type SMS
     */
    private function createScheduleAnnouncement(Request $request)
    {
        try {
            $scheduleWhat = $request->schedule_what;
            $scheduleWhere = $request->schedule_where;
            $scheduleDate = $request->schedule_date;
            $scheduleTime = $request->schedule_time;

            // Build announcement content
            $announcementContent = "<p><strong>Schedule Announcement:</strong></p>";
            $announcementContent .= "<p><strong>What:</strong> {$scheduleWhat}</p>";
            
            if ($scheduleWhere) {
                $announcementContent .= "<p><strong>Where:</strong> {$scheduleWhere}</p>";
            }
            
            if ($scheduleDate) {
                $formattedDate = \Carbon\Carbon::parse($scheduleDate)->format('F d, Y');
                $announcementContent .= "<p><strong>Date:</strong> {$formattedDate}</p>";
            }
            
            if ($scheduleTime) {
                $formattedTime = \Carbon\Carbon::parse($scheduleTime)->format('h:i A');
                $announcementContent .= "<p><strong>Time:</strong> {$formattedTime}</p>";
            }

            // Add the base message if provided
            if ($request->message) {
                $announcementContent .= "<p><strong>Additional Information:</strong></p>";
                $announcementContent .= "<p>{$request->message}</p>";
            }

            // Create announcement
            Announce::create([
                'lydopers_id' => session('lydopers')->lydopers_id,
                'announce_title' => $scheduleWhat ?: 'Schedule Announcement',
                'announce_content' => $announcementContent,
                'announce_type' => 'applicants',
                'date_posted' => now(),
            ]);

            Log::info("Schedule announcement created: " . ($scheduleWhat ?: 'Schedule Announcement'));

        } catch (\Exception $e) {
            Log::error("Failed to create schedule announcement: " . $e->getMessage());
        }
    }

    /**
     * Build the SMS message based on type
     */
    private function buildSmsMessage($baseMessage, $applicant, Request $request)
    {
        $message = $baseMessage;
        $fullName = $applicant->applicant_fname . ' ' . $applicant->applicant_lname;
        
        // Replace name placeholder
        $message = str_replace('{name}', $fullName, $message);

        // If schedule type, append schedule details
        if ($request->sms_type === 'schedule') {
            $scheduleDetails = "\n\nSchedule Details:\n";
            
            if ($request->schedule_what) {
                $scheduleDetails .= "📅 " . $request->schedule_what . "\n";
            }
            
            if ($request->schedule_where) {
                $scheduleDetails .= "📍 " . $request->schedule_where . "\n";
            }
            
            if ($request->schedule_date) {
                $formattedDate = \Carbon\Carbon::parse($request->schedule_date)->format('M d, Y');
                $scheduleDetails .= "🗓️ " . $formattedDate . "\n";
            }
            
            if ($request->schedule_time) {
                $formattedTime = \Carbon\Carbon::parse($request->schedule_time)->format('h:i A');
                $scheduleDetails .= "⏰ " . $formattedTime . "\n";
            }

            $message .= $scheduleDetails;
        }

        return $message;
    }

    /**
     * Real SMS function using iProgSMS API
     */
    public function sendRealSms($mobile, $message)
    {
        try {
            $apiKey = config('services.iprogsms.api_key');
            $apiUrl = config('services.iprogsms.api_url');

            // Check if SMS is enabled
            if (!config('services.iprogsms.enabled', true)) {
                return [
                    'success' => false,
                    'message' => 'SMS service is disabled'
                ];
            }

            Log::info("SMS API Request Preparing", [
                'api_url' => $apiUrl,
                'mobile' => $mobile,
                'message_length' => strlen($message)
            ]);

            // Using JSON body
            $payload = [
                'api_token' => $apiKey,
                'phone_number' => $mobile,
                'message' => $message
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($apiUrl, $payload);

            Log::info("SMS API Request Sent", $payload);
            Log::info("SMS API Response Status: " . $response->status());
            Log::info("SMS API Response Body: " . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check for success based on actual API response
                if (isset($responseData['status']) && $responseData['status'] == 200) {
                    return [
                        'success' => true,
                        'message' => 'SMS successfully queued for delivery',
                        'data' => $responseData
                    ];
                } 
                // Alternative success check
                elseif (isset($responseData['message']) && str_contains(strtolower($responseData['message']), 'queued')) {
                    return [
                        'success' => true,
                        'message' => 'SMS successfully queued for delivery',
                        'data' => $responseData
                    ];
                }
                else {
                    return [
                        'success' => false,
                        'message' => 'SMS API returned error: ' . ($responseData['message'] ?? 'Unknown error')
                    ];
                }
            } else {
                $errorData = $response->json();
                return [
                    'success' => false,
                    'message' => 'HTTP Error ' . $response->status() . ': ' . ($errorData['message'] ?? $response->body() ?? 'Unknown error')
                ];
            }

        } catch (\Exception $e) {
            Log::error("SMS sending exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return false;
        }

        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if number starts with 0, convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '63' . substr($phone, 1);
        }

        // Ensure it's 12 digits (63 + 10 digits)
        if (strlen($phone) === 12 && substr($phone, 0, 2) === '63') {
            return $phone;
        }

        // If it's already 11 digits with 63, return as is
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '63') {
            return $phone;
        }

        Log::warning("Invalid phone number format: " . $phone);
        return false;
    }

    public function testSms(Request $request)
    {
        try {
            $testNumber = $request->get('number', '09123456789');
            $testMessage = $request->get('message', 'Test SMS from LYDO Scholarship System');
            
            $mobile = $this->formatPhoneNumber($testNumber);
            
            if (!$mobile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format'
                ]);
            }

            Log::info("Testing SMS to: " . $mobile);
            
            $result = $this->sendRealSms($mobile, $testMessage);
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check SMS balance/status
     */
    public function checkSmsStatus()
    {
        try {
            $apiKey = config('services.iprogsms.api_key');
            
            return response()->json([
                'success' => true,
                'message' => 'SMS configuration loaded',
                'config' => [
                    'api_key' => substr($apiKey, 0, 8) . '...', // Show only first 8 chars for security
                    'api_url' => config('services.iprogsms.api_url'),
                    'enabled' => config('services.iprogsms.enabled'),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}