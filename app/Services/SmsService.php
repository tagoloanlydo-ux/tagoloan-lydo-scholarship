<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SmsController extends Controller
{
    public function sendSmsToScholars(Request $request)
    {
        $request->validate([
            'selected_emails' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $selectedEmails = explode(',', $request->selected_emails);
        $selectedEmails = array_map('trim', $selectedEmails);
        $message = $request->message;

        try {
            // Get scholar contact numbers for the selected emails
            $scholars = DB::table('tbl_scholar as s')
                ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                ->whereIn('a.applicant_email', $selectedEmails)
                ->where('s.scholar_status', 'active')
                ->select(
                    'a.applicant_email',
                    'a.applicant_contact_number',
                    'a.applicant_fname',
                    'a.applicant_lname'
                )
                ->get();

            if ($scholars->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid scholars found for the selected emails.']);
            }

            $sentCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($scholars as $scholar) {
                $mobile = $this->formatPhoneNumber($scholar->applicant_contact_number);
                
                if (!$mobile) {
                    $results[] = "Invalid phone number for {$scholar->applicant_fname} {$scholar->applicant_lname}";
                    $failedCount++;
                    continue;
                }

                $personalizedMessage = $message;
                $fullName = $scholar->applicant_fname . ' ' . $scholar->applicant_lname;
                $personalizedMessage = str_replace('{name}', $fullName, $personalizedMessage);

                $smsResult = $this->sendSms($mobile, $personalizedMessage);

                if ($smsResult['success']) {
                    $sentCount++;
                    $results[] = "SMS sent to {$fullName} ({$mobile})";
                } else {
                    $failedCount++;
                    $results[] = "Failed to send SMS to {$fullName}: {$smsResult['message']}";
                }
            }

            $summary = "SMS sending completed. Success: {$sentCount}, Failed: {$failedCount}";
            
            return response()->json([
                'success' => true, 
                'message' => $summary,
                'details' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ]);
        }
    }

    private function sendSms($mobile, $message)
    {
        try {
            $apiKey = config('services.iprogsms.api_key');
            $apiUrl = config('services.iprogsms.api_url');
            $senderName = config('services.iprogsms.sender_name');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'mobile' => $mobile,
                'message' => $message,
                'sender_name' => $senderName,
            ]);

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

        return false;
    }
}