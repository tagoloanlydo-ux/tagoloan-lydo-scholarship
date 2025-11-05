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
use App\Events\RenewalUpdated;
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
            ->count();

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
            ->paginate()
    ->appends($request->all());

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
        "a.applicant_pob",
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
        // If status is a single string, wrap it into an array
        $statuses = is_array($status) ? $status : [$status];
        $query->whereIn("ap.initial_screening", $statuses);
    })
    ->whereIn("ap.remarks", ["Poor", "Non Poor", "Ultra Poor"])
    ->where("a.applicant_acad_year", $currentAcadYear)
    ->paginate()
    ->appends($request->all());

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
                "applicant_pob" => $request->applicant_pob,
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

        // Retrieve applicant gender and place of birth
        $applicantData = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->where('tbl_application_personnel.application_personnel_id', $application_personnel_id)
            ->select('tbl_applicant.applicant_gender', 'tbl_applicant.applicant_pob')
            ->first();

        $applicantGender = $applicantData ? $applicantData->applicant_gender : null;
        $applicantPob = $applicantData ? $applicantData->applicant_pob : null;

        if ($intakeSheet) {
            $data = $intakeSheet->toArray();
            $data['remarks'] = $remarks;
            $data['applicant_gender'] = $applicantGender;
            $data['applicant_pob'] = $applicantPob;
            return response()->json($data);
        } else {
            return response()->json([
                'remarks' => $remarks,
                'applicant_gender' => $applicantGender,
                'applicant_pob' => $applicantPob
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
        'house_value' => 'nullable|numeric',
        'house_lot' => 'nullable|string|max:255',
        'lot_value' => 'nullable|numeric',
        'house_rent' => 'nullable|numeric',
        'lot_rent' => 'nullable|numeric',
        'house_water' => 'nullable|string|max:255',
        'house_electric' => 'nullable|string|max:255',
        'house_remarks' => 'nullable|string',

        // JSON fields
        'family_members' => 'nullable',
        'social_service_records' => 'nullable',
        'rv_service_records' => 'nullable',

        // Signatures (store in intake sheet)
        'signature_client' => 'nullable|string',
        'signature_worker' => 'nullable|string',
        'signature_officer' => 'nullable|string',

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
            foreach (['applicant_fname','applicant_mname','applicant_lname','applicant_suffix','applicant_gender','applicant_pob'] as $k) {
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
            'house_rent','lot_rent','house_water','house_electric','house_remarks',
            'family_members','social_service_records','rv_service_records',
            'signature_client','signature_worker','signature_officer'
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
        ->count();

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
            DB::raw("CONCAT(tbl_applicant.applicant_fname, ' ', COALESCE(tbl_applicant.applicant_mname, ''), ' ', tbl_applicant.applicant_lname, IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')) as full_name"),
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

    $unsignedDisbursements = $unsignedQuery->get();

    // Fetch signed disbursements with pagination and filtering
    $signedQuery = DB::table("tbl_disburse")
        ->join("tbl_scholar", "tbl_disburse.scholar_id", "=", "tbl_scholar.scholar_id")
        ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
        ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
        ->select(
            "tbl_disburse.*",
            DB::raw("CONCAT(tbl_applicant.applicant_fname, ' ', COALESCE(tbl_applicant.applicant_mname, ''), ' ', tbl_applicant.applicant_lname, IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')) as full_name"),
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
        "currentAcadYear"
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
            ->count();

        return view(
            "lydo_staff.settings",
            compact(
                "notifications",
                "pendingScreening",
                "pendingRenewals",
            ),
        );
    }

public function updateStaff(Request $request, $id)
{
    $request->validate([
        'lydopers_fname' => [
            'required', 
            'string', 
            'max:50',
            'regex:/^[a-zA-Z\s]+$/' // No numbers or symbols
        ],
        'lydopers_mname' => [
            'nullable', 
            'string', 
            'max:50',
            'regex:/^[a-zA-Z\s]*$/' // No numbers or symbols, can be empty
        ],
        'lydopers_lname' => [
            'required', 
            'string', 
            'max:50',
            'regex:/^[a-zA-Z\s]+$/' // No numbers or symbols
        ],
        'lydopers_suffix' => 'nullable|string|max:10',
        'lydopers_email' => [
            'required',
            'email',
            Rule::unique('tbl_lydopers')->ignore($id, 'lydopers_id')
        ],
        'lydopers_address' => 'required|string',
        'lydopers_contact_number' => [
            'required',
            'string',
            'max:20',
            'regex:/^(09|\+?639)\d{9}$/' // More flexible Philippine mobile number format
        ],
        'lydopers_bdate' => 'required|date',
    ], [
        'lydopers_fname.regex' => 'First name should only contain letters and spaces.',
        'lydopers_mname.regex' => 'Middle name should only contain letters and spaces.',
        'lydopers_lname.regex' => 'Last name should only contain letters and spaces.',
        'lydopers_email.unique' => 'This email is already taken.',
        'lydopers_contact_number.regex' => 'Please enter a valid Philippine mobile number (09XXXXXXXXX or +639XXXXXXXXX).',
    ]);

    // Clean the phone number before saving (remove spaces, dashes, etc.)
    $cleanedPhone = preg_replace('/[^0-9+]/', '', $request->lydopers_contact_number);

    $updateData = [
        'lydopers_fname' => $request->lydopers_fname,
        'lydopers_mname' => $request->lydopers_mname,
        'lydopers_lname' => $request->lydopers_lname,
        'lydopers_suffix' => $request->lydopers_suffix,
        'lydopers_email' => $request->lydopers_email,
        'lydopers_address' => $request->lydopers_address,
        'lydopers_contact_number' => $cleanedPhone, // Use cleaned version
        'lydopers_bdate' => $request->lydopers_bdate,
        'updated_at' => now(),
    ];

    DB::table('tbl_lydopers')
        ->where('lydopers_id', $id)
        ->update($updateData);

    // Update session data
    $updatedUser = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();
    session(['lydopers' => $updatedUser]);

    return back()->with('success', 'Personal information updated successfully.');
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

    // Verify current password (you'll need to implement this based on your auth system)
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    // Update password
    DB::table('tbl_lydopers')
        ->where('lydopers_id', session('lydopers')->lydopers_id)
        ->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now(),
        ]);

    return back()->with('success', 'Password updated successfully.');
}

}
