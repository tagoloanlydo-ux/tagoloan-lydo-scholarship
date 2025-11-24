<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailService;
use App\Events\ApplicantUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\RenewalUpdated;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;

class LydoStaffController extends Controller
{
public function index(Request $request)
{
    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $lastAcadYear = $currentAcadYear ? DB::table("tbl_applicant")
        ->where("applicant_acad_year", "<", $currentAcadYear)
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year") : null;

    $applicantsCurrentYear = $currentAcadYear ? DB::table("tbl_applicant")
        ->where("applicant_acad_year", $currentAcadYear)
        ->count() : 0;

    $applicantsLastYear = $lastAcadYear ? DB::table("tbl_applicant")
        ->where("applicant_acad_year", $lastAcadYear)
        ->count() : 0;

    $percentage =
        $applicantsLastYear > 0
            ? (($applicantsCurrentYear - $applicantsLastYear) /
                    $applicantsLastYear) *
                100
            : ($applicantsCurrentYear > 0 ? 100 : 0);

    $pendingInitial = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingInitialPercentage =
        $applicantsCurrentYear > 0
            ? ($pendingInitial / $applicantsCurrentYear) * 100
            : 0;

    $totalRenewals = DB::table("tbl_renewal")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();
    $approvedRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Approved")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();
    $rejectedRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Rejected")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    $completionRate =
        ($approvedRenewals + $rejectedRenewals) > 0
            ? ($approvedRenewals /
                    ($approvedRenewals + $rejectedRenewals)) *
                100
            : 0;

    $rejectedRate =
        ($approvedRenewals + $rejectedRenewals) > 0
            ? ($rejectedRenewals /
                    ($approvedRenewals + $rejectedRenewals)) *
                100
            : 0;

    $inactiveScholars = DB::table("tbl_scholar")
        ->where("scholar_status", "Inactive")
        ->count();

    $totalScholars = DB::table("tbl_scholar")->count();

    $inactivePercentage =
        $totalScholars > 0 ? ($inactiveScholars / $totalScholars) * 100 : 0;

    $filter = $request->get("filter", "all");

    $remarksMap = [
        "poor" => "Poor",
        "non_poor" => "Non Poor",
        "ultra_poor" => "Ultra Poor",
    ];

    $applications = DB::table("tbl_applicant")
        ->join("tbl_application", "tbl_applicant.applicant_id", "=", "tbl_application.applicant_id")
        ->join("tbl_application_personnel", "tbl_application.application_id", "=", "tbl_application_personnel.application_id")
        ->select(
            "tbl_applicant.applicant_id",
            "tbl_application_personnel.remarks",
            DB::raw("tbl_applicant.applicant_course as course"),
            DB::raw("tbl_applicant.applicant_school_name as school"),
            DB::raw("CONCAT(
                tbl_applicant.applicant_fname, ' ',
                COALESCE(tbl_applicant.applicant_mname, ''), ' ',
                tbl_applicant.applicant_lname,
                IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
            ) as name")
        )
        ->when($filter !== 'all', function ($query) use ($filter, $remarksMap) {
            if (isset($remarksMap[$filter])) {
                $query->where("tbl_application_personnel.remarks", $remarksMap[$filter]);
            }
        })
        ->where("tbl_application_personnel.initial_screening", "Approved")
        ->paginate();

    $notifications = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->select(
            "tbl_application_personnel.*",
            DB::raw("CONCAT(
        tbl_applicant.applicant_fname, ' ',
        COALESCE(tbl_applicant.applicant_mname, ''), ' ',
        tbl_applicant.applicant_lname,
        IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
    ) as name"),
        )
        ->where(function ($q) {
            $q->where(
                "tbl_application_personnel.initial_screening",
                "Approved",
            )->orWhere("tbl_application_personnel.status", "Renewed");
        })
        ->orderBy("tbl_application_personnel.created_at", "desc")
        ->limit(5)
        ->get();

    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    $pendingRenewalPercentage =
        $totalRenewals > 0
            ? ($pendingRenewals / $totalRenewals) * 100
            : 0;

    // Calculate notification badge count
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    // Store current counts in session for comparison
    session([
        'current_counts' => [
            'pending_screening' => $pendingScreening,
            'pending_renewals' => $pendingRenewals
        ]
    ]);

    return view(
        "lydo_staff.dashboard",
        compact(
            "currentAcadYear",
            "lastAcadYear",
            "applicantsCurrentYear",
            "percentage",
            "pendingInitial",
            "pendingInitialPercentage",
            "approvedRenewals",
            "completionRate",
            "filter",
            "applications",
            "notifications",
            "pendingScreening",
            "pendingRenewals",
            "rejectedRate",
            "pendingRenewalPercentage",
            "badgeCount"
        ),
    );
}

 public function screening(Request $request)
{
    $notifications = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->select(
            "tbl_application_personnel.*",
            DB::raw("CONCAT(
        tbl_applicant.applicant_fname, ' ',
        COALESCE(tbl_applicant.applicant_mname, ''), ' ',
        tbl_applicant.applicant_lname,
        IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
    ) as name"),
        )
        ->where(function ($q) {
            $q->where(
                "tbl_application_personnel.initial_screening",
                "Approved",
            )->orWhere("tbl_application_personnel.status", "Renewed");
        })
        ->orderBy("tbl_application_personnel.created_at", "desc")
        ->limit(5)
        ->get();

    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    // Calculate notification badge count
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    // Store current counts in session for comparison
    session([
        'current_counts' => [
            'pending_screening' => $pendingScreening,
            'pending_renewals' => $pendingRenewals
        ]
    ]);

    $query = DB::table("tbl_applicant")
        ->join(
            "tbl_application",
            "tbl_applicant.applicant_id",
            "=",
            "tbl_application.applicant_id",
        )
        ->join(
            "tbl_application_personnel",
            "tbl_application.application_id",
            "=",
            "tbl_application_personnel.application_id",
        )
        ->select("tbl_applicant.*", "tbl_application_personnel.remarks");

    // ✅ filters
    if ($request->filled("search")) {
        $query->where(function ($q) use ($request) {
            $q->where(
                "applicant_fname",
                "like",
                "%" . $request->search . "%",
            )->orWhere(
                "applicant_lname",
                "like",
                "%" . $request->search . "%",
            );
        });
    }

    if ($request->filled("barangay")) {
        $query->where(
            "applicant_brgy",
            "like",
            "%" . $request->barangay . "%",
        );
    }

    $query->where(
        "tbl_application_personnel.initial_screening",
        "Approved",
    );

    $tableApplicants = $query->get();

    $search = $request->input("search");
    $barangay = $request->input("barangay");

    $barangays = DB::table("tbl_applicant")
        ->select("applicant_brgy")
        ->distinct()
        ->orderBy("applicant_brgy", "asc")
        ->pluck("applicant_brgy");

    // Applicants with intake sheets but without remarks (pending screening)
    $tableApplicants = DB::table("tbl_applicant")
        ->join(
            "tbl_application",
            "tbl_applicant.applicant_id",
            "=",
            "tbl_application.applicant_id",
        )
        ->join(
            "tbl_application_personnel",
            "tbl_application.application_id",
            "=",
            "tbl_application_personnel.application_id",
        )
        ->leftJoin(
            "family_intake_sheets",
            "tbl_application_personnel.application_personnel_id",
            "=",
            "family_intake_sheets.application_personnel_id",
        )
        ->select("tbl_applicant.*", "tbl_application_personnel.remarks", "tbl_application_personnel.application_personnel_id")
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where(
                    "tbl_applicant.applicant_fname",
                    "LIKE",
                    "%{$search}%",
                )->orWhere(
                    "tbl_applicant.applicant_lname",
                    "LIKE",
                    "%{$search}%",
                );
            });
        })
        ->when($barangay, function ($query, $barangay) {
            $query->where("tbl_applicant.applicant_brgy", $barangay);
        })
        ->where("tbl_application_personnel.initial_screening", "Approved")
        ->where("tbl_application_personnel.remarks", "waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->get();

    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $listApplicants = DB::table("tbl_applicant as a")
        ->join("tbl_application as app", "a.applicant_id", "=", "app.applicant_id")
        ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
        ->select(
            "a.applicant_id",
            "a.applicant_fname",
            "a.applicant_mname",
            "a.applicant_lname",
            "a.applicant_suffix",
            "a.applicant_brgy",
            "a.applicant_course",
            "a.applicant_school_name",
            "a.applicant_bdate",
            "a.applicant_gender",
            "ap.application_personnel_id",
            "ap.initial_screening",
            "ap.remarks",
        )
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where("a.applicant_fname", "LIKE", "%{$search}%")
                  ->orWhere("a.applicant_lname", "LIKE", "%{$search}%");
            });
        })
        ->when($barangay, function ($query, $barangay) {
            $query->where("a.applicant_brgy", $barangay);
        })
        ->when($request->status, function ($query, $status) {
            $statuses = is_array($status) ? $status : [$status];
            $query->whereIn("ap.initial_screening", $statuses);
        })
        ->whereIn("ap.remarks", ["Poor", "Non Poor", "Ultra Poor"])
        ->where("a.applicant_acad_year", $currentAcadYear)
        ->get();

    return view(
        "lydo_staff.screening",
        compact(
            "notifications",
            "pendingScreening",
            "pendingRenewals",
            "tableApplicants",
            "listApplicants",
            "barangays",
            "currentAcadYear",
            "badgeCount"
        ),
    );
}

    public function updateApplicant(Request $request, $id)
    {
        $request->validate([
            "applicant_fname" => "required|string|max:50",
            "applicant_lname" => "required|string|max:50",
            "applicant_email" => "required|email",
            "applicant_contact_number" => "required|string|max:20",
            "applicant_gender" => "required|in:male,Male,female,Female",
            "applicant_civil_status" => "required|in:single,Single,married,Married,widowed,Widowed",
            "applicant_bdate" => "required|date",
            "applicant_brgy" => "required|string",
            "applicant_school_name" => "required|string",
            "applicant_year_level" => "required|string",
            "applicant_course" => "required|string",
            "applicant_acad_year" => "required|string",
        ]);

        DB::table("tbl_applicant")
            ->where("applicant_id", $id)
            ->update([
                "applicant_fname" => $request->applicant_fname,
                "applicant_mname" => $request->applicant_mname,
                "applicant_lname" => $request->applicant_lname,
                "applicant_suffix" => $request->applicant_suffix,
                "applicant_gender" => $request->applicant_gender,
                "applicant_bdate" => $request->applicant_bdate,
                "applicant_civil_status" => $request->applicant_civil_status,
                "applicant_brgy" => $request->applicant_brgy,
                "applicant_email" => $request->applicant_email,
                "applicant_contact_number" =>
                    $request->applicant_contact_number,
                "applicant_school_name" => $request->applicant_school_name,
                "applicant_year_level" => $request->applicant_year_level,
                "applicant_course" => $request->applicant_course,
                "applicant_acad_year" => $request->applicant_acad_year,
                "updated_at" => now(),
            ]);

        return back()->with(
            "success",
            "Applicant personal information updated!",
        );
    }

public function showIntakeSheet($application_personnel_id)
{
    $intakeSheet = \App\Models\FamilyIntakeSheet::where('application_personnel_id', $application_personnel_id)->first();

    $remarks = DB::table('tbl_application_personnel')->where('application_personnel_id', $application_personnel_id)->value('remarks');

    // Retrieve applicant data including gender from tbl_applicant
    $applicantData = DB::table('tbl_application_personnel')
        ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
        ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
        ->where('tbl_application_personnel.application_personnel_id', $application_personnel_id)
        ->select(
            'tbl_applicant.applicant_gender',
            'tbl_applicant.applicant_fname',
            'tbl_applicant.applicant_mname', 
            'tbl_applicant.applicant_lname',
            'tbl_applicant.applicant_suffix',
            'tbl_applicant.applicant_bdate',
            'tbl_applicant.applicant_brgy'
        )
        ->first();

    $applicantGender = $applicantData ? $applicantData->applicant_gender : null;
    
    // Get current logged-in staff name for worker_name field
    $currentStaff = session('lydopers');
    $currentStaffName = $currentStaff ? $currentStaff->lydopers_fname . ' ' . $currentStaff->lydopers_lname : '';

    // Set default date entry to today in YYYY-MM-DD format for HTML date input
    $defaultDateEntry = now()->format('Y-m-d');

    if ($intakeSheet) {
        $data = $intakeSheet->toArray();
        $data['remarks'] = $remarks;
        $data['applicant_gender'] = $applicantGender;
        
        // Populate applicant basic info from tbl_applicant
        if ($applicantData) {
            $data['applicant_fname'] = $applicantData->applicant_fname;
            $data['applicant_mname'] = $applicantData->applicant_mname;
            $data['applicant_lname'] = $applicantData->applicant_lname;
            $data['applicant_suffix'] = $applicantData->applicant_suffix;
            $data['head_barangay'] = $applicantData->applicant_brgy;
            $data['head_dob'] = $applicantData->applicant_bdate;
        }
        
        // CORRECTED: Ensure all fields are properly mapped
        $data['house_house'] = $intakeSheet->house_house;
        $data['house_lot'] = $intakeSheet->house_lot;
        $data['house_rent'] = $intakeSheet->house_rent;
        $data['lot_rent'] = $intakeSheet->lot_rent;
        $data['house_water'] = $intakeSheet->house_water;
        $data['house_electric'] = $intakeSheet->house_electric;
        $data['other_income'] = $intakeSheet->other_income;
        $data['house_total_income'] = $intakeSheet->house_total_income;
        $data['house_net_income'] = $intakeSheet->house_net_income;
        

        // Worker and officer names - prioritize intake sheet data, fallback to current session
        $data['worker_name'] = $intakeSheet->worker_name ?: $currentStaffName;
        $data['officer_name'] = $intakeSheet->officer_name ?: '';
        
        // Date entry - use saved date or default to today, formatted as "October 23, 2003"
        
        return response()->json($data);
    } else {
        // Return empty form with current staff name as default worker_name and today's date
        return response()->json([
            'remarks' => $remarks,
            'applicant_gender' => $applicantGender,
            // Populate applicant basic info from tbl_applicant if available
            'applicant_fname' => $applicantData ? $applicantData->applicant_fname : '',
            'applicant_mname' => $applicantData ? $applicantData->applicant_mname : '',
            'applicant_lname' => $applicantData ? $applicantData->applicant_lname : '',
            'applicant_suffix' => $applicantData ? $applicantData->applicant_suffix : '',
            'head_barangay' => $applicantData ? $applicantData->applicant_brgy : '',
            'head_dob' => $applicantData ? $applicantData->applicant_bdate : '',
            // Include empty values for house fields if no intake sheet exists
            'house_house' => null,
            'house_lot' => null,
            'house_rent' => null,
            'lot_rent' => null,
            'house_water' => null,
            'house_electric' => null,
            'other_income' => null,
            'house_total_income' => null,
            'house_net_income' => null,

            // Worker and officer names with current staff as default
            'worker_name' => $currentStaffName,
            'officer_name' => null,
            // Date entry defaults to today
            'date_entry' => $defaultDateEntry,
        ]);
    }
}

public function updateIntakeSheet(Request $request, $application_personnel_id)
{
    // Validation (kept simple; adjust as needed)
    $validated = $request->validate([
        'head_4ps' => 'nullable|string|max:255',
        'head_ipno' => 'nullable|string|max:255',
        'head_address' => 'nullable|string|max:255',
        'head_zone' => 'nullable|string|max:255',
        'head_pob' => 'nullable|string|max:255',
        'head_dob' => 'nullable|date',
        'head_educ' => 'nullable|string|max:255',
        'head_occ' => 'nullable|string|max:255',
        'head_religion' => 'nullable|string|max:255',
        'serial_number' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',

        // Household Info
        'house_total_income' => 'nullable|numeric',
        'house_net_income' => 'nullable|numeric',
        'other_income' => 'nullable|numeric',
        'house_house' => 'nullable|string|max:255',
        'house_lot' => 'nullable|string|max:255',
        'house_rent' => 'nullable|numeric',
        'lot_rent' => 'nullable|numeric',
        'house_water' => 'nullable|numeric',
        'house_electric' => 'nullable|numeric',
        'house_remarks' => 'nullable|string',

        // JSON fields
        'family_members' => 'nullable',
        'social_service_records' => 'nullable',
        'rv_service_records' => 'nullable',


        // Application personnel fields (may not exist on tbl_application_personnel)
        'worker_name' => 'nullable|string|max:255',
        'officer_name' => 'nullable|string|max:255',
        'date_entry' => 'nullable|date',

        // Remarks field (application_personnel)
        'remarks' => 'required|in:Poor,Non Poor,Ultra Poor',
    ]);

    try {
        // Load application_personnel (used for remarks/status and to find applicant)
        $ap = \App\Models\ApplicationPersonnel::findOrFail($application_personnel_id);

        // Update tbl_applicant if applicant fields were submitted
        $applicantId = DB::table('tbl_application')
            ->where('application_id', $ap->application_id)
            ->value('applicant_id');

        if ($applicantId) {
            $applicantUpdate = [];
            foreach (['applicant_fname','applicant_mname','applicant_lname','applicant_suffix','applicant_gender'] as $k) {
                if ($request->filled($k) || $request->has($k)) {
                    $applicantUpdate[$k] = $request->input($k);
                }
            }
            if (!empty($applicantUpdate)) {
                $applicantUpdate['updated_at'] = now();
                DB::table('tbl_applicant')->where('applicant_id', $applicantId)->update($applicantUpdate);
            }
        }

        // Update or create family_intake_sheets record (all intake/household fields + signatures)
        $intake = \App\Models\FamilyIntakeSheet::firstOrNew(['application_personnel_id' => $application_personnel_id]);

        $intakeFields = [
            'head_4ps','head_ipno','head_address','head_zone','head_pob','head_dob','head_educ','head_occ','head_religion',
            'serial_number','location',
            'other_income','house_total_income','house_net_income','house_house','house_value','house_lot','lot_value',
            'house_rent','lot_rent','house_water','house_electric','house_remarks', // Updated field names
            'family_members','social_service_records','rv_service_records'
        ];

        foreach ($intakeFields as $field) {
            if ($request->has($field)) {
                $intake->{$field} = $request->input($field);
            }
        }

        // Worker/officer/date: write to tbl_application_personnel only if column exists,
        // otherwise save into family_intake_sheets if that table has the columns.
        $schema = \Illuminate\Support\Facades\Schema::class;
        if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_application_personnel', 'worker_name')) {
            $ap->worker_name = $request->input('worker_name');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('family_intake_sheets', 'worker_name') && $request->has('worker_name')) {
            $intake->worker_name = $request->input('worker_name');
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_application_personnel', 'officer_name')) {
            $ap->officer_name = $request->input('officer_name');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('family_intake_sheets', 'officer_name') && $request->has('officer_name')) {
            $intake->officer_name = $request->input('officer_name');
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('tbl_application_personnel', 'date_entry')) {
            $ap->date_entry = $request->input('date_entry');
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('family_intake_sheets', 'date_entry') && $request->has('date_entry')) {
            $intake->date_entry = $request->input('date_entry');
        }

        // Save intake first (so any intake-side worker/officer/date are persisted)
        $intake->updated_at = now();
        $intake->save();

        // Update application_personnel only with columns that actually exist there
        $ap->remarks = $request->input('remarks');
        $ap->initial_screening = 'Reviewed';
        $ap->status = 'Pending';
        $ap->updated_at = now();
        $ap->save();

        return response('success', 200);
    } catch (\Exception $e) {
        \Log::error('Update Intake Sheet error: '.$e->getMessage());
        return response('error', 500);
    }
}
    public function submitIntakeSheet(Request $request, $application_personnel_id)
    {
        // Validate that the intake sheet exists
        $intakeSheet = \App\Models\FamilyIntakeSheet::where('application_personnel_id', $application_personnel_id)->first();

        if (!$intakeSheet) {
            return response()->json(['error' => 'Intake sheet not found'], 404);
        }

        // Update the application_personnel record to mark as submitted
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $application_personnel_id)
            ->update([
                'intake_sheet_submitted' => true,
                'status' => 'Submitted', // Optional: update status if needed
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Intake sheet submitted successfully']);
    }
public function updateRemarks(Request $request, $id)
{
    $request->validate([
        'remarks' => 'required|in:Poor,Non Poor,Ultra Poor',
    ]);

    // ✅ Step 1: Update existing record
    DB::table('tbl_application_personnel')
        ->where('application_personnel_id', $id)
        ->update([
            'remarks' => $request->remarks,
            'initial_screening' => 'Reviewed',
            'status' => 'Pending',
            'updated_at' => now(),
        ]);

    // ✅ Step 2: Update family intake sheet remarks
    DB::table('family_intake_sheets')
        ->where('application_personnel_id', $id)
        ->update([
            'house_remarks' => $request->remarks,
            'updated_at' => now(),
        ]);

    // ✅ Step 3: Get the existing record details to duplicate
    $existing = DB::table('tbl_application_personnel')->where('application_personnel_id', $id)->first();

    if ($existing) {
        // ✅ Step 4: Get the lydopers_id of Lydo Staff
        $lydoStaff = DB::table('tbl_lydopers')
            ->where('lydopers_role', 'lydo_staff')
            ->first();

        if ($lydoStaff) {
            // ✅ Step 5: Insert a new row based on existing data
            DB::table('tbl_application_personnel')->insert([
                'application_id'     => $existing->application_id,
                'lydopers_id'        => $lydoStaff->lydopers_id,
                'remarks'            => $request->remarks, // same remarks (Poor, Non Poor, Ultra Poor)
                'initial_screening'  => 'Reviewed',
                'status'             => 'Pending',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    return back()->with('success', 'Remarks updated and new record added successfully.');
}
public function disbursement(Request $request)
{
    $notifications = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->select(
            "tbl_application_personnel.*",
            DB::raw("CONCAT(
        tbl_applicant.applicant_fname, ' ',
        COALESCE(tbl_applicant.applicant_mname, ''), ' ',
        tbl_applicant.applicant_lname,
        IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
    ) as name"),
        )
        ->where(function ($q) {
            $q->where(
                "tbl_application_personnel.initial_screening",
                "Approved",
            )->orWhere("tbl_application_personnel.status", "Renewed");
        })
        ->orderBy("tbl_application_personnel.created_at", "desc")
        ->limit(5)
        ->get();

    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    // Calculate notification badge count
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    // Store current counts in session for comparison
    session([
        'current_counts' => [
            'pending_screening' => $pendingScreening,
            'pending_renewals' => $pendingRenewals
        ]
    ]);

    // Get filter parameters
    $search = $request->input('search');
    $barangay = $request->input('barangay');
    $academicYear = $request->input('academic_year');
    $semester = $request->input('semester');

    // Fetch unsigned disbursements with pagination and filtering
    $unsignedQuery = DB::table("tbl_disburse")
        ->join("tbl_scholar", "tbl_disburse.scholar_id", "=", "tbl_scholar.scholar_id")
        ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
        ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
        ->select(
            "tbl_disburse.*",
            DB::raw("
                CONCAT(
                    UPPER(LEFT(tbl_applicant.applicant_lname, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_lname, 2)), ', ',
                    UPPER(LEFT(tbl_applicant.applicant_fname, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_fname, 2)), ' ',
                    IFNULL(CONCAT( UPPER(LEFT(tbl_applicant.applicant_mname, 1)), '.' ), ''),
                    IFNULL(CONCAT(' ', UPPER(LEFT(tbl_applicant.applicant_suffix, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_suffix, 2)) ), '')
                ) as full_name
            "),
            "tbl_applicant.applicant_brgy"
        )
        ->whereNull("tbl_disburse.disburse_signature")
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where("tbl_applicant.applicant_fname", "like", "%$search%")
                  ->orWhere("tbl_applicant.applicant_lname", "like", "%$search%");
            });
        })
        ->when($barangay, function ($query, $barangay) {
            $query->where("tbl_applicant.applicant_brgy", $barangay);
        })
        ->when($academicYear, function ($query, $academicYear) {
            $query->where("tbl_disburse.disburse_acad_year", $academicYear);
        })
        ->when($semester, function ($query, $semester) {
            $query->where("tbl_disburse.disburse_semester", $semester);
        });

    // Sort alphabetically by Lastname, Firstname M. Suffix
    $unsignedQuery->orderBy(DB::raw("
        CONCAT(
            tbl_applicant.applicant_lname, ', ',
            tbl_applicant.applicant_fname, ' ',
            IFNULL(CONCAT(LEFT(tbl_applicant.applicant_mname, 1), '.'), ''),
            IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
        )
    "));

    $unsignedDisbursements = $unsignedQuery->get();

    $signedQuery = DB::table("tbl_disburse")
        ->join("tbl_scholar", "tbl_disburse.scholar_id", "=", "tbl_scholar.scholar_id")
        ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
        ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
        ->select(
            "tbl_disburse.*",
            DB::raw("
                CONCAT(
                    UPPER(LEFT(tbl_applicant.applicant_lname, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_lname, 2)), ', ',
                    UPPER(LEFT(tbl_applicant.applicant_fname, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_fname, 2)), ' ',
                    IFNULL(CONCAT( UPPER(LEFT(tbl_applicant.applicant_mname, 1)), '.' ), ''),
                    IFNULL(CONCAT(' ', UPPER(LEFT(tbl_applicant.applicant_suffix, 1)), LOWER(SUBSTRING(tbl_applicant.applicant_suffix, 2)) ), '')
                ) as full_name
            "),
            "tbl_applicant.applicant_brgy"
        )
        ->whereNotNull("tbl_disburse.disburse_signature")
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where("tbl_applicant.applicant_fname", "like", "%$search%")
                  ->orWhere("tbl_applicant.applicant_lname", "like", "%$search%");
            });
        })
        ->when($barangay, function ($query, $barangay) {
            $query->where("tbl_applicant.applicant_brgy", $barangay);
        })
        ->when($academicYear, function ($query, $academicYear) {
            $query->where("tbl_disburse.disburse_acad_year", $academicYear);
        })
        ->when($semester, function ($query, $semester) {
            $query->where("tbl_disburse.disburse_semester", $semester);
        });

    // Sort alphabetically by Lastname, Firstname M. Suffix
    $signedQuery->orderBy(DB::raw("
        CONCAT(
            tbl_applicant.applicant_lname, ', ',
            tbl_applicant.applicant_fname, ' ',
            IFNULL(CONCAT(LEFT(tbl_applicant.applicant_mname, 1), '.'), ''),
            IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
        )
    "));

    $signedDisbursements = $signedQuery->get();

    $barangays = DB::table("tbl_applicant")
        ->select("applicant_brgy")
        ->distinct()
        ->pluck("applicant_brgy");

    $academicYears = DB::table("tbl_disburse")
        ->select("disburse_acad_year")
        ->distinct()
        ->orderBy("disburse_acad_year", "desc")
        ->pluck("disburse_acad_year");

    $semesters = DB::table("tbl_disburse")
        ->select("disburse_semester")
        ->distinct()
        ->orderBy("disburse_semester")
        ->pluck("disburse_semester");

    return view("lydo_staff.disbursement", compact(
        "notifications",
        "pendingScreening",
        "pendingRenewals",
        "unsignedDisbursements",
        "signedDisbursements",
        "barangays",
        "academicYears",
        "semesters",
        "currentAcadYear",
        "badgeCount"
    ));
}
  public function settings(Request $request)
{
    $notifications = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->select(
            "tbl_application_personnel.*",
            DB::raw("CONCAT(
        tbl_applicant.applicant_fname, ' ',
        COALESCE(tbl_applicant.applicant_mname, ''), ' ',
        tbl_applicant.applicant_lname,
        IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
    ) as name"),
        )
        ->where(function ($q) {
            $q->where(
                "tbl_application_personnel.initial_screening",
                "Approved",
            )->orWhere("tbl_application_personnel.status", "Renewed");
        })
        ->orderBy("tbl_application_personnel.created_at", "desc")
        ->limit(5)
        ->get();

    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets",
            "tbl_application_personnel.application_personnel_id",
            "=",
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    // Calculate notification badge count
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    // Store current counts in session for comparison
    session([
        'current_counts' => [
            'pending_screening' => $pendingScreening,
            'pending_renewals' => $pendingRenewals
        ]
    ]);

    return view(
        "lydo_staff.settings",
        compact(
            "notifications",
            "pendingScreening",
            "pendingRenewals",
            "badgeCount"
        ),
    );
}


public function getNotificationCounts()
{
    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    // Calculate new notifications
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    return response()->json([
        'badge_count' => $badgeCount,
        'pending_screening' => $pendingScreening,
        'pending_renewals' => $pendingRenewals,
        'has_new_notifications' => $badgeCount > 0
    ]);
}

public function markNotificationsAsRead()
{
    $currentCounts = session('current_counts', []);
    session(['unread_notifications' => $currentCounts]);
    
    return response()->json(['success' => true]);
}


    public function signDisbursement(Request $request, $disburseId)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        DB::table('tbl_disburse')
            ->where('disburse_id', $disburseId)
            ->update([
                'disburse_signature' => $request->signature,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Disbursement signed successfully.');
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
    ]);

    $user = DB::table('tbl_lydopers')
        ->where('lydopers_id', session('lydopers')->lydopers_id)
        ->first();

    // Verify current password
    if (!password_verify($request->current_password, $user->lydopers_pass)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    // Update password
    DB::table('tbl_lydopers')
        ->where('lydopers_id', session('lydopers')->lydopers_id)
        ->update([
            'lydopers_pass' => password_hash($request->new_password, PASSWORD_DEFAULT),
            'updated_at' => now(),
        ]);

    return back()->with('success', 'Password updated successfully.');
}

public function generateIntakeSheetPdf($application_personnel_id)
{
    try {
        \Log::info("Generating PDF for application_personnel_id: {$application_personnel_id}");
        
        // Get intake sheet data with ALL fields
        $intakeSheet = \App\Models\FamilyIntakeSheet::where('application_personnel_id', $application_personnel_id)->first();

        if (!$intakeSheet) {
            \Log::error("Intake sheet not found for ID: {$application_personnel_id}");
            return response()->json(['error' => 'Intake sheet not found'], 404);
        }

        // Get applicant data with ALL fields INCLUDING REMARKS
        $applicantData = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->where('tbl_application_personnel.application_personnel_id', $application_personnel_id)
            ->select(
                'tbl_applicant.applicant_fname',
                'tbl_applicant.applicant_mname',
                'tbl_applicant.applicant_lname',
                'tbl_applicant.applicant_suffix',
                'tbl_applicant.applicant_contact_number',
                'tbl_applicant.applicant_gender',
                'tbl_applicant.applicant_bdate',
                'tbl_applicant.applicant_brgy',
                'tbl_applicant.applicant_civil_status',
                'tbl_applicant.applicant_email',
                'tbl_applicant.applicant_contact_number',
                'tbl_application_personnel.remarks' // THIS IS THE REMARKS FIELD
            )
            ->first();

        if (!$applicantData) {
            \Log::error("Applicant data not found for application_personnel_id: {$application_personnel_id}");
            return response()->json(['error' => 'Applicant data not found'], 404);
        }

        // Parse family members
        $familyMembers = [];
        if ($intakeSheet->family_members) {
            try {
                $familyMembers = json_decode($intakeSheet->family_members, true) ?: [];
            } catch (\Exception $e) {
                \Log::error("Error parsing family members: " . $e->getMessage());
                $familyMembers = [];
            }
        }

        // Parse social service records
        $socialServiceRecords = [];
        if ($intakeSheet->social_service_records) {
            try {
                $socialServiceRecords = json_decode($intakeSheet->social_service_records, true) ?: [];
            } catch (\Exception $e) {
                \Log::error("Error parsing social service records: " . $e->getMessage());
                $socialServiceRecords = [];
            }
        }

        // Parse RV service records
        $rvServiceRecords = [];
        if ($intakeSheet->rv_service_records) {
            try {
                $rvServiceRecords = json_decode($intakeSheet->rv_service_records, true) ?: [];
            } catch (\Exception $e) {
                \Log::error("Error parsing RV service records: " . $e->getMessage());
                $rvServiceRecords = [];
            }
        }

        // Calculate age
        $age = '';
        if ($applicantData->applicant_bdate) {
            try {
                $birthdate = \Carbon\Carbon::parse($applicantData->applicant_bdate);
                $age = $birthdate->age;
            } catch (\Exception $e) {
                $age = '';
            }
        }

        // Prepare COMPLETE data for PDF - matching showIntakeSheet structure
        $data = [
            'serialNumber' => $intakeSheet->serial_number ?? 'N/A',
            'head' => [
                'within_tagoloan' => true, // Default values
                'outside_tagoloan' => false,
                '_4ps' => $intakeSheet->head_4ps ?? 'No',
                'ipno' => $intakeSheet->head_ipno ?? '',
                'lname' => $applicantData->applicant_lname ?? '',
                'fname' => $applicantData->applicant_fname ?? '',
                'mname' => $applicantData->applicant_mname ?? '',
                'suffix' => $applicantData->applicant_suffix ?? '',
                'contact' => $applicantData->applicant_contact_number ?? '',
                'sex' => $applicantData->applicant_gender ?? '',
                'age' => $age,
                'address' => $intakeSheet->head_address ?? '',
                'zone' => $intakeSheet->head_zone ?? '',
                'barangay' => $intakeSheet->head_barangay ?? $applicantData->applicant_brgy ?? '',
                'dob' => $applicantData->applicant_bdate ?? '',
                'pob' => $intakeSheet->head_pob ?? '',
                'civil' => $applicantData->applicant_civil_status ?? '',
                'educ' => $intakeSheet->head_educ ?? '',
                'occ' => $intakeSheet->head_occ ?? '',
                'religion' => $intakeSheet->head_religion ?? '',
                'remarks' => $applicantData->remarks ?? '', // REMARKS FROM APPLICATION_PERSONNEL
            ],
            'family' => $familyMembers,
            'house' => [
                'total_income' => $intakeSheet->house_total_income ?? 0,
                'net_income' => $intakeSheet->house_net_income ?? 0,
                'other_income' => $intakeSheet->other_income ?? 0,
                'house' => $intakeSheet->house_house ?? '',
                'lot' => $intakeSheet->house_lot ?? '',
                'house_rent' => $intakeSheet->house_rent ?? 0,
                'lot_rent' => $intakeSheet->lot_rent ?? 0,
                'water' => $intakeSheet->house_water ?? 0,
                'electric' => $intakeSheet->house_electric ?? 0,
                'remarks' => $applicantData->remarks ?? '', // ALSO ADD TO HOUSE SECTION IF NEEDED
            ],
            'social_service_records' => $socialServiceRecords,
            'rv_service_records' => $rvServiceRecords,
            'worker_info' => [
                'worker_name' => $intakeSheet->worker_name ?? '',
                'officer_name' => $intakeSheet->officer_name ?? '',
               'date_entry' => $intakeSheet->date_entry ? \Carbon\Carbon::parse($intakeSheet->date_entry)->format('F d Y') : now()->format('F d Y'),            ],
                 'application_remarks' => $applicantData->remarks ?? '', // SEPARATE FIELD FOR REMARKS
        ];

        // Debug log to check data including remarks
        \Log::info('PDF Data prepared:', [
            'family_members_count' => count($familyMembers),
            'social_records_count' => count($socialServiceRecords),
            'rv_records_count' => count($rvServiceRecords),
            'remarks' => $applicantData->remarks ?? 'No remarks'
        ]);

        // Generate PDF
        $pdf = PDF::loadView('pdf.intake-sheet-print', $data)
                  ->setPaper('legal', 'landscape')
                  ->setOptions([
                      'dpi' => 150,
                      'defaultFont' => 'Arial',
                      'isHtml5ParserEnabled' => true
                  ]);

        return $pdf->stream('family-intake-sheet-' . $application_personnel_id . '.pdf');

    } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json(['error' => 'PDF generation failed: ' . $e->getMessage()], 500);
    }
}
public function update(Request $request, $id)
{
    $request->validate([
        "lydopers_fname" => "required|string|max:50",
        "lydopers_lname" => "required|string|max:50",
        "lydopers_mname" => "nullable|string|max:50",
        "lydopers_suffix" => "nullable|string|max:10",
        "lydopers_email" => "required|email|unique:tbl_lydopers,lydopers_email," . $id . ",lydopers_id",
        "lydopers_contact_number" => "required|numeric",
        "lydopers_address" => "nullable|string",
        "lydopers_bdate" => "nullable|date",
    ]);

    try {
        DB::table("tbl_lydopers")
            ->where("lydopers_id", $id)
            ->update([
                "lydopers_fname" => $request->lydopers_fname,
                "lydopers_mname" => $request->lydopers_mname,
                "lydopers_lname" => $request->lydopers_lname,
                "lydopers_suffix" => $request->lydopers_suffix,
                "lydopers_email" => $request->lydopers_email,
                "lydopers_contact_number" => $request->lydopers_contact_number,
                "lydopers_address" => $request->lydopers_address,
                "lydopers_bdate" => $request->lydopers_bdate,
                "updated_at" => now(),
            ]);

        // Update session data
        $updatedUser = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();
        session(['lydopers' => $updatedUser]);

        return back()->with(
            "success",
            "Personal information updated successfully!"
        );
    } catch (\Exception $e) {
        return back()->with(
            "error",
            "Failed to update personal information. Please try again."
        );
    }
}
// App\Http\Controllers\LydoStaffController.php
public function getCardData()
{
    try {
        $currentAcadYear = DB::table("tbl_applicant")
            ->select("applicant_acad_year")
            ->orderBy("applicant_acad_year", "desc")
            ->value("applicant_acad_year");

        // Get all counts in single query for better performance
        $counts = DB::table("tbl_applicant")
            ->selectRaw("COUNT(*) as applicantsCurrentYear")
            ->selectRaw("SUM(CASE WHEN applicant_acad_year = ? THEN 1 ELSE 0 END) as applicantsCurrentYear", [$currentAcadYear])
            ->first();

        $applicantsCurrentYear = $counts->applicantsCurrentYear ?? 0;

        // Other counts...
        $pendingInitial = DB::table("tbl_application_personnel")
            ->join("tbl_application", "tbl_application_personnel.application_id", "=", "tbl_application.application_id")
            ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
            ->leftJoin("family_intake_sheets", "tbl_application_personnel.application_personnel_id", "=", "family_intake_sheets.application_personnel_id")
            ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
            ->where("tbl_application_personnel.remarks", "Waiting")
            ->whereNotNull("family_intake_sheets.application_personnel_id")
            ->count();

        $approvedRenewals = DB::table("tbl_renewal")
            ->where("renewal_status", "Approved")
            ->where("renewal_acad_year", $currentAcadYear)
            ->count();

        $pendingRenewals = DB::table("tbl_renewal")
            ->where("renewal_status", "Pending")
            ->where("renewal_acad_year", $currentAcadYear)
            ->count();

        // Calculate percentages
        $pendingInitialPercentage = $applicantsCurrentYear > 0 
            ? ($pendingInitial / $applicantsCurrentYear) * 100 
            : 0;

        $totalRenewals = $approvedRenewals + $pendingRenewals;
        $completionRate = $totalRenewals > 0 
            ? ($approvedRenewals / $totalRenewals) * 100 
            : 0;

        $pendingRenewalPercentage = $totalRenewals > 0 
            ? ($pendingRenewals / $totalRenewals) * 100 
            : 0;

        return response()->json([
            'applicantsCurrentYear' => $applicantsCurrentYear,
            'pendingInitial' => $pendingInitial,
            'approvedRenewals' => $approvedRenewals,
            'pendingRenewals' => $pendingRenewals,
            'pendingInitialPercentage' => $pendingInitialPercentage,
            'completionRate' => $completionRate,
            'pendingRenewalPercentage' => $pendingRenewalPercentage
        ]);

    } catch (\Exception $e) {
        \Log::error('Card data fetch error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to fetch card data'
        ], 500);
    }
}

public function getApplicantsData(Request $request)
{
    try {
        $filter = $request->get('filter', 'all');
        
        $remarksMap = [
            "poor" => "Poor",
            "non_poor" => "Non Poor",
            "ultra_poor" => "Ultra Poor",
        ];

        $applications = DB::table("tbl_applicant")
            ->join("tbl_application", "tbl_applicant.applicant_id", "=", "tbl_application.applicant_id")
            ->join("tbl_application_personnel", "tbl_application.application_id", "=", "tbl_application_personnel.application_id")
            ->select(
                "tbl_applicant.applicant_id",
                "tbl_application_personnel.remarks",
                DB::raw("tbl_applicant.applicant_course as course"),
                DB::raw("tbl_applicant.applicant_school_name as school"),
                DB::raw("CONCAT(
                    tbl_applicant.applicant_fname, ' ',
                    COALESCE(tbl_applicant.applicant_mname, ''), ' ',
                    tbl_applicant.applicant_lname,
                    IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
                ) as name")
            )
            ->when($filter !== 'all', function ($query) use ($filter, $remarksMap) {
                if (isset($remarksMap[$filter])) {
                    $query->where("tbl_application_personnel.remarks", $remarksMap[$filter]);
                }
            })
            ->where("tbl_application_personnel.initial_screening", "Approved")
            ->get();

        return response()->json($applications);

    } catch (\Exception $e) {
        \Log::error('Applicants data fetch error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to fetch applicants data'
        ], 500);
    }
}

}
