<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Announce;
use App\Models\FamilyIntakeSheet;
use App\Http\Controllers\SmsController;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function status(Request $request)
    {
         // Get the current logged-in mayor staff ID
    $currentStaffId = session('lydopers')->lydopers_id;

    // Get NEW applications that need initial screening by this staff
    $newApplications = DB::table("tbl_application as app")
        ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
        ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
        ->select(
            "app.application_id",
            "a.applicant_fname",
            "a.applicant_lname",
            "app.created_at",
        )
        ->where("ap.lydopers_id", $currentStaffId)
        ->where("ap.initial_screening", "Pending")
        ->orderBy("app.created_at", "desc")
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return (object) [
                "type" => "application",
                "name" => $item->applicant_fname . " " . $item->applicant_lname,
                "created_at" => $item->created_at,
            ];
        });

    // Get NEW remarks for status approval that need review by this staff
    $newRemarks = DB::table("tbl_application_personnel as ap")
        ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
        ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
        ->where("ap.lydopers_id", $currentStaffId)
        ->where('ap.initial_screening', 'Reviewed')
        ->where('ap.status', 'Pending')
        ->whereIn('ap.remarks', ['Poor', 'Ultra Poor'])
        ->select(
            "ap.remarks",
            "a.applicant_fname",
            "a.applicant_lname",
            "ap.created_at",
        )
        ->orderBy("ap.created_at", "desc")
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return (object) [
                "type" => "remark",
                "remarks" => $item->remarks,
                "name" => $item->applicant_fname . " " . $item->applicant_lname,
                "created_at" => $item->created_at,
            ];
        });
        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        // MAIN TABLE: Pending + Approved applications (Poor and Ultra Poor only)
        $query = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_mname as mname',
                'app.applicant_lname as lname',
                'app.applicant_suffix as suffix',
                'app.applicant_brgy as barangay',
                'app.applicant_school_name as school',
                'ap.initial_screening as screening',
                'ap.remarks as remarks',
                'ap.status as status'
            )
            ->where('ap.initial_screening', 'Reviewed')
            ->where('ap.status', 'Pending')
            ->whereIn('ap.remarks', ['Poor', 'Ultra Poor']);

        // SEARCH filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('app.applicant_fname', 'like', "%$search%")
                  ->orWhere('app.applicant_lname', 'like', "%$search%");
            });
        }

        // BARANGAY filter
        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        $applications = $query->paginate(100000);

        $barangays = DB::table('tbl_applicant')->distinct()->pluck('applicant_brgy');

        // LIST: Approved or Rejected
        $listQuery = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->join('tbl_lydopers as lydo', 'ap.lydopers_id', '=', 'lydo.lydopers_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_mname as mname',
                'app.applicant_lname as lname',
                'app.applicant_suffix as suffix',
                'app.applicant_brgy as barangay',
                'app.applicant_school_name as school',
                'ap.remarks as remarks',
                'ap.status as status'
            )
            ->whereIn('ap.status', ['Approved', 'Rejected'])
            ->where('lydo.lydopers_role', 'mayor_staff');

        $listApplications = $listQuery->paginate(10000000, ['*'], 'list');

        $showBadge = !session('notifications_viewed');
        $tableApplicants = $applications;

        // AJAX response for frontend
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'tableApplicants' => $tableApplicants->items(),
                'listApplications' => $listApplications->items(),
                'tablePagination' => $tableApplicants->appends(request()->query())->links()->toHtml(),
                'listPagination' => $listApplications->appends(request()->query())->links()->toHtml(),
            ]);
        }

        return view('mayor_staff.status', compact(
            'tableApplicants',
            'barangays',
            'notifications',
            'newApplications',
            'newRemarks',
            'listApplications',
            'showBadge'
        ));
    }
public function getIntakeSheet($applicationPersonnelId)
{
    try {
        \Log::info("=== START INTAKE SHEET DEBUG ===");
        \Log::info("Application Personnel ID: " . $applicationPersonnelId);

        // Get basic applicant data
        $appRow = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $applicationPersonnelId)
            ->select(
                'app.applicant_id',
                'app.applicant_fname',
                'app.applicant_mname',
                'app.applicant_lname',
                'app.applicant_suffix',
                'app.applicant_gender',
                'app.applicant_brgy',
                'ap.remarks',
                'a.application_letter',
                'a.cert_of_reg',
                'a.grade_slip',
                'a.brgy_indigency',
                'a.student_id'
            )
            ->first();

        if (!$appRow) {
            \Log::warning("No application/applicant found for application_personnel_id: {$applicationPersonnelId}");
            return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
        }

        $fullName = trim(
            ($appRow->applicant_fname ?? '') .
            ' ' . ($appRow->applicant_mname ? $appRow->applicant_mname . ' ' : '') .
            ($appRow->applicant_lname ?? '') .
            ($appRow->applicant_suffix ? ', ' . $appRow->applicant_suffix : '')
        );

        $documentStatuses = [
            'application_letter' => $this->evaluateDocumentStatus($appRow->application_letter),
            'cert_reg' => $this->evaluateDocumentStatus($appRow->cert_of_reg),
            'grade_slip' => $this->evaluateDocumentStatus($appRow->grade_slip),
            'brgy_indigency' => $this->evaluateDocumentStatus($appRow->brgy_indigency),
            'student_id' => $this->evaluateDocumentStatus($appRow->student_id),
        ];

        // Check if intake sheet exists
        $intakeSheet = FamilyIntakeSheet::where('application_personnel_id', $applicationPersonnelId)->first();

        \Log::info("Intake Sheet Found: " . ($intakeSheet ? 'Yes' : 'No'));

        if (!$intakeSheet) {
            \Log::info("No intake sheet found for ID: " . $applicationPersonnelId);
            
            $data = [
                'applicant_name' => $fullName,
                'applicant_gender' => $appRow->applicant_gender ?? null,
                'remarks' => $appRow->remarks ?? null,
                'head_barangay' => $appRow->applicant_brgy ?? null,
                
                // FIXED: Document paths - corrected to storage/document (singular)
                    'doc_application_letter' => $appRow->application_letter ? asset('storage/' . $appRow->application_letter) : null,
    'doc_cert_reg' => $appRow->cert_of_reg ? asset('storage/' . $appRow->cert_of_reg) : null,
    'doc_grade_slip' => $appRow->grade_slip ? asset('storage/' . $appRow->grade_slip) : null,
    'doc_brgy_indigency' => $appRow->brgy_indigency ? asset('storage/' . $appRow->brgy_indigency) : null,
    'doc_student_id' => $appRow->student_id ? asset('storage/' . $appRow->student_id) : null,
                // Empty arrays - using frontend expected field names
                'family_members' => [],
                'social_service_records' => [],
            ];

            return response()->json([
                'success' => true, 
                'intakeSheet' => $data,
                'intake_sheet' => $data
            ], 200);
        }

        // DEBUG: Check the actual database values
        \Log::info("=== DATABASE VALUES DEBUG ===");
        \Log::info("Family Members DB Value: " . ($intakeSheet->family_members ?? 'NULL'));
        \Log::info("Social Service Records DB Value: " . ($intakeSheet->social_service_records ?? 'NULL'));
        \Log::info("RV Service Records DB Value: " . ($intakeSheet->rv_service_records ?? 'NULL'));

        // Enhanced JSON parsing with better NULL handling
        $familyMembers = $this->parseJsonField($intakeSheet->family_members, 'family_members');
        
        // FIX: Use rv_service_records instead of social_service_records
        $socialServiceRecords = $this->parseJsonField($intakeSheet->rv_service_records, 'rv_service_records');

        // FIXED TRANSFORMATION: Use original field names to avoid mapping issues
        $transformedFamilyMembers = [];
        foreach ($familyMembers as $member) {
            $transformedFamilyMembers[] = [
                'NAME' => $member['name'] ?? $member['NAME'] ?? '-',
                'RELATION' => $member['relationship'] ?? $member['relation'] ?? $member['RELATION'] ?? '-',
                'BIRTHDATE' => $member['birthdate'] ?? $member['BIRTHDATE'] ?? $member['birth_date'] ?? '-',
                'AGE' => $member['age'] ?? $member['AGE'] ?? '-',
                'SEX' => $member['sex'] ?? $member['gender'] ?? $member['SEX'] ?? '-',
                'CIVIL STATUS' => $member['civil_status'] ?? $member['CIVIL STATUS'] ?? $member['civilStatus'] ?? '-',
                'EDUCATIONAL ATTAINMENT' => $member['education'] ?? $member['EDUCATIONAL ATTAINMENT'] ?? $member['educational_attainment'] ?? '-',
                'OCCUPATION' => $member['occupation'] ?? $member['OCCUPATION'] ?? '-',
                'INCOME' => $member['monthly_income'] ?? $member['income'] ?? $member['INCOME'] ?? '-',
                'REMARKS' => $member['remarks'] ?? $member['REMARKS'] ?? '-'
            ];
        }

        $transformedServiceRecords = [];
        foreach ($socialServiceRecords as $record) {
            $transformedServiceRecords[] = [
                'DATE' => $record['date'] ?? $record['DATE'] ?? '-',
                'PROBLEM/NEED' => $record['problem'] ?? $record['PROBLEM/NEED'] ?? $record['problem_need'] ?? '-',
                'ACTION/ASSISTANCE GIVEN' => $record['action'] ?? $record['ACTION/ASSISTANCE GIVEN'] ?? $record['action_assistance'] ?? '-',
                'REMARKS' => $record['remarks'] ?? $record['REMARKS'] ?? '-'
            ];
        }

        \Log::info("Final Counts - Family Members: " . count($transformedFamilyMembers) . ", Social Service Records: " . count($transformedServiceRecords));

        // FORMAT HOUSE AND LOT INFORMATION WITH RENT AMOUNTS
        $houseDisplay = $intakeSheet->house_house ?? null;
        $lotDisplay = $intakeSheet->house_lot ?? null;

        // If house is rented, combine with house_rent amount
        if ($houseDisplay === 'Rent' && !empty($intakeSheet->house_rent)) {
            $houseDisplay = 'Rent - ₱' . number_format($intakeSheet->house_rent, 2);
        }

        // If lot is rented, combine with lot_rent amount
        if ($lotDisplay === 'Rent' && !empty($intakeSheet->lot_rent)) {
            $lotDisplay = 'Rent - ₱' . number_format($intakeSheet->lot_rent, 2);
        }

        // Prepare response data
        $data = [
            // Applicant Information
            'applicant_name' => $fullName,
            'applicant_gender' => $appRow->applicant_gender ?? null,
            'remarks' => $appRow->remarks ?? null,
            
            // Head of Family Information
            'head_4ps' => $intakeSheet->head_4ps ?? null,
            'head_ipno' => $intakeSheet->head_ipno ?? null,
            'head_address' => $intakeSheet->head_address ?? null,
            'head_zone' => $intakeSheet->head_zone ?? null,
            'head_barangay' => $intakeSheet->head_barangay ?? $appRow->applicant_brgy ?? null,
            'head_pob' => $intakeSheet->head_pob ?? null,
            'head_dob' => $intakeSheet->head_dob ? (string)$intakeSheet->head_dob : null,
            'head_educ' => $intakeSheet->head_educ ?? null,
            'head_occ' => $intakeSheet->head_occ ?? null,
            'head_religion' => $intakeSheet->head_religion ?? null,
            
            // Household Information
            'serial_number' => $intakeSheet->serial_number ?? null,
            'house_total_income' => $intakeSheet->house_total_income ?? null,
            'house_net_income' => $intakeSheet->house_net_income ?? null,
            'other_income' => $intakeSheet->other_income ?? null,
            'house_house' => $houseDisplay,
            'house_lot' => $lotDisplay,
            'house_electric' => $intakeSheet->house_electric ?? null,
            'house_water' => $intakeSheet->house_water ?? null,
            
            // Family Members and Service Records - USING TRANSFORMED DATA
            'family_members' => $transformedFamilyMembers,
            'social_service_records' => $transformedServiceRecords,
            
            // Signatures
            'worker_name' => $intakeSheet->worker_name ?? null,
            'officer_name' => $intakeSheet->officer_name ?? null,
            'date_entry' => $intakeSheet->date_entry ? (string)$intakeSheet->date_entry : null,
           
            // FIXED: Document paths - corrected to storage/document (singular)
            'doc_application_letter' => $appRow->application_letter ? asset('storage/' . $appRow->application_letter) : null,
            'doc_cert_reg' => $appRow->cert_of_reg ? asset('storage/' . $appRow->cert_of_reg) : null,
            'doc_grade_slip' => $appRow->grade_slip ? asset('storage/' . $appRow->grade_slip) : null,
            'doc_brgy_indigency' => $appRow->brgy_indigency ? asset('storage/' . $appRow->brgy_indigency) : null,
            'doc_student_id' => $appRow->student_id ? asset('storage/' . $appRow->student_id) : null,

            // Raw values for reference
            'house_rent' => $intakeSheet->house_rent ?? null,
            'lot_rent' => $intakeSheet->lot_rent ?? null,
        ];

        \Log::info("=== END INTAKE SHEET DEBUG ===");

        return response()->json([
            'success' => true, 
            'intakeSheet' => $data,
            'intake_sheet' => $data,
            'debug_info' => [
                'family_members_count' => count($transformedFamilyMembers),
                'social_service_records_count' => count($transformedServiceRecords),
                'has_intake_sheet' => true,
                'family_members_original' => $familyMembers,
                'social_service_records_original' => $socialServiceRecords
            ]
        ], 200);
        
    } catch (\Exception $e) {
        \Log::error('Error in getIntakeSheet: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error loading intake sheet data.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Evaluate document status based on your criteria
 */
private function evaluateDocumentStatus($documentPath)
{
    if (!$documentPath) {
        return 'Missing';
    }
    
    // Add your evaluation logic here
    // This is a placeholder - replace with your actual evaluation criteria
    $fileExists = Storage::exists($documentPath);
    
    if (!$fileExists) {
        return 'Bad';
    }
    
    // You can add more sophisticated checks here
    // For example: file size, format, content validation, etc.
    
    return 'Good';
}
/**
 * Helper method to parse JSON fields with comprehensive error handling
 */
private function parseJsonField($fieldValue, $fieldName)
{
    if ($fieldValue === null) {
        \Log::info("Field '{$fieldName}' is NULL, returning empty array");
        return [];
    }

    if (empty(trim($fieldValue))) {
        \Log::info("Field '{$fieldName}' is empty string, returning empty array");
        return [];
    }

    if ($fieldValue === 'null' || $fieldValue === 'NULL') {
        \Log::info("Field '{$fieldName}' is string 'null', returning empty array");
        return [];
    }

    try {
        $decoded = json_decode($fieldValue, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::warning("JSON decode error for {$fieldName}: " . json_last_error_msg());
            \Log::warning("Raw value that failed to decode: " . $fieldValue);
            return [];
        }

        if (!is_array($decoded)) {
            \Log::warning("Decoded {$fieldName} is not an array, type: " . gettype($decoded));
            return [];
        }

        \Log::info("Successfully decoded {$fieldName}, count: " . count($decoded));
        return $decoded;

    } catch (\Exception $e) {
        \Log::error("Exception decoding {$fieldName}: " . $e->getMessage());
        return [];
    }
}
    public function updateStatus(Request $request, $id)
    {
        // Log the start of the update process
        Log::info("Starting status update for application_personnel_id: {$id}", [
            'status' => $request->status,
            'reason' => $request->reason ?? 'N/A'
        ]);

        try {
            // Validate request
            $request->validate([
                'status' => 'required|in:Approved,Rejected',
                'reason' => 'required_if:status,Rejected|string|max:1000'
            ]);

            // Get the application_id and applicant email from application_personnel and applicant
            $applicationPersonnel = DB::table('tbl_application_personnel as ap')
                ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
                ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
                ->where('ap.application_personnel_id', $id)
                ->select('a.application_id', 'app.applicant_email', 'app.applicant_fname', 'app.applicant_lname', 'app.applicant_contact_number')
                ->first();

            if (!$applicationPersonnel) {
                Log::warning("Application not found for application_personnel_id: {$id}");
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Application not found.']);
                }
                return back()->with('error', 'Application not found.');
            }

            // Update the status and reason if rejected
            $updateData = ['status' => $request->status, 'updated_at' => now()];
            if ($request->status === 'Rejected') {
                $updateData['rejection_reason'] = $request->reason;
            }

            DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $id)
                ->update($updateData);

            Log::info("Status updated successfully for application_personnel_id: {$id}", [
                'new_status' => $request->status,
                'rejection_reason' => $request->reason ?? null
            ]);

            // If status is Approved, add to tbl_scholar if not already exists and send email
            if ($request->status === 'Approved') {
                $existingScholar = Scholar::where('application_id', $applicationPersonnel->application_id)->first();

                if (!$existingScholar) {
                    $scholar = Scholar::create([
                        'application_id' => $applicationPersonnel->application_id,
                        'scholar_username' => $request->username ?? 'default_username',
                        'scholar_pass' => bcrypt($request->password ?? 'default123'),
                        'date_activated' => now(),
                        'scholar_status' => 'Active',
                    ]);
                    Log::info("Scholar record created for application_id: {$applicationPersonnel->application_id}");
                } else {
                    $scholar = $existingScholar;
                    Log::info("Scholar record already exists for application_id: {$applicationPersonnel->application_id}");
                }

                // Send email with registration link
                try {
                    $registrationLink = \Illuminate\Support\Facades\URL::signedRoute('scholar.scholar_reg', ['scholar_id' => $scholar->scholar_id]);
                    $emailData = [
                        'applicant_fname' => $applicationPersonnel->applicant_fname,
                        'applicant_lname' => $applicationPersonnel->applicant_lname,
                        'registration_link' => $registrationLink,
                    ];

                    Mail::send('emails.scholar-registration-link', $emailData, function ($message) use ($applicationPersonnel) {
                        $message->to($applicationPersonnel->applicant_email)
                            ->subject('Scholar Registration - Update Your Account')
                            ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
                    });
                    Log::info("Approval email sent successfully to: " . $applicationPersonnel->applicant_email);
                } catch (\Exception $e) {
                    Log::error("Failed to send approval email to: " . $applicationPersonnel->applicant_email . " - " . $e->getMessage());
                    // Continue processing even if email fails
                }

                // Send SMS notification
                try {
                    $mobile = $applicationPersonnel->applicant_contact_number;
                    Log::info("Original mobile number: " . $mobile);
                    if (preg_match('/^0\d{10}$/', $mobile)) {
                        $mobile = '+63' . substr($mobile, 1);
                    } elseif (preg_match('/^9\d{9}$/', $mobile)) {
                        $mobile = '+63' . $mobile;
                    }
                    Log::info("Formatted mobile number: " . $mobile);
                    $smsController = new SmsController();
                    $smsMessage = "Congratulations {$applicationPersonnel->applicant_fname}! Your scholarship application has been approved. Update your username/password: {$registrationLink}";
                    Log::info("Sending SMS to: " . $mobile . " with message: " . $smsMessage);
                    $result = $smsController->sendSms($mobile, $smsMessage);
                    Log::info("SMS send result: " . ($result ? 'Success' : 'Failed'));
                } catch (\Exception $e) {
                    Log::error("Failed to send approval SMS to: " . $mobile . " - " . $e->getMessage());
                    // Continue processing even if SMS fails
                }
            }

            // If status is Rejected, send rejection email
            if ($request->status === 'Rejected') {
                try {
                    $emailData = [
                        'applicant_fname' => $applicationPersonnel->applicant_fname,
                        'applicant_lname' => $applicationPersonnel->applicant_lname,
                        'reason' => $request->reason,
                    ];

                    Mail::send('emails.scholar-status-rejection', $emailData, function ($message) use ($applicationPersonnel) {
                        $message->to($applicationPersonnel->applicant_email)
                            ->subject('Scholarship Application Update')
                            ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
                    });
                    Log::info("Rejection email sent successfully to: " . $applicationPersonnel->applicant_email);
                } catch (\Exception $e) {
                    Log::error("Failed to send rejection email to: " . $applicationPersonnel->applicant_email . " - " . $e->getMessage());
                    // Continue processing even if email fails
                }
            }

            Log::info("Status update process completed successfully for application_personnel_id: {$id}");

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
            }

            return back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            Log::error("Unexpected error in updateStatus for application_personnel_id: {$id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating status.'
                ]);
            }

            return back()->with('error', 'An error occurred while updating status.');
        }
    }
public function getNotificationCount(Request $request)
{
    $currentStaffId = session('lydopers')->lydopers_id;
    
    // Count applications that need initial screening
    $applicationCount = DB::table("tbl_application_personnel as ap")
        ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
        ->where("ap.lydopers_id", $currentStaffId)
        ->where("ap.initial_screening", "Pending")
        ->count();

    // Count remarks that need status approval
    $remarkCount = DB::table("tbl_application_personnel as ap")
        ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
        ->where("ap.lydopers_id", $currentStaffId)
        ->where('ap.initial_screening', 'Reviewed')
        ->where('ap.status', 'Pending')
        ->whereIn('ap.remarks', ['Poor', 'Ultra Poor'])
        ->count();

    $totalCount = $applicationCount + $remarkCount;

    return response()->json([
        'count' => $totalCount,
        'application_count' => $applicationCount,
        'remark_count' => $remarkCount
    ]);
}
}
