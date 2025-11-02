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
            ->paginate(15);

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
            ->paginate(15)
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
    ->paginate(15)
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

    public function renewal(Request $request)
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
            ->count();

        $currentYear = date("Y");

        // ✅ Pending renewals (current year only)
        $tableApplicants = DB::table("tbl_renewal as r")
            ->join("tbl_scholar as s", "r.scholar_id", "=", "s.scholar_id")
            ->join(
                "tbl_application as ap",
                "s.application_id",
                "=",
                "ap.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "ap.applicant_id",
                "=",
                "a.applicant_id",
            )
            ->select(
                "r.renewal_id",
                "a.applicant_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "a.applicant_brgy",
                "a.applicant_email",
                "a.applicant_school_name",
                "r.renewal_status",
                "r.renewal_cert_of_reg",
                "r.renewal_grade_slip",
                "r.renewal_brgy_indigency",
                "r.renewal_acad_year",
                "ap.application_id",
                "s.scholar_id",
            )
            ->where("r.renewal_status", "Pending")
            ->where("r.renewal_acad_year", $currentAcadYear)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        "a.applicant_fname",
                        "like",
                        "%$search%",
                    )->orWhere("a.applicant_lname", "like", "%$search%");
                });
            })
            ->when($request->barangay, function ($query, $barangay) {
                $query->where("a.applicant_brgy", $barangay);
            })
            ->paginate(15);

        $renewals = DB::table("tbl_renewal")
            ->select(
                "renewal_id",
                "scholar_id",
                "renewal_cert_of_reg",
                "renewal_grade_slip",
                "renewal_brgy_indigency",
                "renewal_semester",
                "renewal_acad_year",
                "date_submitted",
                "renewal_status",
            )
            ->get()
            ->groupBy("scholar_id");

        // para dropdown filter ng barangay
        $barangays = DB::table("tbl_applicant")
            ->select("applicant_brgy")
            ->distinct()
            ->pluck("applicant_brgy");

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

$listView = DB::table("tbl_renewal as r")
    ->join("tbl_scholar as s", "r.scholar_id", "=", "s.scholar_id")
    ->join("tbl_application as ap", "s.application_id", "=", "ap.application_id")
    ->join("tbl_applicant as a", "ap.applicant_id", "=", "a.applicant_id")
    ->select(
        "s.scholar_id",
        "r.renewal_id",
        "a.applicant_id",
        "a.applicant_fname",
        "a.applicant_mname",
        "a.applicant_lname",
        "a.applicant_suffix",
        "a.applicant_brgy",
        "a.applicant_school_name",
        "a.applicant_course",
        "r.renewal_status",
        "r.renewal_acad_year",
        "ap.application_id"
    )
    ->whereIn("r.renewal_status", ["Approved", "Rejected"])
    ->where("r.renewal_acad_year", $currentAcadYear)
    ->when($request->search, function ($query, $search) {
        $query->where(function ($q) use ($search) {
            $q->where("a.applicant_fname", "like", "%$search%")
              ->orWhere("a.applicant_lname", "like", "%$search%");
        });
    })
    ->when($request->barangay, function ($query, $barangay) {
        $query->where("a.applicant_brgy", $barangay);
    })
    ->paginate(15) // ✅ use paginate instead of get
    ->appends($request->all());

        return view(
            "lydo_staff.renewal",
            compact(
                "pendingScreening",
                "pendingRenewals",
                "currentAcadYear",
                "tableApplicants",
                "barangays",
                "renewals",
                "notifications",
                "listView",
            ),
        );
    }
    public function updateRenewalStatus(Request $request, $id)
    {
        $request->validate([
            "renewal_status" => "required|in:Approved,Rejected",
            "reason" => "nullable|string|max:500",
        ]);


        $updateData = [
            "renewal_status" => $request->renewal_status,
            "updated_at" => now(),
        ];

        if ($request->renewal_status === "Rejected" && $request->filled('reason')) {
            $updateData["rejection_reason"] = $request->reason;
        }

        DB::table("tbl_renewal")
            ->where("renewal_id", $id)
            ->update($updateData);

        // Broadcast update
        $currentAcadYear = DB::table("tbl_applicant")
            ->select("applicant_acad_year")
            ->orderBy("applicant_acad_year", "desc")
            ->value("applicant_acad_year");

        $approvedRenewals = DB::table("tbl_renewal")
            ->where("renewal_status", "Approved")
            ->where("renewal_acad_year", $currentAcadYear)
            ->count();

        $pendingRenewals = DB::table("tbl_renewal")
            ->where("renewal_status", "Pending")
            ->where("renewal_acad_year", $currentAcadYear)
            ->count();

        broadcast(new RenewalUpdated('approved_renewals', $approvedRenewals))->toOthers();
        broadcast(new RenewalUpdated('pending_renewals', $pendingRenewals))->toOthers();

        $scholarId = DB::table("tbl_renewal")
            ->where("renewal_id", $id)
            ->value("scholar_id"); // kuha scholar id

        if ($scholarId) {
            $applicant = DB::table("tbl_scholar")
                ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
                ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
                ->where("tbl_scholar.scholar_id", $scholarId)
                ->select("tbl_applicant.applicant_email", "tbl_applicant.applicant_fname", "tbl_applicant.applicant_lname")
                ->first();

            if ($applicant) {
                $emailService = new EmailService();
                if ($request->renewal_status === "Rejected") {
                    DB::table("tbl_scholar")
                        ->where("scholar_id", $scholarId)
                        ->update([
                            "scholar_status" => "Inactive",
                            "updated_at" => now(),
                        ]);

                    $emailService->sendRejectionEmail($applicant->applicant_email, [
                        'applicant_fname' => $applicant->applicant_fname,
                        'applicant_lname' => $applicant->applicant_lname,
                        'reason' => $request->reason,
                    ]);
                } elseif ($request->renewal_status === "Approved") {
                    $emailService->sendApprovalEmail($applicant->applicant_email, [
                        'applicant_fname' => $applicant->applicant_fname,
                        'applicant_lname' => $applicant->applicant_lname,
                    ]);
                }
            }
        }

        return response()->json(["success" => true]);
    }


    public function getRequirements($id)
    {
        $renewal = \DB::table("tbl_renewal")
            ->join(
                "tbl_scholar",
                "tbl_renewal.scholar_id",
                "=",
                "tbl_scholar.scholar_id",
            )
            ->join(
                "tbl_application",
                "tbl_scholar.application_id",
                "=",
                "tbl_application.application_id",
            )
            ->join(
                "tbl_applicant",
                "tbl_application.applicant_id",
                "=",
                "tbl_applicant.applicant_id",
            )
            ->where("tbl_renewal.renewal_id", $id)
            ->select(
                "tbl_applicant.applicant_fname",
                "tbl_applicant.applicant_lname",
                "tbl_applicant.applicant_brgy",
                "tbl_applicant.applicant_school_name",
                "tbl_renewal.renewal_cert_of_reg",
                "tbl_renewal.renewal_grade_slip",
                "tbl_renewal.renewal_brgy_indigency",
                "tbl_renewal.renewal_acad_year",
                "tbl_renewal.renewal_semester",
            )
            ->first();

        return response()->json($renewal);
    }
    public function updateStatus(Request $request, $scholarId)
    {
        $status = $request->input("renewal_status");

        DB::table("tbl_renewal")
            ->where("scholar_id", $scholarId)
            ->latest("renewal_id")
            ->limit(1)
            ->update(["renewal_status" => $status]);

        // ✅ kapag rejected, update scholar status = inactive
        if ($status === "Rejected") {
            DB::table("tbl_scholar")
                ->where("scholar_id", $scholarId)
                ->update(["scholar_status" => "Inactive"]);
        }

        return response()->json(["success" => true]);
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

        // Fetch unsigned disbursements
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
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("tbl_applicant.applicant_fname", "like", "%$search%")
                      ->orWhere("tbl_applicant.applicant_lname", "like", "%$search%");
                });
            })
            ->when($request->barangay, function ($query, $barangay) {
                $query->where("tbl_applicant.applicant_brgy", $barangay);
            })
            ->when($request->academic_year, function ($query, $academic_year) {
                $query->where("tbl_disburse.disburse_acad_year", $academic_year);
            })
            ->when($request->semester, function ($query, $semester) {
                $query->where("tbl_disburse.disburse_semester", $semester);
            });

        $unsignedDisbursements = $unsignedQuery->paginate(15, ['*'], 'unsigned_page');

        // Fetch signed disbursements
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
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where("tbl_applicant.applicant_fname", "like", "%$search%")
                      ->orWhere("tbl_applicant.applicant_lname", "like", "%$search%");
                });
            })
            ->when($request->barangay, function ($query, $barangay) {
                $query->where("tbl_applicant.applicant_brgy", $barangay);
            })
            ->when($request->academic_year, function ($query, $academic_year) {
                $query->where("tbl_disburse.disburse_acad_year", $academic_year);
            })
            ->when($request->semester, function ($query, $semester) {
                $query->where("tbl_disburse.disburse_semester", $semester);
            });

        $signedDisbursements = $signedQuery->paginate(15, ['*'], 'signed_page');

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

        return redirect()->back()->with('success', 'Disbursement signed successfully.');
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

        return view("lydo_staff.settings", compact(
            "notifications",
            "pendingScreening",
            "pendingRenewals",
            "currentAcadYear"
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/login");
    }


    public function getApplicantDetails($applicant_id)
    {
        $applicant = DB::table('tbl_applicant')
            ->where('applicant_id', $applicant_id)
            ->first();

        if ($applicant) {
            return response()->json($applicant);
        } else {
            return response()->json(['error' => 'Applicant not found'], 404);
        }
    }
}
