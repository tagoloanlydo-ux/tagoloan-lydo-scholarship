<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
    // NEW APPLICATIONS
    $newApplications = DB::table("tbl_application as app")
        ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
        ->select(
            "app.application_id",
            "a.applicant_fname",
            "a.applicant_lname",
            "app.created_at"
        )
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

    // NEW REMARKS
    $newRemarks = DB::table("tbl_application_personnel as ap")
        ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
        ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
        ->whereIn("ap.remarks", ["Poor", "Non Poor", "Ultra Poor", "Non Indigenous"])
        ->select("ap.remarks", "a.applicant_fname", "a.applicant_lname", "ap.created_at")
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

    // DEBUG LOGS
    \Log::info("=== STATUS PAGE DEBUG ===");
    $allAppPersonnel = DB::table('tbl_application_personnel')->get();
    \Log::info("All application_personnel records:", ['count' => $allAppPersonnel->count(), 'data' => $allAppPersonnel->toArray()]);
    $allApplications = DB::table('tbl_application')->get();
    \Log::info("All application records:", ['count' => $allApplications->count(), 'data' => $allApplications->toArray()]);
    $allApplicants = DB::table('tbl_applicant')->get();
    \Log::info("All applicant records:", ['count' => $allApplicants->count(), 'data' => $allApplicants->toArray()]);

    // MAIN TABLE: Pending + Reviewed applications (Poor and Ultra Poor only)
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
     
       // match either remark value
       ->whereIn('ap.remarks', ['Poor', 'Ultra Poor']);

    // DEBUG main query
    $debugResults = $query->get();
    \Log::info("Main query results:", ['count' => $debugResults->count(), 'data' => $debugResults->toArray()]);

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

    $applications = $query->paginate();

    // DEBUG final apps
    \Log::info("Final applications for view:", ['count' => $applications->count(), 'data' => $applications->items()]);

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

    $debugListResults = $listQuery->get();
    \Log::info("List query results:", ['count' => $debugListResults->count(), 'data' => $debugListResults->toArray()]);

    $listApplications = $listQuery->paginate(100, ['*'], 'list');

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

    public function updateStatus(Request $request, $id)
{
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

    // If status is Approved, add to tbl_scholar if not already exists and send email
    if ($request->status === 'Approved') {
        $existingScholar = Scholar::where('application_id', $applicationPersonnel->application_id)->first();

        if (!$existingScholar) {
            Scholar::create([
                'application_id' => $applicationPersonnel->application_id,
                'scholar_username' => $request->username ?? 'default_username',
                'scholar_pass' => bcrypt($request->password ?? 'default123'),
                'date_activated' => now(),
                'scholar_status' => 'Active',
            ]);
        }

        // Send email with registration link
        try {
            $scholar = Scholar::where('application_id', $applicationPersonnel->application_id)->first();
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
        }
    }

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    return back()->with('success', 'Status updated successfully!');
}
    public function getIntakeSheet($applicationPersonnelId)
    {
        try {
            \Log::info("Fetching intake sheet for application_personnel_id: {$applicationPersonnelId}");

            // Debug: Check if record exists first
            $exists = FamilyIntakeSheet::where('application_personnel_id', $applicationPersonnelId)->exists();
            \Log::info("Debug: Intake sheet exists? " . ($exists ? 'Yes' : 'No'));

            // Get full record and log it
            $sheet = FamilyIntakeSheet::where('application_personnel_id', $applicationPersonnelId)->first();
            \Log::info("Debug: Full intake sheet record:", [
                'found' => (bool)$sheet,
                'data' => $sheet ? $sheet->toArray() : null
            ]);

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
                return response()->json(['success' => false, 'message' => 'Application or applicant not found.'], 404);
            }

            $fullName = trim(
                ($appRow->applicant_fname ?? '') .
                ' ' . ($appRow->applicant_mname ? $appRow->applicant_mname . ' ' : '') .
                ($appRow->applicant_lname ?? '') .
                ($appRow->applicant_suffix ? ', ' . $appRow->applicant_suffix : '')
            );

            $intakeSheet = FamilyIntakeSheet::where('application_personnel_id', $applicationPersonnelId)->first();

            if (!$intakeSheet) {
                \Log::info("No FamilyIntakeSheet record found, returning empty/default structure for application_personnel_id: {$applicationPersonnelId}");

                $empty = [
                    'applicant_name' => $fullName,
                    'applicant_fname' => $appRow->applicant_fname ?? null,
                    'applicant_mname' => $appRow->applicant_mname ?? null,
                    'applicant_lname' => $appRow->applicant_lname ?? null,
                    'applicant_suffix' => $appRow->applicant_suffix ?? null,
                    'applicant_gender' => $appRow->applicant_gender ?? null,
                    'remarks' => $appRow->remarks ?? null,
                    'application_letter' => $appRow->application_letter ? asset('storage/documents/' . $appRow->application_letter) : null,
                    'cert_of_reg' => $appRow->cert_of_reg ? asset('storage/documents/' . $appRow->cert_of_reg) : null,
                    'grade_slip' => $appRow->grade_slip ? asset('storage/documents/' . $appRow->grade_slip) : null,
                    'brgy_indigency' => $appRow->brgy_indigency ? asset('storage/documents/' . $appRow->brgy_indigency) : null,
                    'student_id' => $appRow->student_id ? asset('storage/documents/' . $appRow->student_id) : null,
                    'head_4ps' => null,
                    'head_ipno' => null,
                    'head_address' => null,
                    'head_zone' => null,
                    'head_barangay' => $appRow->applicant_brgy ?? null,
                    'head_pob' => null,
                    'head_dob' => null,
                    'head_educ' => null,
                    'head_occ' => null,
                    'head_religion' => null,
                    'serial_number' => null,
                    'location' => null,
                    'house_total_income' => null,
                    'house_net_income' => null,
                    'other_income' => null,
                    'house_house' => null,
                    'house_value' => null,
                    'house_lot' => null,
                    'lot_value' => null,
                    'house_rent' => null,
                    'lot_rent' => null,
                    'house_water' => null,
                    'house_electric' => null,
                    'house_remarks' => null,
                    'family_members' => [],
                    'social_service_records' => [],
                    'rv_service_records' => [],
                    'hc_estimated_cost' => null,
                    'worker_name' => null,
                    'officer_name' => null,
                    'date_entry' => null,
                    'signature_client' => null,
                    'signature_worker' => null,
                    'signature_officer' => null,
                ];

                return response()->json(['success' => true, 'intakeSheet' => $empty, 'intake_sheet' => $empty], 200, ['Content-Type' => 'application/json']);
            }

            $familyMembers = [];
            if (!empty($intakeSheet->family_members)) {
                $decoded = json_decode($intakeSheet->family_members, true);
                $familyMembers = is_array($decoded) ? $decoded : [];
            }

            $rvServiceRecords = [];
            if (!empty($intakeSheet->rv_service_records)) {
                $decoded = json_decode($intakeSheet->rv_service_records, true);
                $rvServiceRecords = is_array($decoded) ? $decoded : [];
            }

            $socialServiceRecords = [];
            if (!empty($intakeSheet->social_service_records)) {
                $decoded = json_decode($intakeSheet->social_service_records, true);
                $socialServiceRecords = is_array($decoded) ? $decoded : [];
            }

            $data = [
                'applicant_name' => $fullName,
                'applicant_fname' => $appRow->applicant_fname ?? null,
                'applicant_mname' => $appRow->applicant_mname ?? null,
                'applicant_lname' => $appRow->applicant_lname ?? null,
                'applicant_suffix' => $appRow->applicant_suffix ?? null,
                'applicant_gender' => $appRow->applicant_gender ?? null,
                'remarks' => $appRow->remarks ?? null,
                'application_letter' => $appRow->application_letter ? asset('storage/documents/' . $appRow->application_letter) : null,
                'cert_of_reg' => $appRow->cert_of_reg ? asset('storage/documents/' . $appRow->cert_of_reg) : null,
                'grade_slip' => $appRow->grade_slip ? asset('storage/documents/' . $appRow->grade_slip) : null,
                'brgy_indigency' => $appRow->brgy_indigency ? asset('storage/documents/' . $appRow->brgy_indigency) : null,
                'student_id' => $appRow->student_id ? asset('storage/documents/' . $appRow->student_id) : null,
                'head_4ps' => $intakeSheet->head_4ps,
                'head_ipno' => $intakeSheet->head_ipno,
                'head_address' => $intakeSheet->head_address,
                'head_zone' => $intakeSheet->head_zone,
                'head_barangay' => $intakeSheet->head_barangay ?? $appRow->applicant_brgy ?? null,
                'head_pob' => $intakeSheet->head_pob,
                'head_dob' => $intakeSheet->head_dob ? (string)$intakeSheet->head_dob : null,
                'head_educ' => $intakeSheet->head_educ,
                'head_occ' => $intakeSheet->head_occ,
                'head_religion' => $intakeSheet->head_religion,
                'serial_number' => $intakeSheet->serial_number,
                'location' => $intakeSheet->location,
                'house_total_income' => $intakeSheet->house_total_income,
                'house_net_income' => $intakeSheet->house_net_income,
                'other_income' => $intakeSheet->other_income,
                'house_house' => $intakeSheet->house_house,
                'house_value' => $intakeSheet->house_value,
                'house_lot' => $intakeSheet->house_lot,
                'lot_value' => $intakeSheet->lot_value,
                'house_rent' => $intakeSheet->house_rent,
                'lot_rent' => $intakeSheet->lot_rent,
                'house_water' => $intakeSheet->house_water,
                'house_electric' => $intakeSheet->house_electric,
                'house_remarks' => $intakeSheet->house_remarks,
                'family_members' => $familyMembers,
                'social_service_records' => $socialServiceRecords,
                'rv_service_records' => $rvServiceRecords,
                'hc_estimated_cost' => $intakeSheet->hc_estimated_cost,
                'worker_name' => $intakeSheet->worker_name,
                'officer_name' => $intakeSheet->officer_name,
                'date_entry' => $intakeSheet->date_entry ? (string)$intakeSheet->date_entry : null,
                'signature_client' => $intakeSheet->signature_client ? asset('storage/' . $intakeSheet->signature_client) : null,
                'signature_worker' => $intakeSheet->signature_worker ? asset('storage/' . $intakeSheet->signature_worker) : null,
                'signature_officer' => $intakeSheet->signature_officer ? asset('storage/' . $intakeSheet->signature_officer) : null,
            ];

            \Log::info("Intake sheet loaded for application_personnel_id: {$applicationPersonnelId}");

            return response()->json(['success' => true, 'intakeSheet' => $data, 'intake_sheet' => $data], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            \Log::error('Error fetching intake sheet: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading intake sheet data.'], 500);
        }
    }
}
