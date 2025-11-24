<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

            $sentCount = 0;
            $failedCount = 0;
            $results = [];

            // Create announcement if it's a schedule type
            if ($smsType === 'schedule' && $request->schedule_what) {
                $this->createScheduleAnnouncement($request);
            }

            foreach ($applicants as $applicant) {
                $mobile = $this->formatPhoneNumber($applicant->applicant_contact_number);
                
                if (!$mobile) {
                    $results[] = "Invalid phone number for {$applicant->applicant_fname} {$applicant->applicant_lname} ({$applicant->applicant_contact_number})";
                    $failedCount++;
                    continue;
                }

                $personalizedMessage = $this->buildSmsMessage($message, $applicant, $request);
                $fullName = $applicant->applicant_fname . ' ' . $applicant->applicant_lname;

                // Send real SMS
                $smsResult = $this->sendRealSms($mobile, $personalizedMessage);

                if ($smsResult['success']) {
                    $sentCount++;
                    $results[] = "✓ SMS sent to {$fullName} ({$mobile}) - Status: {$applicant->initial_screening}";
                    Log::info("SMS Success: {$fullName} - {$mobile} - {$applicant->initial_screening}");
                } else {
                    $failedCount++;
                    $results[] = "✗ Failed to send SMS to {$fullName}: {$smsResult['message']}";
                    Log::error("SMS Failed: {$fullName} - {$mobile} - Error: {$smsResult['message']}");
                }

                // Add small delay between messages to avoid rate limiting
                usleep(500000); // 0.5 second delay
            }

            $summary = "SMS sending completed.";
            
            Log::info("Applicant SMS Summary: " . $summary);
            
            return response()->json([
                'success' => true, 
                'message' => $summary,
                'details' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("Applicant SMS sending error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ]);
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
            $announcementContent = "Schedule Announcement:\n\n";
            $announcementContent .= "What: {$scheduleWhat}\n";
            
            if ($scheduleWhere) {
                $announcementContent .= "Where: {$scheduleWhere}\n";
            }
            
            if ($scheduleDate) {
                $formattedDate = \Carbon\Carbon::parse($scheduleDate)->format('F d, Y');
                $announcementContent .= "Date: {$formattedDate}\n";
            }
            
            if ($scheduleTime) {
                $formattedTime = \Carbon\Carbon::parse($scheduleTime)->format('h:i A');
                $announcementContent .= "Time: {$formattedTime}\n";
            }

            // Create announcement
            Announce::create([
                'lydopers_id' => session('lydopers')->lydopers_id,
                'announce_title' => $scheduleWhat,
                'announce_content' => $announcementContent,
                'announce_type' => 'applicants',
                'date_posted' => now(),
            ]);

            Log::info("Schedule announcement created: {$scheduleWhat}");

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
public function sendRealSms($mobile, $message)    {
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
    /**
     * Alternative method using query parameters
     */
    private function sendRealSmsQueryParams($mobile, $message)
    {
        try {
            $apiKey = config('services.iprogsms.api_key');
            $apiUrl = config('services.iprogsms.api_url');

            // Build URL with query parameters
            $url = $apiUrl . '?' . http_build_query([
                'api_token' => $apiKey,
                'phone_number' => $mobile,
                'message' => $message
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->post($url);

            Log::info("SMS API Query Request: " . $url);
            Log::info("SMS API Response: " . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'SMS API error: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error("SMS sending error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage()
            ];
        }
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