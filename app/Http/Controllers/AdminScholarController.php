<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AdminScholarController extends Controller
{
public function scholar(Request $request)
{
    // Get scholars with applicant information - include both active and inactive
    $query = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            's.scholar_id',
            's.scholar_status',
            's.date_activated',
            'a.applicant_id',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            'a.applicant_email',
            'a.applicant_contact_number',
            'a.applicant_school_name',
            'a.applicant_course',
            'a.applicant_year_level',
            'a.applicant_acad_year'
        );

    // Apply status filter - default to active
    $statusFilter = $request->get('status', 'active');
    if ($statusFilter === 'active') {
        $query->where('s.scholar_status', 'active');
    } elseif ($statusFilter === 'inactive') {
        $query->where('s.scholar_status', 'inactive');
    }
    // If 'all' is selected, show both active and inactive

    // Apply other filters
    if ($request->has('search') && !empty($request->search)) {
        $query->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('barangay') && !empty($request->barangay)) {
        $query->where('a.applicant_brgy', $request->barangay);
    }

    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $query->where('a.applicant_acad_year', $request->academic_year);
    }

   $scholars = $query->get();

    // Get distinct barangays for filter dropdown
    $barangays = DB::table('tbl_applicant')
        ->select('applicant_brgy')
        ->distinct()
        ->orderBy('applicant_brgy', 'asc')
        ->pluck('applicant_brgy');

    // Get distinct academic years for filter dropdown
    $academicYears = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->distinct()
        ->orderBy('applicant_acad_year', 'desc')
        ->pluck('applicant_acad_year');

    return view('lydo_admin.scholar', compact( 'scholars', 'barangays', 'academicYears', 'statusFilter'));
}
public function getScholarDocuments($scholar_id)
{
    try {
        $documents = DB::table('tbl_renewal')
            ->where('scholar_id', $scholar_id)
            ->select(
                'renewal_cert_of_reg',
                'renewal_grade_slip',
                'renewal_brgy_indigency',
                'renewal_semester',
                'renewal_acad_year',
                'date_submitted',
                'renewal_status'
            )
            ->get();

        // Process documents to generate proper URLs
        $processedDocuments = $documents->map(function ($doc) {
            return [
                'renewal_cert_of_reg' => $this->getDocumentUrl($doc->renewal_cert_of_reg),
                'renewal_grade_slip' => $this->getDocumentUrl($doc->renewal_grade_slip),
                'renewal_brgy_indigency' => $this->getDocumentUrl($doc->renewal_brgy_indigency),
                'renewal_semester' => $doc->renewal_semester,
                'renewal_acad_year' => $doc->renewal_acad_year,
                'date_submitted' => $doc->date_submitted,
                'renewal_status' => $doc->renewal_status,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $processedDocuments
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching documents: ' . $e->getMessage()
        ], 500);
    }
}
// Helper method to generate proper document URLs
private function getDocumentUrl($filePath)
{
    if (empty($filePath)) {
        return null;
    }

    // If it's already a full URL, return as is
    if (filter_var($filePath, FILTER_VALIDATE_URL)) {
        return $filePath;
    }

    // If it starts with storage/, convert to proper URL
    if (strpos($filePath, 'storage/') === 0) {
        return asset($filePath);
    }

    // If it's just a filename, assume it's in storage/renewal
    if (!str_contains($filePath, '/')) {
        return asset('storage/renewal/' . $filePath);
    }

    // For other cases, try to generate URL
    return asset('storage/' . ltrim($filePath, '/'));
}

    public function sendEmailToScholars(Request $request)
    {
        $request->validate([
            'selected_emails' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_type' => 'required|string|in:bulk,individual',
        ]);

        $selectedEmails = explode(',', $request->selected_emails);
        $selectedEmails = array_map('trim', $selectedEmails);
        $subject = $request->subject;
        $message = $request->message;
        $sendType = $request->send_type;

        try {
            // Get scholar details for the selected emails
            $scholars = DB::table('tbl_scholar as s')
                ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                ->whereIn('a.applicant_email', $selectedEmails)
                ->where('s.scholar_status', 'active')
                ->select(
                    'a.applicant_email',
                    'a.applicant_fname',
                    'a.applicant_lname',
                    'a.applicant_mname',
                    'a.applicant_suffix'
                )
                ->get();

            if ($scholars->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid scholars found for the selected emails.']);
            }

            $sentCount = 0;

            if ($sendType === 'bulk') {
                // Send one email to all scholars
                $recipientEmails = $scholars->pluck('applicant_email')->toArray();

                Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $message], function ($mail) use ($recipientEmails, $subject) {
                    $mail->to($recipientEmails)
                         ->subject($subject);
                });

                $sentCount = count($recipientEmails);
            } else {
                // Send individual emails
                foreach ($scholars as $scholar) {
                    $personalizedMessage = $message;
                    $fullName = $scholar->applicant_fname . ' ' . ($scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '') . $scholar->applicant_lname . ($scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '');

                    // Replace placeholders if any
                    $personalizedMessage = str_replace('{name}', $fullName, $personalizedMessage);

                    Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $personalizedMessage], function ($mail) use ($scholar, $subject) {
                        $mail->to($scholar->applicant_email)
                             ->subject($subject);
                    });

                    $sentCount++;
                }
            }

            return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . $sentCount . ' scholar(s)!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }
public function generateScholarsPdf(Request $request)
{
    try {
        \Log::info('Scholars PDF Request:', $request->all());
        
        set_time_limit(120);
        
        // Get scholars with applicant information
        $query = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                's.scholar_id',
                's.scholar_status',
                's.date_activated',
                'a.applicant_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                'a.applicant_brgy',
                'a.applicant_email',
                'a.applicant_contact_number',
                'a.applicant_school_name',
                'a.applicant_course',
                'a.applicant_year_level',
                'a.applicant_acad_year'
            );

        // Apply status filter
        $statusFilter = $request->get('status', 'active');
        if ($statusFilter === 'active') {
            $query->where('s.scholar_status', 'active');
        } elseif ($statusFilter === 'inactive') {
            $query->where('s.scholar_status', 'inactive');
        }

        // Apply other filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('a.applicant_fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('barangay')) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $query->where('a.applicant_acad_year', $request->academic_year);
        }

        // Get all records without limit
        $scholars = $query
            ->orderBy('a.applicant_lname', 'asc')
            ->orderBy('a.applicant_fname', 'asc')
            ->orderBy('a.applicant_mname', 'asc')
            ->get();

        \Log::info("Final scholars count: {$scholars->count()}");

        // Get filter info - ALWAYS include this for all pages
        $filters = [];
        if ($request->filled('search')) $filters[] = 'Search: ' . $request->search;
        if ($request->filled('barangay')) $filters[] = 'Barangay: ' . $request->barangay;
        if ($request->filled('academic_year')) $filters[] = 'Academic Year: ' . $request->academic_year;
        if ($request->filled('status')) {
            $filters[] = 'Status: ' . ucfirst($request->status);
        } else {
            $filters[] = 'Status: Active'; // Default
        }
        
        // Add record count to filters so it shows on all pages
        $filters[] = 'Total Records: ' . $scholars->count();

        $title = 'Scholars List Report';

        \Log::info("Generating PDF with {$scholars->count()} scholars");

        $pdf = Pdf::loadView('pdf.scholars-print', compact('scholars', 'filters', 'title'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream('scholars-list-' . date('Y-m-d-H-i-s') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('Scholars PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}

    public function getAllFilteredScholars(Request $request)
    {
        $query = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select('s.scholar_id', 'a.applicant_email')
            ->where('s.scholar_status', 'active');

        // Apply the same filters as the main scholar method
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('a.applicant_acad_year', $request->academic_year);
        }

        // Get all scholar emails that match the filters
        $scholarEmails = $query->pluck('a.applicant_email');

        return response()->json(['scholar_emails' => $scholarEmails]);
    }
public function sendSmsToScholars(Request $request)
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
                'a.applicant_lname',
                'a.applicant_mname',
                'a.applicant_suffix'
            )
            ->get();

        if ($scholars->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No valid scholars found for the selected emails.']);
        }

        // Create announcement if it's a schedule type
        if ($smsType === 'schedule' && $request->schedule_what) {
            $this->createScholarScheduleAnnouncement($request);
        }

        $sentCount = 0;
        $failedCount = 0;
        $results = [];

        // Send email notification for schedule type SMS
        if ($smsType === 'schedule') {
            $this->sendScheduleEmailNotification($scholars, $request);
        }

        foreach ($scholars as $scholar) {
            $mobile = $this->formatPhoneNumber($scholar->applicant_contact_number);
            
            if (!$mobile) {
                $results[] = "Invalid phone number for {$scholar->applicant_fname} {$scholar->applicant_lname} ({$scholar->applicant_contact_number})";
                $failedCount++;
                continue;
            }

            $personalizedMessage = $this->buildScholarSmsMessage($message, $scholar, $request);
            $fullName = $scholar->applicant_fname . ' ' . ($scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '') . $scholar->applicant_lname . ($scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '');

            // Send real SMS using the existing SmsController
            $smsController = new SmsController();
            $smsResult = $smsController->sendRealSms($mobile, $personalizedMessage);
            
            if ($smsResult['success']) {
                $sentCount++;
                $results[] = "✓ SMS sent to {$fullName} ({$mobile})";
                Log::info("Scholar SMS Success: {$fullName} - {$mobile}");
            } else {
                $failedCount++;
                $results[] = "✗ Failed to send SMS to {$fullName}: {$smsResult['message']}";
                Log::error("Scholar SMS Failed: {$fullName} - {$mobile} - Error: {$smsResult['message']}");
            }

            // Add small delay between messages to avoid rate limiting
            usleep(500000); // 0.5 second delay
        }

        $summary = "SMS sending completed. Sent: {$sentCount}, Failed: {$failedCount}";
        
        // Add email notification info if schedule type
        if ($smsType === 'schedule') {
            $summary .= " | Email notification sent to all selected scholars";
        }
        
        Log::info("Scholar SMS Summary: " . $summary);
        
        return response()->json([
            'success' => true, 
            'message' => $summary,
            'details' => $results
        ]);

    } catch (\Exception $e) {
        Log::error("Scholar SMS sending error: " . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Failed to send SMS: ' . $e->getMessage()
        ]);
    }
}

/**
 * Send email notification for schedule type SMS
 */
private function sendScheduleEmailNotification($scholars, Request $request)
{
    try {
        $scheduleWhat = $request->schedule_what;
        $scheduleWhere = $request->schedule_where;
        $scheduleDate = $request->schedule_date;
        $scheduleTime = $request->schedule_time;

        // Build email subject and message
        $emailSubject = "Schedule Announcement: {$scheduleWhat}";
        
        $emailMessage = "Dear Scholar,\n\n";
        $emailMessage .= "You have received an important schedule announcement:\n\n";
        $emailMessage .= "What: {$scheduleWhat}\n";
        
        if ($scheduleWhere) {
            $emailMessage .= "Where: {$scheduleWhere}\n";
        }
        
        if ($scheduleDate) {
            $formattedDate = \Carbon\Carbon::parse($scheduleDate)->format('F d, Y');
            $emailMessage .= "Date: {$formattedDate}\n";
        }
        
        if ($scheduleTime) {
            $formattedTime = \Carbon\Carbon::parse($scheduleTime)->format('h:i A');
            $emailMessage .= "Time: {$formattedTime}\n";
        }
        
        $emailMessage .= "\nThis message has also been sent to your mobile phone via SMS.\n\n";
        $emailMessage .= "Best regards,\nLYDO Scholarship Team";

        // Send email to all scholars
        $recipientEmails = $scholars->pluck('applicant_email')->toArray();

        Mail::send('emails.plain-email', [
            'subject' => $emailSubject, 
            'emailMessage' => $emailMessage
        ], function ($mail) use ($recipientEmails, $emailSubject) {
            $mail->to($recipientEmails)
                 ->subject($emailSubject);
        });

        Log::info("Schedule email notification sent to " . count($recipientEmails) . " scholars");

    } catch (\Exception $e) {
        Log::error("Failed to send schedule email notification: " . $e->getMessage());
    }
}

/**
 * Create announcement for schedule type SMS for scholars
 */
private function createScholarScheduleAnnouncement(Request $request)
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

        // Create announcement with type 'scholar'
        DB::table('tbl_announce')->insert([
            'lydopers_id' => session('lydopers')->lydopers_id,
            'announce_title' => $scheduleWhat,
            'announce_content' => $announcementContent,
            'announce_type' => 'scholar',
            'date_posted' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Scholar schedule announcement created: {$scheduleWhat}");

    } catch (\Exception $e) {
        Log::error("Failed to create scholar schedule announcement: " . $e->getMessage());
    }
}

/**
 * Build the SMS message for scholars based on type
 */
private function buildScholarSmsMessage($baseMessage, $scholar, Request $request)
{
    $message = $baseMessage;
    $fullName = $scholar->applicant_fname . ' ' . ($scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '') . $scholar->applicant_lname . ($scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '');
    
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
 * Send SMS to scholar using the same logic as SmsController
 */
private function sendScholarSms($mobile, $message)
{
    try {
        // Your SMS API configuration - use the same as in SmsController
        $apiUrl = env('SMS_API_URL', 'https://api.itexmo.com/api/broadcast');
        $apiKey = env('SMS_API_KEY', '');
        $apiPassword = env('SMS_API_PASSWORD', '');
        $senderId = env('SMS_SENDER_ID', 'LYDO_SCHOLAR');

        // Prepare the request data
        $postData = [
            'Email' => $apiKey,
            'Password' => $apiPassword,
            'Recipients' => [$mobile],
            'Message' => $message,
            'ApiCode' => $senderId,
            'SenderId' => $senderId
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log the response for debugging
        Log::info("SMS API Response for {$mobile}: " . $response);

        // Check for cURL errors
        if ($curlError) {
            Log::error("SMS cURL Error for {$mobile}: " . $curlError);
            return [
                'success' => false,
                'message' => 'SMS gateway connection failed: ' . $curlError
            ];
        }

        // Parse the response
        $responseData = json_decode($response, true);

        // Check if SMS was sent successfully
        if ($httpCode === 200 && isset($responseData['response']) && $responseData['response'] === 'success') {
            return [
                'success' => true,
                'message' => 'SMS sent successfully'
            ];
        } else {
            $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
            Log::error("SMS API Error for {$mobile}: " . $errorMessage);
            return [
                'success' => false,
                'message' => 'SMS failed: ' . $errorMessage
            ];
        }

    } catch (\Exception $e) {
        Log::error("SMS sending exception for {$mobile}: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'SMS exception: ' . $e->getMessage()
        ];
    }
}

/**
 * Alternative simple SMS sending using different API if needed
 */
private function sendScholarSmsAlternative($mobile, $message)
{
    try {
        // Alternative SMS API - adjust based on your provider
        $apiKey = env('SMS_API_KEY');
        $apiSecret = env('SMS_API_SECRET');
        
        // Example using a different SMS gateway
        $postData = http_build_query([
            'apikey' => $apiKey,
            'secret' => $apiSecret,
            'number' => $mobile,
            'message' => $message,
            'sendername' => 'LYDO_SCHOLAR'
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.some-sms-provider.com/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        // Check response and return accordingly
        if (strpos($response, 'OK') !== false) {
            return ['success' => true, 'message' => 'SMS sent successfully'];
        } else {
            return ['success' => false, 'message' => 'SMS sending failed'];
        }

    } catch (\Exception $e) {
        return ['success' => false, 'message' => 'SMS exception: ' . $e->getMessage()];
    }
}
}
