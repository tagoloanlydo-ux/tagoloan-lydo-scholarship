<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Announce;
use App\Http\Controllers\SmsController;

class MayorStaffController extends Controller
{
    public function index()
    {
        $newApplications = DB::table("tbl_application as app")
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->select(
                "app.application_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "app.created_at",
            )
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks (Poor, Non Poor, Ultra Poor, Non Indigenous)
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join(
                "tbl_application as app",
                "ap.application_id",
                "=",
                "app.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->whereIn("ap.remarks", [
                "Poor",
                "Non Poor",
                "Ultra Poor",
                "Non Indigenous",
            ])
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
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });


        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        $showBadge = !session('notifications_viewed');

                $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $currentAcadYear = $currentYear . '-' . $nextYear;
    $lastAcadYear = ($currentYear - 1) . '-' . $currentYear;

    $applicantsCurrentYear = DB::table('tbl_applicant')
        ->where('applicant_acad_year', $currentAcadYear)
        ->count();

    $applicantsLastYear = DB::table('tbl_applicant')
        ->where('applicant_acad_year', $lastAcadYear)
        ->count();

    $percentage = $applicantsLastYear > 0
        ? (($applicantsCurrentYear - $applicantsLastYear) / $applicantsLastYear) * 100
        : 0;
$totalApplications = DB::table('tbl_application_personnel as ap')
    ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
    ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
    ->where('app.applicant_acad_year', $currentAcadYear)
    ->count();

$pendingInitial = DB::table('tbl_application_personnel as ap')
    ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
    ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
    ->where('app.applicant_acad_year', $currentAcadYear)
    ->where('ap.initial_screening', 'Pending')
    ->count();

// reviewed = total - pending
$reviewedCount = $totalApplications - $pendingInitial;

// percentage reviewed
$percentageReviewed = $totalApplications > 0
    ? ($reviewedCount / $totalApplications) * 100
    : 0;

    $pendingStatus = DB::table('tbl_application_personnel')
    ->where('status', 'Pending')
    ->count();

    $reviewedInitial = DB::table('tbl_application_personnel')
    ->where('initial_screening', 'Reviewed')
    ->count();

    // Get recent decisions (last 5 approved/rejected/pending applications)
    $recentDecisions = DB::table('tbl_application_personnel as ap')
        ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
        ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
        ->select(
            'ap.application_personnel_id',
            'app.applicant_fname',
            'app.applicant_lname',
            'app.applicant_school_name',
            'app.applicant_course',
            'app.applicant_brgy',
            'ap.status',
            'ap.initial_screening',
            'ap.updated_at'
        )
        ->whereIn('ap.status', ['Approved', 'Rejected', 'Pending'])
        ->orderBy('ap.updated_at', 'desc')
        ->limit(5)
        ->get();

    // Get remarks distribution
    $remarksDistribution = DB::table('tbl_application_personnel')
        ->select('status as remarks', DB::raw('COUNT(*) as count'))
        ->whereIn('status', ['Approved', 'Rejected'])
        ->groupBy('status')
        ->get();

    // Get applicant trend data for the last 5 years
    $applicantTrend = [];
    $currentYear = date('Y');

    for ($i = 4; $i >= 0; $i--) {
        $year = $currentYear - $i;
        $nextYear = $year + 1;
        $academicYear = $year . '-' . $nextYear;

        $count = DB::table('tbl_applicant')
            ->where('applicant_acad_year', $academicYear)
            ->count();

        $applicantTrend[] = [
            'year' => $academicYear,
            'count' => $count
        ];
    }

    // Get available academic years for filtering
    $availableAcademicYears = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->distinct()
        ->orderBy('applicant_acad_year', 'desc')
        ->pluck('applicant_acad_year')
        ->toArray();

    // Get pending applications with details
    $pendingApplications = DB::table('tbl_application_personnel as ap')
        ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
        ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
        ->select(
            'ap.application_personnel_id',
            'app.applicant_fname',
            'app.applicant_lname',
            'app.applicant_school_name',
            'app.applicant_course',
            'app.applicant_brgy',
            'a.created_at',
            'ap.status'
        )
        ->where('ap.status', 'Pending')
        ->orderBy('a.created_at', 'desc')
        ->get();

        return view('mayor_staff.dashboard', compact(
            'notifications',
            'totalApplications',
            'pendingInitial',
            'reviewedCount',
            'percentageReviewed',
            'newApplications',
            'newRemarks',
            'currentAcadYear',
            'lastAcadYear',
            'applicantsCurrentYear',
            'percentage',
            'pendingStatus',
            'reviewedInitial',
            'recentDecisions',
            'remarksDistribution',
            'pendingApplications',
            'applicantTrend',
            'availableAcademicYears',
            'showBadge'
        ));
    }


    public function application(Request $request)
    {
        $query = DB::table("tbl_applicant as a")
            ->join(
                "tbl_application as app",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->join(
                "tbl_application_personnel as ap",
                "app.application_id",
                "=",
                "ap.application_id",
            )
            ->select(
                "a.*",
                "app.application_id",
                "ap.application_personnel_id",
                "ap.status",
                "ap.initial_screening",
                "ap.remarks",
                "a.applicant_email",
            )
            ->where(
                "a.applicant_acad_year",
                "=",
                now()->format("Y") .
                    "-" .
                    now()
                        ->addYear()
                        ->format("Y"),
            );

  
        if ($request->filled("search")) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    "a.applicant_fname",
                    "like",
                    "%" . $request->search . "%",
                )->orWhere(
                    "a.applicant_lname",
                    "like",
                    "%" . $request->search . "%",
                );
            });
        }

  
        if ($request->filled("barangay")) {
            $query->where("a.applicant_brgy", $request->barangay);
        }

       
        $query->where("ap.initial_screening", "Pending");

        $tableApplicants = $query->paginate(15);


        $listApplicants = DB::table("tbl_applicant as a")
            ->join(
                "tbl_application as app",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->join(
                "tbl_application_personnel as ap",
                "app.application_id",
                "=",
                "ap.application_id",
            )
            ->select(
                "a.*",
                "app.application_id",
                "ap.application_personnel_id",
                "ap.status",
                "ap.initial_screening",
                "ap.remarks",
                "a.applicant_email",
            )
            ->where(
                "a.applicant_acad_year",
                "=",
                now()->format("Y") .
                    "-" .
                    now()
                        ->addYear()
                        ->format("Y"),
            )
            ->whereIn("ap.initial_screening", ["Approved", "Rejected"])
            ->when($request->filled("search"), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where(
                        "a.applicant_fname",
                        "like",
                        "%" . $request->search . "%",
                    )->orWhere(
                        "a.applicant_lname",
                        "like",
                        "%" . $request->search . "%",
                    );
                });
            })
            ->when($request->filled("barangay"), function ($q) use ($request) {
                $q->where("a.applicant_brgy", $request->barangay);
            })
            ->paginate(15, ['*'], 'list');

        $barangays = DB::table("tbl_applicant")
            ->pluck("applicant_brgy")
            ->unique();

        $newApplications = DB::table("tbl_application as app")
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->select(
                "app.application_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "app.created_at",
            )
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks (Poor, Non Poor, Ultra Poor, Non Indigenous)
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join(
                "tbl_application as app",
                "ap.application_id",
                "=",
                "app.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->whereIn("ap.remarks", [
                "Poor",
                "Non Poor",
                "Ultra Poor",
                "Non Indigenous",
            ])
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
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

      
        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        $applications = DB::table("tbl_application as app")
            ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
            ->join("tbl_applicant as a", "app.applicant_id", "=", "a.applicant_id")
            ->select(
                "app.application_id",
                "app.applicant_id",
                "ap.application_personnel_id",
                "app.application_letter",
                "app.cert_of_reg",
                "app.grade_slip",
                "app.brgy_indigency",
                "app.student_id",
                "a.applicant_school_name",
                "a.applicant_acad_year",
                "a.applicant_year_level",
                "a.applicant_course",
            )
            ->get()
            ->map(function ($app) {
                return [
                    "application_id" => $app->application_id,
                    "applicant_id" => $app->applicant_id,
                    "application_personnel_id" => $app->application_personnel_id,
                    "application_letter" => $app->application_letter ? "/storage/" . $app->application_letter : null,
                    "cert_of_reg" => $app->cert_of_reg ? "/storage/" . $app->cert_of_reg : null,
                    "grade_slip" => $app->grade_slip ? "/storage/" . $app->grade_slip : null,
                    "brgy_indigency" => $app->brgy_indigency ? "/storage/" . $app->brgy_indigency : null,
                    "student_id" => $app->student_id ? "/storage/" . $app->student_id : null,
                    "school_name" => $app->applicant_school_name,
                    "academic_year" => $app->applicant_acad_year,
                    "year_level" => $app->applicant_year_level,
                    "course" => $app->applicant_course,
                ];
            })
            ->groupBy("applicant_id");

        $showBadge = !session('notifications_viewed');

        // Ensure variables are always set to prevent undefined variable errors
        $tableApplicants = $tableApplicants ?? collect();
        $listApplicants = $listApplicants ?? collect();
        $barangays = $barangays ?? [];
        $notifications = $notifications ?? collect();
        $applications = $applications ?? [];
        $showBadge = $showBadge ?? false;

        return view(
            "mayor_staff.application",
            compact(
                "tableApplicants",
                "listApplicants",
                "barangays",
                "notifications",
                "applications",
                "showBadge",
            ),
        );
    }
    public function updateInitialScreening(Request $request, $id)
    {
        $request->validate([
            "initial_screening" => "required|string|max:1000",
        ]);

        // Save to DB - update the remarks field with the initial screening text
        DB::table("tbl_application_personnel")
            ->where("application_personnel_id", $id)
            ->update([
                "remarks" => $request->initial_screening,
                "updated_at" => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Initial screening remarks updated successfully.']);
    }

    public function deleteApplication($id)
    {
        // Delete the application personnel record
        DB::table("tbl_application_personnel")
            ->where("application_personnel_id", $id)
            ->delete();

        return redirect()
            ->back()
            ->with("success", "Application deleted successfully.");
    }

    public function approveApplication($id)
    {
        // Get the application_id and applicant email from application_personnel and applicant
        $applicationPersonnel = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $id)
            ->select('a.application_id', 'app.applicant_email', 'app.applicant_fname', 'app.applicant_lname')
            ->first();

        if (!$applicationPersonnel) {
            return response()->json(['success' => false, 'message' => 'Application not found.']);
        }

        // Update the initial_screening
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update(['initial_screening' => 'Approved', 'updated_at' => now()]);

        // Send approval email
        $emailData = [
            'applicant_fname' => $applicationPersonnel->applicant_fname,
            'applicant_lname' => $applicationPersonnel->applicant_lname,
        ];

        Mail::send('emails.initial-screening-approval', $emailData, function ($message) use ($applicationPersonnel) {
            $message->to($applicationPersonnel->applicant_email)
                ->subject('Initial Screening Approval - LYDO Scholarship')
                ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
        });

        return response()->json(['success' => true, 'message' => 'Initial screening approved successfully.']);
    }

    public function rejectApplication(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        // Get the application_id and applicant email from application_personnel and applicant
        $applicationPersonnel = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $id)
            ->select('a.application_id', 'app.applicant_email', 'app.applicant_fname', 'app.applicant_lname')
            ->first();

        if (!$applicationPersonnel) {
            return response()->json(['success' => false, 'message' => 'Application not found.']);
        }

        $reason = $request->reason;

        // Update the initial_screening and rejection_reason
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update([
                'initial_screening' => 'Rejected',
                'rejection_reason' => $reason,
                'updated_at' => now()
            ]);

        // Send rejection email
        $emailData = [
            'applicant_fname' => $applicationPersonnel->applicant_fname,
            'applicant_lname' => $applicationPersonnel->applicant_lname,
            'reason' => $reason,
        ];

        Mail::send('emails.initial-screening-rejection', $emailData, function ($message) use ($applicationPersonnel) {
            $message->to($applicationPersonnel->applicant_email)
                ->subject('Initial Screening Rejection - LYDO Scholarship')
                ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
        });

        return response()->json(['success' => true, 'message' => 'Initial screening rejected successfully.']);
    }

    public function editInitialScreening(Request $request, $id)
    {
        $request->validate([
            'initial_screening_status' => 'required|in:Approved,Rejected',
        ]);

        // Update the initial_screening status
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update([
                'initial_screening' => $request->initial_screening_status,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Initial screening status updated successfully.']);
    }
    public function getRequirements($id)
    {
        $application = Application::with("applicant")->findOrFail($id);

        return response()->json([
            "application" => [
                "application_letter" => $application->application_letter,
                "cert_of_reg" => $application->cert_of_reg,
                "grade_slip" => $application->grade_slip,
                "brgy_indigency" => $application->brgy_indigency,
            ],
            "applicant" => [
                "applicant_fname" => $application->applicant->applicant_fname,
                "applicant_lname" => $application->applicant->applicant_lname,
                "applicant_brgy" => $application->applicant->applicant_brgy,
                "applicant_school_name" =>
                    $application->applicant->applicant_school_name,
            ],
        ]);
    }

    public function status(Request $request)
    {
        $newApplications = DB::table("tbl_application as app")
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->select(
                "app.application_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "app.created_at",
            )
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks (Poor, Non Poor, Ultra Poor, Non Indigenous)
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join(
                "tbl_application as app",
                "ap.application_id",
                "=",
                "app.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->whereIn("ap.remarks", [
                "Poor",
                "Non Poor",
                "Ultra Poor",
                "Non Indigenous",
            ])
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
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        $applications = DB::table("tbl_application")
            ->select(
                "application_id",
                "applicant_id",
                "application_letter",
                "cert_of_reg",
                "grade_slip",
                "brgy_indigency",
            )
            ->get()
            ->groupBy("applicant_id");

        $query = DB::table('tbl_application_personnel as ap')
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
            ->where('ap.status', 'Pending')
            ->where('lydo.lydopers_role', 'lydo_staff')
            ->whereIn('ap.remarks', ['Poor', 'Ultra Poor']); // Only show Poor and Ultra Poor

        // ✅ Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('app.applicant_fname', 'like', "%$search%")
                  ->orWhere('app.applicant_lname', 'like', "%$search%");
            });
        }

        // ✅ Filter by barangay
        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        $applications = $query->paginate(15);

        $barangays = DB::table('tbl_applicant')->distinct()->pluck('applicant_brgy');

        $listApplications = DB::table('tbl_application_personnel as ap')
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
            ->where('lydo.lydopers_role', 'lydo_staff')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('app.applicant_fname', 'like', "%{$request->search}%")
                      ->orWhere('app.applicant_lname', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('barangay'), function ($q) use ($request) {
                $q->where('app.applicant_brgy', $request->barangay);
            })
            ->when($request->filled('status_filter'), function ($q) use ($request) {
                $q->where('ap.status', $request->status_filter);
            })
            ->paginate(15, ['*'], 'list');

        $barangays = DB::table('tbl_applicant')->distinct()->pluck('applicant_brgy');

        $showBadge = !session('notifications_viewed');

        $tableApplicants = $applications;

        // If AJAX request, return JSON data
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'tableApplicants' => $tableApplicants->items(),
                'listApplications' => $listApplications->items(),
                'tablePagination' => $tableApplicants->appends(request()->query())->links()->toHtml(),
                'listPagination' => $listApplications->appends(request()->query())->links()->toHtml(),
            ]);
        }

        return view('mayor_staff.status', compact('tableApplicants', 'barangays', 'notifications', 'newApplications', 'newRemarks', 'listApplications', 'showBadge'));
}

// ✅ Update status
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

        // Send SMS notification
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
    }

    // If status is Rejected, send rejection email
    if ($request->status === 'Rejected') {
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
    }

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    return back()->with('success', 'Status updated successfully!');
}
    public function settings()
    {
                   $newApplications = DB::table("tbl_application as app")
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->select(
                "app.application_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "app.created_at",
            )
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks (Poor, Non Poor, Ultra Poor, Non Indigenous)
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join(
                "tbl_application as app",
                "ap.application_id",
                "=",
                "app.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->whereIn("ap.remarks", [
                "Poor",
                "Non Poor",
                "Ultra Poor",
                "Non Indigenous",
            ])
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
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

      
        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        $showBadge = !session('notifications_viewed');

        return view('mayor_staff.settings', compact('notifications', 'newApplications', 'newRemarks', 'showBadge') );
    }

    
    public function report(Request $request)
    {
        $newApplications = DB::table("tbl_application as app")
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->select(
                "app.application_id",
                "a.applicant_fname",
                "a.applicant_lname",
                "app.created_at",
            )
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks (Poor, Non Poor, Ultra Poor, Non Indigenous)
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join(
                "tbl_application as app",
                "ap.application_id",
                "=",
                "app.application_id",
            )
            ->join(
                "tbl_applicant as a",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->whereIn("ap.remarks", [
                "Poor",
                "Non Poor",
                "Ultra Poor",
                "Non Indigenous",
            ])
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
                    "name" =>
                        $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        $notifications = $newApplications
            ->merge($newRemarks)
            ->sortByDesc("created_at");

        $totalApplicants = DB::table('tbl_applicant')->count();

        // Approved na Poor / Ultra Poor
        $poorCount = DB::table('tbl_application_personnel')
            ->where('initial_screening', 'Approved')
            ->where(function ($q) {
                $q->where('remarks', 'Poor')
                  ->orWhere('remarks', 'Ultra Poor');
            })
            ->count();

        // Rejected / Non Poor
        $nonPoorCount = DB::table('tbl_application_personnel')
            ->where(function ($q) {
                $q->where('initial_screening', 'Rejected')
                  ->orWhere('remarks', 'Non Poor');
            })
            ->count();

        // compute percentage
        $poorPercent = $totalApplicants > 0 ? round(($poorCount / $totalApplicants) * 100, 2) : 0;
        $nonPoorPercent = $totalApplicants > 0 ? round(($nonPoorCount / $totalApplicants) * 100, 2) : 0;

        $totalApplicants = DB::table('tbl_applicant')->count();

        // Ultra Poor (Reviewed + Approved)
        $ultraPoorCount = DB::table('tbl_application_personnel')
            ->where('initial_screening', 'Reviewed')
            ->where('remarks', 'Ultra Poor')
            ->where('status', 'Approved')
            ->count();

        // Non Indigenous (Reviewed + Rejected)
        $nonIndigenousCount = DB::table('tbl_application_personnel')
            ->where('initial_screening', 'Reviewed')
            ->where('remarks', 'Non Indigenous')
            ->where('status', 'Rejected')
            ->count();

        // Percentages
        $ultraPoorPercent = $totalApplicants > 0 ? round(($ultraPoorCount / $totalApplicants) * 100, 2) : 0;
        $nonIndigenousPercent = $totalApplicants > 0 ? round(($nonIndigenousCount / $totalApplicants) * 100, 2) : 0;

        $years = DB::table('tbl_application_personnel')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();

        // Line Chart 1: Initial Screening Approved / Rejected
        $approvedTrend = [];
        $rejectedTrend = [];

        foreach ($years as $year) {
            $approvedTrend[] = DB::table('tbl_application_personnel')
                ->where('initial_screening', 'Approved')
                ->whereYear('created_at', $year)
                ->count();

            $rejectedTrend[] = DB::table('tbl_application_personnel')
                ->where('initial_screening', 'Rejected')
                ->whereYear('created_at', $year)
                ->count();
        }

        // Line Chart 2: Reviewed Initial Screening (status Approved / Rejected)
        $reviewedApprovedTrend = [];
        $reviewedRejectedTrend = [];

        foreach ($years as $year) {
            $reviewedApprovedTrend[] = DB::table('tbl_application_personnel')
                ->where('initial_screening', 'Reviewed')
                ->where('status', 'Approved')
                ->whereYear('created_at', $year)
                ->count();

            $reviewedRejectedTrend[] = DB::table('tbl_application_personnel')
                ->where('initial_screening', 'Reviewed')
                ->where('status', 'Rejected')
                ->whereYear('created_at', $year)
                ->count();
        }

        // Get barangays for filtering
        $barangays = DB::table('tbl_applicant')
            ->distinct()
            ->pluck('applicant_brgy');

        // Query for listApplications with filtering
        $listApplicationsQuery = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_lname as lname',
                'app.applicant_brgy as barangay',
                'ap.initial_screening',
                'ap.status'
            )
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected']);

        // Apply filters
        if ($request->filled('barangay')) {
            $listApplicationsQuery->where('app.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $listApplicationsQuery->whereYear('ap.created_at', $request->academic_year);
        }

        if ($request->filled('initial_screening')) {
            $listApplicationsQuery->where('ap.initial_screening', $request->initial_screening);
        }

        // Add status filter
        if ($request->filled('status')) {
            $listApplicationsQuery->where('ap.status', $request->status);
        }

        $listApplications = $listApplicationsQuery->get();

        // Query for statusApplications (for the new Status Report tab)
        $statusApplicationsQuery = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_lname as lname',
                'app.applicant_brgy as barangay',
                'ap.initial_screening',
                'ap.status'
            )
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected'])
            ->whereIn('ap.status', ['Approved', 'Rejected']);

        // Apply status report filters
        if ($request->filled('status_barangay')) {
            $statusApplicationsQuery->where('app.applicant_brgy', $request->status_barangay);
        }

        if ($request->filled('status_academic_year')) {
            $statusApplicationsQuery->whereYear('ap.created_at', $request->status_academic_year);
        }

        if ($request->filled('status_initial_screening')) {
            $statusApplicationsQuery->where('ap.initial_screening', $request->status_initial_screening);
        }

        $statusApplications = $statusApplicationsQuery->get();

        return view('mayor_staff.report', compact(
            'notifications', 
            'newApplications', 
            'newRemarks', 
            'ultraPoorCount',
            'ultraPoorPercent',
            'nonIndigenousCount',
            'nonIndigenousPercent',
            'poorCount',
            'poorPercent',
            'nonPoorCount',
            'years',
            'approvedTrend',
            'rejectedTrend',
            'reviewedApprovedTrend',
            'reviewedRejectedTrend',
            'nonPoorPercent',
            'barangays',
            'listApplications',
            'statusApplications'
        )); 
    }

    public function printReport(Request $request)
    {
        $type = $request->query('type', 'all');
        $barangay = $request->query('barangay');
        $academicYear = $request->query('academic_year');
        $screeningStatus = $request->query('initial_screening');
        $statusFilter = $request->query('status');

        $query = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_lname as lname',
                'app.applicant_brgy as barangay',
                'ap.initial_screening',
                'ap.status',
                'ap.created_at'
            )
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected']);

        if ($barangay) {
            $query->where('app.applicant_brgy', $barangay);
        }

        if ($academicYear) {
            $query->whereYear('ap.created_at', $academicYear);
        }

        if ($screeningStatus) {
            $query->where('ap.initial_screening', $screeningStatus);
        }

        // Add status filter
        if ($statusFilter) {
            $query->where('ap.status', $statusFilter);
        }

        $applications = $query->get();

        if ($type === 'barangay') {
            $applications = $applications->groupBy('barangay');
        }

        return view('mayor_staff.print-report', compact('applications', 'type', 'barangay', 'academicYear', 'screeningStatus', 'statusFilter'));
    }

    public function printStatusReport(Request $request)
    {
        $type = $request->query('type', 'all');
        $barangay = $request->query('barangay');
        $academicYear = $request->query('academic_year');
        $screeningStatus = $request->query('initial_screening');

        $query = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname as fname',
                'app.applicant_lname as lname',
                'app.applicant_brgy as barangay',
                'ap.initial_screening',
                'ap.status',
                'ap.created_at'
            )
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected'])
            ->whereIn('ap.status', ['Approved', 'Rejected']);

        if ($barangay) {
            $query->where('app.applicant_brgy', $barangay);
        }

        if ($academicYear) {
            $query->whereYear('ap.created_at', $academicYear);
        }

        if ($screeningStatus) {
            $query->where('ap.initial_screening', $screeningStatus);
        }

        $applications = $query->get();

        if ($type === 'barangay') {
            $applications = $applications->groupBy('barangay');
        }

        return view('mayor_staff.print-status-report', compact('applications', 'type', 'barangay', 'academicYear', 'screeningStatus'));
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer',
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'application_issues' => 'nullable|array',
            'application_issues.*' => 'in:application_letter,cert_of_reg,grade_slip,brgy_indigency',
        ]);

        try {
            // Get applicant details
            $applicant = DB::table('tbl_application_personnel as ap')
                ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
                ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
                ->where('ap.application_personnel_id', $request->application_personnel_id)
                ->select('app.applicant_fname', 'app.applicant_lname', 'app.applicant_email')
                ->first();

            if (!$applicant) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Applicant not found.']);
                }
                return redirect()->back()->with('error', 'Applicant not found.');
            }

            // Build the email message with application issues if provided
            $emailMessage = $request->message;

            if ($request->has('application_issues') && is_array($request->application_issues) && !empty($request->application_issues)) {
                $issues = [];
                foreach ($request->application_issues as $issue) {
                    switch ($issue) {
                        case 'application_letter':
                            $issues[] = 'Application Letter';
                            break;
                        case 'cert_of_reg':
                            $issues[] = 'Certificate of Registration';
                            break;
                        case 'grade_slip':
                            $issues[] = 'Grade Slip';
                            break;
                        case 'brgy_indigency':
                            $issues[] = 'Barangay Indigency';
                            break;
                        case 'student_id':
                            $issues[] = 'Student ID';
                            break;
                    }
                }

                if (!empty($issues)) {
                    $emailMessage .= "\n\nThe following documents have issues and need to be resubmitted:\n" . implode("\n", array_map(function($issue) {
                        return "- " . $issue;
                    }, $issues));
                }
            }

            // Send email using Laravel's Mail facade
            Mail::raw($emailMessage, function ($mail) use ($request, $applicant) {
                $mail->to($request->recipient_email)
                     ->subject($request->subject)
                     ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
            });

            $successMessage = 'Email sent successfully to ' . $applicant->applicant_fname . ' ' . $applicant->applicant_lname . '.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $successMessage]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            $errorMessage = 'Failed to send email. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage]);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    public function updatePersonalInfo(Request $request, $id)
    {
        $request->validate([
            'lydopers_fname' => 'required|string|max:255',
            'lydopers_lname' => 'required|string|max:255',
            'lydopers_email' => 'required|email|unique:tbl_lydopers,lydopers_email,' . $id,
            'lydopers_address' => 'required|string|max:500',
            'lydopers_contact_number' => 'required|string|max:20',
        ]);

        DB::table('tbl_lydopers')
            ->where('lydopers_id', $id)
            ->update([
                'lydopers_fname' => $request->lydopers_fname,
                'lydopers_lname' => $request->lydopers_lname,
                'lydopers_email' => $request->lydopers_email,
                'lydopers_address' => $request->lydopers_address,
                'lydopers_contact_number' => $request->lydopers_contact_number,
                'updated_at' => now(),
            ]);

        // Update session
        $updatedUser = DB::table('tbl_lydopers')->find($id);
        session(['lydopers' => $updatedUser]);

        return redirect()->back()->with('success', 'Personal information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = session('lydopers');
        if (!Hash::check($request->current_password, $user->lydopers_password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        DB::table('tbl_lydopers')
            ->where('lydopers_id', $user->lydopers_id)
            ->update([
                'lydopers_password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function markNotificationsViewed(Request $request)
    {
        session(['notifications_viewed' => true]);
        return response()->json(['success' => true]);
    }



    // New method for application page realtime updates
    public function getApplicationUpdates(Request $request)
    {
        $query = DB::table("tbl_applicant as a")
            ->join(
                "tbl_application as app",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->join(
                "tbl_application_personnel as ap",
                "app.application_id",
                "=",
                "ap.application_id",
            )
            ->select(
                "a.*",
                "app.application_id",
                "ap.application_personnel_id",
                "ap.status",
                "ap.initial_screening",
                "ap.remarks",
                "a.applicant_email",
            )
            ->where(
                "a.applicant_acad_year",
                "=",
                now()->format("Y") .
                    "-" .
                    now()
                        ->addYear()
                        ->format("Y"),
            )
            ->where("ap.initial_screening", "Pending");

        if ($request->filled("search")) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    "a.applicant_fname",
                    "like",
                    "%" . $request->search . "%",
                )->orWhere(
                    "a.applicant_lname",
                    "like",
                    "%" . $request->search . "%",
                );
            });
        }

        if ($request->filled("barangay")) {
            $query->where("a.applicant_brgy", $request->barangay);
        }

        $tableApplicants = $query->get();

        $listApplicants = DB::table("tbl_applicant as a")
            ->join(
                "tbl_application as app",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->join(
                "tbl_application_personnel as ap",
                "app.application_id",
                "=",
                "ap.application_id",
            )
            ->select(
                "a.*",
                "app.application_id",
                "ap.application_personnel_id",
                "ap.status",
                "ap.initial_screening",
                "ap.remarks",
                "a.applicant_email",
            )
            ->where(
                "a.applicant_acad_year",
                "=",
                now()->format("Y") .
                    "-" .
                    now()
                        ->addYear()
                        ->format("Y"),
            )
            ->whereIn("ap.initial_screening", ["Approved", "Rejected"])
            ->when($request->filled("search"), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where(
                        "a.applicant_fname",
                        "like",
                        "%" . $request->search . "%",
                    )->orWhere(
                        "a.applicant_lname",
                        "like",
                        "%" . $request->search . "%",
                    );
                });
            })
            ->when($request->filled("barangay"), function ($q) use ($request) {
                $q->where("a.applicant_brgy", $request->barangay);
            })
            ->paginate(15, ['*'], 'list');

        $applications = DB::table("tbl_application as app")
            ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
            ->join("tbl_applicant as a", "app.applicant_id", "=", "a.applicant_id")
            ->select(
                "app.application_id",
                "app.applicant_id",
                "ap.application_personnel_id",
                "app.application_letter",
                "app.cert_of_reg",
                "app.grade_slip",
                "app.brgy_indigency",
                "app.student_id",
                "a.applicant_school_name",
                "a.applicant_acad_year",
                "a.applicant_year_level",
                "a.applicant_course",
            )
            ->get()
            ->map(function ($app) {
                return [
                    "application_id" => $app->application_id,
                    "applicant_id" => $app->applicant_id,
                    "application_personnel_id" => $app->application_personnel_id,
                    "application_letter" => $app->application_letter ? "/storage/" . $app->application_letter : null,
                    "cert_of_reg" => $app->cert_of_reg ? "/storage/" . $app->cert_of_reg : null,
                    "grade_slip" => $app->grade_slip ? "/storage/" . $app->grade_slip : null,
                    "brgy_indigency" => $app->brgy_indigency ? "/storage/" . $app->brgy_indigency : null,
                    "student_id" => $app->student_id ? "/storage/" . $app->student_id : null,
                    "school_name" => $app->applicant_school_name,
                    "academic_year" => $app->applicant_acad_year,
                    "year_level" => $app->applicant_year_level,
                    "course" => $app->applicant_course,
                ];
            })
            ->groupBy("applicant_id");

        return response()->json([
            'tableApplicants' => $tableApplicants,
            'listApplicants' => $listApplicants,
            'applications' => $applications,
        ]);
    }

    public function getFilteredApplicants(Request $request)
    {
        $query = DB::table("tbl_applicant as a")
            ->join(
                "tbl_application as app",
                "a.applicant_id",
                "=",
                "app.applicant_id",
            )
            ->join(
                "tbl_application_personnel as ap",
                "app.application_id",
                "=",
                "ap.application_id",
            )
            ->select(
                "a.*",
                "app.application_id",
                "ap.application_personnel_id",
                "ap.status",
                "ap.initial_screening",
                "ap.remarks",
                "a.applicant_email",
            )
            ->where(
                "a.applicant_acad_year",
                "=",
                now()->format("Y") .
                    "-" .
                    now()
                        ->addYear()
                        ->format("Y"),
            )
            ->where("ap.initial_screening", "Pending");

        if ($request->filled("search")) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    "a.applicant_fname",
                    "like",
                    "%" . $request->search . "%",
                )->orWhere(
                    "a.applicant_lname",
                    "like",
                    "%" . $request->search . "%",
                );
            });
        }

        if ($request->filled("barangay")) {
            $query->where("a.applicant_brgy", $request->barangay);
        }

        $tableApplicants = $query->get();

        $applications = DB::table("tbl_application as app")
            ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
            ->join("tbl_applicant as a", "app.applicant_id", "=", "a.applicant_id")
            ->select(
                "app.application_id",
                "app.applicant_id",
                "ap.application_personnel_id",
                "app.application_letter",
                "app.cert_of_reg",
                "app.grade_slip",
                "app.brgy_indigency",
                "app.student_id",
                "a.applicant_school_name",
                "a.applicant_acad_year",
                "a.applicant_year_level",
                "a.applicant_course",
            )
            ->where("ap.initial_screening", "Pending")
            ->when($request->filled("search"), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where(
                        "a.applicant_fname",
                        "like",
                        "%" . $request->search . "%",
                    )->orWhere(
                        "a.applicant_lname",
                        "like",
                        "%" . $request->search . "%",
                    );
                });
            })
            ->when($request->filled("barangay"), function ($q) use ($request) {
                $q->where("a.applicant_brgy", $request->barangay);
            })
            ->get()
            ->map(function ($app) {
                return [
                    "application_id" => $app->application_id,
                    "applicant_id" => $app->applicant_id,
                    "application_personnel_id" => $app->application_personnel_id,
                    "application_letter" => $app->application_letter ? "/storage/" . $app->application_letter : null,
                    "cert_of_reg" => $app->cert_of_reg ? "/storage/" . $app->cert_of_reg : null,
                    "grade_slip" => $app->grade_slip ? "/storage/" . $app->grade_slip : null,
                    "brgy_indigency" => $app->brgy_indigency ? "/storage/" . $app->brgy_indigency : null,
                    "student_id" => $app->student_id ? "/storage/" . $app->student_id : null,
                    "school_name" => $app->applicant_school_name,
                    "academic_year" => $app->applicant_acad_year,
                    "year_level" => $app->applicant_year_level,
                    "course" => $app->applicant_course,
                ];
            })
            ->groupBy("applicant_id");

        return response()->json([
            'tableApplicants' => $tableApplicants,
            'applications' => $applications,
        ]);
    }

    // New method for status page realtime updates
    public function getStatusUpdates(Request $request)
    {
        $query = DB::table('tbl_application_personnel as ap')
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
            ->where('ap.status', 'Pending')
            ->where('lydo.lydopers_role', 'lydo_staff');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('app.applicant_fname', 'like', "%$search%")
                  ->orWhere('app.applicant_lname', 'like', "%$search%");
            });
        }

        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        $applications = $query->paginate(15);

        $listApplications = DB::table('tbl_application_personnel as ap')
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
            ->where('lydo.lydopers_role', 'lydo_staff')
            ->paginate(15, ['*'], 'list');

        return response()->json([
            'applications' => $applications->items(),
            'listApplications' => $listApplications->items(),
        ]);
    }

    public function sseApplicants(Request $request)
    {
        $lastId = $request->query('last_id', 0);

        return response()->stream(function () use ($lastId) {
            while (true) {
                // Get new pending applications since last_id
                $newApplications = DB::table("tbl_application as app")
                    ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
                    ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
                    ->select(
                        "app.application_id",
                        "ap.application_personnel_id",
                        "a.applicant_fname",
                        "a.applicant_lname",
                        "a.applicant_brgy",
                        "a.applicant_gender",
                        "a.applicant_bdate",
                        "a.applicant_email",
                        "app.created_at",
                    )
                    ->where("app.application_id", ">", $lastId)
                    ->where("ap.initial_screening", "Pending")
                    ->orderBy("app.application_id", "asc")
                    ->get();

                foreach ($newApplications as $application) {
                    echo "data: " . json_encode($application) . "\n\n";
                    ob_flush();
                    flush();
                    $lastId = $application->application_id;
                }

                sleep(5); // Poll every 5 seconds
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    public function getFilteredPendingApplicants(Request $request)
    {
        $query = DB::table('tbl_application_personnel as ap')
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
            ->where('ap.status', 'Pending')
            ->where('lydo.lydopers_role', 'lydo_staff')
            ->whereIn('ap.remarks', ['Poor', 'Ultra Poor']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('app.applicant_fname', 'like', "%$search%")
                  ->orWhere('app.applicant_lname', 'like', "%$search%");
            });
        }

        // Apply barangay filter
        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        $tableApplicants = $query->paginate(15);

        return response()->json([
            'data' => $tableApplicants->items(),
            'pagination' => [
                'current_page' => $tableApplicants->currentPage(),
                'last_page' => $tableApplicants->lastPage(),
                'per_page' => $tableApplicants->perPage(),
                'total' => $tableApplicants->total(),
                'links' => $tableApplicants->links()->toHtml(),
            ]
        ]);
    }

    public function getFilteredProcessedApplicants(Request $request)
    {
        $query = DB::table('tbl_application_personnel as ap')
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
            ->where('lydo.lydopers_role', 'lydo_staff');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('app.applicant_fname', 'like', "%$search%")
                  ->orWhere('app.applicant_lname', 'like', "%$search%");
            });
        }

        // Apply barangay filter
        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        // Apply status filter
        if ($request->filled('status_filter')) {
            $query->where('ap.status', $request->status_filter);
        }

        $listApplications = $query->paginate(15, ['*'], 'list');

        return response()->json([
            'data' => $listApplications->items(),
            'pagination' => [
                'current_page' => $listApplications->currentPage(),
                'last_page' => $listApplications->lastPage(),
                'per_page' => $listApplications->perPage(),
                'total' => $listApplications->total(),
                'links' => $listApplications->links()->toHtml(),
            ]
        ]);
    }

    public function welcome(Request $request)
    {
        Log::info("Request received: " . $request->method() . " " . $request->path());
        return response()->json(['message' => 'Welcome to the Mayor Staff API!']);
    }

    public function saveDocumentComment(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer',
            'document_type' => 'required|string',
            'comment' => 'required|string',
            'is_bad' => 'required|boolean'
        ]);

        try {
            // Check if comment already exists
            $existingComment = DB::table('tbl_document_comments')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->where('document_type', $request->document_type)
                ->first();

            if ($existingComment) {
                // Update existing comment
                DB::table('tbl_document_comments')
                    ->where('id', $existingComment->id)
                    ->update([
                        'comment' => $request->comment,
                        'is_bad' => $request->is_bad,
                        'updated_at' => now()
                    ]);
            } else {
                // Create new comment
                DB::table('tbl_document_comments')->insert([
                    'application_personnel_id' => $request->application_personnel_id,
                    'document_type' => $request->document_type,
                    'comment' => $request->comment,
                    'is_bad' => $request->is_bad,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Update document status in application_personnel table
            $statusColumn = $request->document_type . '_status';
            $status = $request->is_bad ? 'bad' : 'good';

            DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->update([
                    $statusColumn => $status,
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Comment saved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save comment.'], 500);
        }
    }

    public function getDocumentComments($applicationPersonnelId)
    {
        try {
            $comments = DB::table('tbl_document_comments')
                ->where('application_personnel_id', $applicationPersonnelId)
                ->get()
                ->keyBy('document_type');

            // Also get document statuses from application_personnel
            $statuses = DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $applicationPersonnelId)
                ->select([
                    'application_letter_status',
                    'cert_of_reg_status',
                    'grade_slip_status',
                    'brgy_indigency_status',
                    'student_id_status'
                ])
                ->first();

            return response()->json([
                'success' => true,
                'comments' => $comments,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load comments.'], 500);
        }
    }

    public function saveDocumentStatus(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer',
            'document_type' => 'required|string',
            'status' => 'required|in:good,bad'
        ]);

        try {
            $statusColumn = $request->document_type . '_status';

            // Update status in tbl_application_personnel
            DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->update([
                    $statusColumn => $request->status,
                    'updated_at' => now()
                ]);

            // Update is_bad in tbl_document_comments
            DB::table('tbl_document_comments')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->where('document_type', $request->document_type)
                ->update([
                    'is_bad' => $request->status === 'bad',
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Document status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update document status.'], 500);
        }
    }

public function sendDocumentEmail(Request $request)
{
    $request->validate([
        'application_personnel_id' => 'required|integer'
    ]);

    try {
        // Get applicant details
        $applicant = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $request->application_personnel_id)
            ->select('app.applicant_id', 'app.applicant_fname', 'app.applicant_lname', 'app.applicant_email', 'ap.update_token')
            ->first();

        if (!$applicant) {
            return response()->json(['success' => false, 'message' => 'Applicant not found.']);
        }

        // Get bad documents from both tbl_document_comments and tbl_application_personnel status columns
        $badDocumentsFromComments = DB::table('tbl_document_comments')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->where('is_bad', true)
            ->pluck('document_type')
            ->toArray();

        // Also check document status columns in tbl_application_personnel
        $applicationPersonnel = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->select([
                'application_letter_status',
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'student_id_status'
            ])
            ->first();

        $badDocumentsFromStatuses = [];
        if ($applicationPersonnel) {
            $documentTypes = [
                'application_letter' => $applicationPersonnel->application_letter_status,
                'cert_of_reg' => $applicationPersonnel->cert_of_reg_status,
                'grade_slip' => $applicationPersonnel->grade_slip_status,
                'brgy_indigency' => $applicationPersonnel->brgy_indigency_status,
                'student_id' => $applicationPersonnel->student_id_status,
            ];

            foreach ($documentTypes as $type => $status) {
                if ($status === 'bad') {
                    $badDocumentsFromStatuses[] = $type;
                }
            }
        }

        // Combine both sources of bad documents
        $badDocuments = array_unique(array_merge($badDocumentsFromComments, $badDocumentsFromStatuses));

        if (empty($badDocuments)) {
            return response()->json(['success' => false, 'message' => 'No bad documents found to send email.']);
        }

        // Generate update token if not exists
        $applicationPersonnelRecord = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->first();

        if (!$applicationPersonnelRecord->update_token) {
            $updateToken = Str::random(64);
            DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->update(['update_token' => $updateToken]);
        } else {
            $updateToken = $applicationPersonnelRecord->update_token;
        }

        // FIX: Use the correct route parameter name 'applicant_id' (not 'applicant_id')
        $updateLink = route('scholar.showUpdateApplication', ['applicant_id' => $applicant->applicant_id]) . '?token=' . $updateToken . '&issues=' . implode(',', $badDocuments);

        // Build email message
        $documentNames = [];
        foreach ($badDocuments as $doc) {
            switch ($doc) {
                case 'application_letter':
                    $documentNames[] = 'Application Letter';
                    break;
                case 'cert_of_reg':
                    $documentNames[] = 'Certificate of Registration';
                    break;
                case 'grade_slip':
                    $documentNames[] = 'Grade Slip';
                    break;
                case 'brgy_indigency':
                    $documentNames[] = 'Barangay Indigency';
                    break;
                case 'student_id':
                    $documentNames[] = 'Student ID';
                    break;
            }
        }

        // Send email using the blade template
        $emailData = [
            'applicant_id' => $applicant->applicant_id,
            'updateToken' => $updateToken,
            'applicant_fname' => $applicant->applicant_fname,
            'applicant_lname' => $applicant->applicant_lname,
            'bad_documents' => $documentNames,
        ];

        Mail::send('emails.document-update-required', $emailData, function ($message) use ($applicant) {
            $message->to($applicant->applicant_email)
                ->subject('Document Update Required - LYDO Scholarship')
                ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
        });

        // Save email message to database
        DB::table('tbl_document_email_messages')->insert([
            'application_personnel_id' => $request->application_personnel_id,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
    } catch (\Exception $e) {
        \Log::error('Email sending error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
    }
}

}
