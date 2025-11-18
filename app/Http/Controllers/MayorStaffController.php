<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Announce;
use App\Models\Lydopers;
use App\Models\FamilyIntakeSheet;
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
    // Get the current logged-in mayor staff ID
    $currentStaffId = session('lydopers')->lydopers_id;

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
            "app.created_at" // Add this to sort by creation date
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
        ->where("ap.lydopers_id", $currentStaffId) // Add this filter
        ->where("ap.initial_screening", "Pending")
        ->orderBy("app.created_at", "desc"); // Add this line to show newest first

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
            "app.created_at" // Add this to sort by creation date
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
        ->where("ap.lydopers_id", $currentStaffId) // Add this filter
        ->whereIn("ap.initial_screening", ["Approved", "Rejected"])
        ->orderBy("app.created_at", "desc") // Add this line to show newest first
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
        
        ->get();

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
            "app.created_at" // Add this for potential sorting
        )
        ->orderBy("app.created_at", "desc") // Add this to sort applications by newest first
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
                "created_at" => $app->created_at // Include creation date
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
    try {
        // Delete the application personnel record
        $deleted = DB::table("tbl_application_personnel")
            ->where("application_personnel_id", $id)
            ->delete();

        if ($deleted) {
            // Check if it's an AJAX request
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Application deleted successfully.']);
            }
            return redirect()
                ->back()
                ->with("success", "Application deleted successfully.");
        } else {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
            }
            return redirect()
                ->back()
                ->with("error", "Application not found.");
        }
    } catch (\Exception $e) {
        \Log::error('Delete application error: ' . $e->getMessage());
        
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Failed to delete application.'], 500);
        }
        return redirect()
            ->back()
            ->with("error", "Failed to delete application.");
    }
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

        // Generate intake sheet token
        $token = Str::random(64);
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update(['intake_sheet_token' => $token]);

        // Send approval email
        $emailData = [
            'applicant_fname' => $applicationPersonnel->applicant_fname,
            'applicant_lname' => $applicationPersonnel->applicant_lname,
            'application_personnel_id' => $id,
            'token' => $token,
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


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'reason' => 'required_if:status,Rejected|string|max:1000'
        ]);

        // Get the application personnel record
        $applicationPersonnel = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $id)
            ->select('a.application_id', 'app.applicant_email', 'app.applicant_fname', 'app.applicant_lname')
            ->first();

        if (!$applicationPersonnel) {
            return response()->json(['success' => false, 'message' => 'Application not found.']);
        }

        $status = $request->status;
        $reason = $request->reason ?? null;

        // Update the status and rejection_reason if rejected
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update([
                'status' => $status,
                'rejection_reason' => $status === 'Rejected' ? $reason : null,
                'updated_at' => now()
            ]);

        // Send email notification if rejected
        if ($status === 'Rejected' && $reason) {
            $emailData = [
                'applicant_fname' => $applicationPersonnel->applicant_fname,
                'applicant_lname' => $applicationPersonnel->applicant_lname,
                'reason' => $reason,
            ];

            Mail::send('emails.scholar-status-rejection', $emailData, function ($message) use ($applicationPersonnel) {
                $message->to($applicationPersonnel->applicant_email)
                    ->subject('Application Status Update - LYDO Scholarship')
                    ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
            });
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

     public function status()
    {
        // Get the current logged-in mayor staff ID
        $currentStaffId = session('lydopers')->lydopers_id;

        // Get notifications
        $newApplications = DB::table("tbl_application as app")
            ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
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
                    "name" => $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });

        // Get NEW remarks
        $newRemarks = DB::table("tbl_application_personnel as ap")
            ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
            ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
            ->whereIn("ap.remarks", ["Poor", "Non Poor", "Ultra Poor", "Non Indigenous"])
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

        $notifications = $newApplications->merge($newRemarks)->sortByDesc("created_at");
        $showBadge = !session('notifications_viewed');

        // DEBUG: Get applications with relaxed conditions first
        $applications = DB::table('tbl_application_personnel as ap')
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
                'ap.status as status',
                'ap.initial_screening as initial_screening', // For debugging
                'lydo.lydopers_role as role' // For debugging
            )
            ->where('ap.lydopers_id', $currentStaffId) // Add this filter
            ->where('ap.status', 'Pending')
            ->where('ap.initial_screening', 'Reviewed')
            ->whereIn('ap.remarks', ['Poor', 'Ultra Poor', 'Indigenous']) // Include all valid poverty levels
           ->get();

        // Get list of processed applications
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
                'ap.status as status',
                'ap.initial_screening as initial_screening', // For debugging
                'lydo.lydopers_role as role' // For debugging
            )
            ->where('ap.lydopers_id', $currentStaffId) // Add this filter
            ->whereIn('ap.status', ['Approved', 'Rejected'])
            ->get();

        // Get barangays for filtering
        $barangays = DB::table('tbl_applicant')
            ->distinct()
            ->pluck('applicant_brgy');

        return view('mayor_staff.status', compact('notifications', 'applications', 'listApplications', 'barangays', 'showBadge'));
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
        'lydopers_email' => 'required|email|unique:tbl_lydopers,lydopers_email,' . $id . ',lydopers_id', // FIXED LINE
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
    $updatedUser = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();
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
        ->where('lydopers_id', $user->lydopers_id) // Make sure this uses lydopers_id
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
            ->get();

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

        $applications = $query->get();

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
            ->get();

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

        $tableApplicants = $query->get();

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

        $listApplications = $query->get();

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

    public function apiWelcome(Request $request)
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
        // Get document statuses and reason from application_personnel
        $applicationPersonnel = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $applicationPersonnelId)
            ->select([
                'application_letter_status',
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'student_id_status',
                'reason'
            ])
            ->first();

        // Convert to comments format for frontend compatibility
        $comments = [];
        $statuses = [];
        $reasons = [];
        
        if ($applicationPersonnel) {
            $statuses = [
                'application_letter_status' => $applicationPersonnel->application_letter_status,
                'cert_of_reg_status' => $applicationPersonnel->cert_of_reg_status,
                'grade_slip_status' => $applicationPersonnel->grade_slip_status,
                'brgy_indigency_status' => $applicationPersonnel->brgy_indigency_status,
                'student_id_status' => $applicationPersonnel->student_id_status,
            ];
            
            // Parse individual document reasons from JSON
            if ($applicationPersonnel->reason) {
                $reasonsData = json_decode($applicationPersonnel->reason, true);
                if (is_array($reasonsData)) {
                    $reasons = $reasonsData;
                }
            }
        }

        return response()->json([
            'success' => true,
            'comments' => $comments,
            'statuses' => $statuses,
            'reasons' => $reasons
        ]);
    } catch (\Exception $e) {
        \Log::error('Error loading document comments: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to load document status.'], 500);
    }
}
public function saveDocumentStatus(Request $request)
{
    $request->validate([
        'application_personnel_id' => 'required|integer',
        'document_type' => 'required|string',
        'status' => 'required|in:good,bad',
        'reason' => 'nullable|string|max:1000'
    ]);

    try {
        $statusColumn = $request->document_type . '_status';

        // Get current reasons
        $currentReasons = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->value('reason');

        $reasons = [];
        if ($currentReasons) {
            $reasons = json_decode($currentReasons, true) ?? [];
        }

        if ($request->status === 'bad') {
            $reasons[$request->document_type] = $request->reason;
        } else {
            // Remove reason if status is good
            unset($reasons[$request->document_type]);
        }

        // Update status and reasons in tbl_application_personnel
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->update([
                $statusColumn => $request->status,
                'reason' => !empty($reasons) ? json_encode($reasons) : null,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Document status updated successfully.']);
    } catch (\Exception $e) {
        \Log::error('Error saving document status: ' . $e->getMessage());
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

        // Check document status columns in tbl_application_personnel
        $applicationPersonnel = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->select([
                'application_letter_status',
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'student_id_status',
                'reason'
            ])
            ->first();

        $badDocuments = [];
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
                    $badDocuments[] = $type;
                }
            }
        }

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

        // Build documents array for the email template
        $documentsData = [];
        foreach ($badDocuments as $doc) {
            $documentName = '';
            switch ($doc) {
                case 'application_letter':
                    $documentName = 'Application Letter';
                    break;
                case 'cert_of_reg':
                    $documentName = 'Certificate of Registration';
                    break;
                case 'grade_slip':
                    $documentName = 'Grade Slip';
                    break;
                case 'brgy_indigency':
                    $documentName = 'Barangay Indigency';
                    break;
                case 'student_id':
                    $documentName = 'Student ID';
                    break;
            }
            
            // Get reason for this document if available
            $reason = '';
            if ($applicationPersonnel->reason) {
                $reasonsData = json_decode($applicationPersonnel->reason, true);
                if (is_array($reasonsData) && isset($reasonsData[$doc])) {
                    $reason = $reasonsData[$doc];
                }
            }
            
            $documentsData[] = [
                'name' => $documentName,
                'reason' => $reason
            ];
        }

        // Build update link - convert array to string for URL parameter
        $documentTypesString = implode(',', $badDocuments);
        $updateLink = route('scholar.showUpdateApplication', ['applicant_id' => $applicant->applicant_id]) . '?token=' . $updateToken . '&issues=' . $documentTypesString;

        // Send email using the blade template
        $emailData = [
            'applicant_id' => $applicant->applicant_id,
            'updateToken' => $updateToken,
            'applicant_fname' => $applicant->applicant_fname,
            'applicant_lname' => $applicant->applicant_lname,
            'update_link' => $updateLink,
            'bad_documents' => $documentsData,
            'document_types' => $documentTypesString, // This is now a string for the template
        ];

        Mail::send('emails.document-update-required', $emailData, function ($message) use ($applicant) {
            $message->to($applicant->applicant_email)
                ->subject('Document Update Required - LYDO Scholarship')
                ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
        });

        return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
    } catch (\Exception $e) {
        \Log::error('Email sending error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
    }
}
public function showIntakeSheet($application_personnel_id, Request $request)
{
    // Verify token
    $token = $request->query('token');
    if (!$token) {
        abort(403, 'Access denied. Token required.');
    }
    
    // Get application personnel record
    $applicationPersonnel = DB::table('tbl_application_personnel')
        ->where('application_personnel_id', $application_personnel_id)
        ->where('intake_sheet_token', $token)
        ->first();

    if (!$applicationPersonnel) {
        abort(403, 'Invalid token or application not found.');
    }

    // Get applicant data
    $applicant = DB::table('tbl_applicant as a')
        ->join('tbl_application as app', 'a.applicant_id', '=', 'app.applicant_id')
        ->join('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
        ->where('ap.application_personnel_id', $application_personnel_id)
        ->select('a.*')
        ->first();

    if (!$applicant) {
        abort(404, 'Applicant not found.');
    }

    // Pass the application_personnel_id to the view
    return view('intake_sheet.form', compact('applicant', 'application_personnel_id', 'token'));
}

    public function submitIntakeSheetPublic(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer',
            'token' => 'required|string',
            'head' => 'required|array',
            'family' => 'required|array',
            'house' => 'required|array',
        ]);

        // Verify token
        $applicationPersonnel = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->where('intake_sheet_token', $request->token)
            ->first();

        if (!$applicationPersonnel) {
            return response()->json(['success' => false, 'message' => 'Invalid token or application not found.']);
        }

        // Check if intake sheet already submitted
        $existingSheet = FamilyIntakeSheet::where('application_personnel_id', $request->application_personnel_id)->first();
        if ($existingSheet) {
            return response()->json(['success' => false, 'message' => 'Intake sheet already submitted.']);
        }


        // Prepare data for insertion
        $data = [
            'application_personnel_id' => $request->application_personnel_id,
            'head_4ps' => $request->head['_4ps'] ?? null,
            'head_ipno' => $request->head['ipno'] ?? null,
            'head_address' => $request->head['address'] ?? null,
            'head_zone' => $request->head['zone'] ?? null,
            'head_barangay' => $request->head['barangay'] ?? null,
            'head_pob' => $request->head['pob'] ?? null,
            'head_dob' => $request->head['dob'] ?? null,
            'head_educ' => $request->head['educ'] ?? null,
            'head_occ' => $request->head['occ'] ?? null,
            'head_religion' => $request->head['religion'] ?? null,
            'serial_number' => $request->head['serial'] ?? null,
            'location' => $request->location ?? null,
            'house_total_income' => $request->house['total_income'] ?? null,
            'house_net_income' => $request->house['net_income'] ?? null,
            'other_income' => $request->house['other_income'] ?? null,
            'house_house' => $request->house['house'] ?? null,
            'house_house_value' => $request->house['house_value'] ?? null,
            'house_lot' => $request->house['lot'] ?? null,
            'house_lot_value' => $request->house['lot_value'] ?? null,
            'house_house_rent' => $request->house['house_rent'] ?? null,
            'house_lot_rent' => $request->house['lot_rent'] ?? null,
            'house_water' => $request->house['water'] ?? null,
            'house_electric' => $request->house['electric'] ?? null,
            'family_members' => json_encode($request->family),
            'date_entry' => now(),
        ];

        // Create the intake sheet
        FamilyIntakeSheet::create($data);

        // Optionally update application_personnel to mark as submitted
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $request->application_personnel_id)
            ->update(['intake_sheet_submitted' => true, 'updated_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Family intake sheet submitted successfully.']);
    }

    public function getIntakeSheet($application_personnel_id)
    {
        try {
            // Get the intake sheet data
            $intakeSheet = FamilyIntakeSheet::where('application_personnel_id', $application_personnel_id)->first();

            if (!$intakeSheet) {
                return response()->json(['error' => 'Intake sheet not found'], 404);
            }

            // Get applicant details
            $applicant = DB::table('tbl_applicant as a')
                ->join('tbl_application as app', 'a.applicant_id', '=', 'app.applicant_id')
                ->join('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
                ->where('ap.application_personnel_id', $application_personnel_id)
                ->select(
                    'a.applicant_fname',
                    'a.applicant_mname',
                    'a.applicant_lname',
                    'a.applicant_suffix',
                    'a.applicant_gender',
                    'a.applicant_bdate',
                    'a.applicant_email',
                    'a.applicant_brgy',
                    'a.applicant_address',
                    'a.applicant_zone',
                    'a.applicant_pob',
                    'a.applicant_educ',
                    'a.applicant_occ',
                    'a.applicant_religion',
                    'ap.status'
                )
                ->first();

            if (!$applicant) {
                return response()->json(['error' => 'Applicant not found'], 404);
            }

            // Prepare the response data
            $responseData = [
                'serial_number' => $intakeSheet->serial_number,
                'applicant_fname' => $applicant->applicant_fname,
                'applicant_mname' => $applicant->applicant_mname,
                'applicant_lname' => $applicant->applicant_lname,
                'applicant_suffix' => $applicant->applicant_suffix,
                'head_gender' => $applicant->applicant_gender,
                'head_4ps' => $intakeSheet->head_4ps,
                'head_ipno' => $intakeSheet->head_ipno,
                'head_address' => $intakeSheet->head_address ?: $applicant->applicant_address,
                'head_zone' => $intakeSheet->head_zone ?: $applicant->applicant_zone,
                'head_barangay' => $intakeSheet->head_barangay ?: $applicant->applicant_brgy,
                'head_pob' => $intakeSheet->head_pob ?: $applicant->applicant_pob,
                'head_dob' => $intakeSheet->head_dob ?: $applicant->applicant_bdate,
                'head_educ' => $intakeSheet->head_educ ?: $applicant->applicant_educ,
                'head_occ' => $intakeSheet->head_occ ?: $applicant->applicant_occ,
                'head_religion' => $intakeSheet->head_religion ?: $applicant->applicant_religion,
                'location' => $intakeSheet->location,
                'house_total_income' => $intakeSheet->house_total_income,
                'house_net_income' => $intakeSheet->house_net_income,
                'other_income' => $intakeSheet->other_income,
                'house_house' => $intakeSheet->house_house,
                'house_house_value' => $intakeSheet->house_house_value,
                'house_lot' => $intakeSheet->house_lot,
                'house_lot_value' => $intakeSheet->house_lot_value,
                'house_house_rent' => $intakeSheet->house_house_rent,
                'house_lot_rent' => $intakeSheet->house_lot_rent,
                'house_water' => $intakeSheet->house_water,
                'house_electric' => $intakeSheet->house_electric,
                'family_members' => $intakeSheet->family_members,
                'rv_service_records' => $intakeSheet->rv_service_records,
                'signature_client' => $intakeSheet->signature_client ? asset('storage/' . $intakeSheet->signature_client) : null,
                'signature_worker' => $intakeSheet->signature_worker ? asset('storage/' . $intakeSheet->signature_worker) : null,
                'signature_officer' => $intakeSheet->signature_officer ? asset('storage/' . $intakeSheet->signature_officer) : null,
                'status' => $applicant->status,
                'date_entry' => $intakeSheet->date_entry,
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Error fetching intake sheet: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch intake sheet data'], 500);
        }
    }

    public function welcomeApi(Request $request)
    {
        Log::info("Request received: " . $request->method() . " " . $request->path());
        return response()->json(['message' => 'Welcome to the Mayor Staff API!']);
    }

    public function debugApplications(Request $request)
    {
        $currentStaffId = session('lydopers')->lydopers_id;

        $allApplications = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->select(
                'ap.application_personnel_id',
                'app.applicant_fname',
                'app.applicant_lname',
                'ap.remarks',
                'ap.initial_screening',
                'ap.status',
                'ap.lydopers_id'
            )
            ->where('ap.lydopers_id', $currentStaffId)
            ->get();

        return response()->json([
            'current_staff_id' => $currentStaffId,
            'applications' => $allApplications,
            'count' => $allApplications->count()
        ]);
    }

    /**
     * Get applications data for mayor staff table.
     */
    public function getApplicationsData(Request $request)
    {
        try {
            $applications = DB::table('tbl_application as app')
                ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                ->leftJoin('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
                ->select(
                    'app.application_id',
                    'a.applicant_id',
                    'a.applicant_fname',
                    'a.applicant_mname',
                    'a.applicant_lname',
                    'a.applicant_email',
                    'a.applicant_contact_number',
                    'a.applicant_school_name',
                    'a.applicant_year_level',
                    'app.date_submitted',
                    'ap.remarks',
                    'ap.status',
                    'ap.initial_screening'
                )
                ->orderBy('app.date_submitted', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $applications,
                'count' => $applications->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching applications data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
public function submitIntakeSheet(Request $request)
{
    $request->validate([
        'application_personnel_id' => 'required|integer',
        'token' => 'required|string',
        'head' => 'required|array',
        'family' => 'required|array',
        'house' => 'required|array',
    ]);

    // Verify token
    $applicationPersonnel = DB::table('tbl_application_personnel')
        ->where('application_personnel_id', $request->application_personnel_id)
        ->where('intake_sheet_token', $request->token)
        ->first();

    if (!$applicationPersonnel) {
        return response()->json(['success' => false, 'message' => 'Invalid token or application not found.']);
    }

    // Check if intake sheet already submitted
    $existingSheet = FamilyIntakeSheet::where('application_personnel_id', $request->application_personnel_id)->first();
    if ($existingSheet) {
        return response()->json(['success' => false, 'message' => 'Intake sheet already submitted.']);
    }

    // Generate serial number
    $serialNumber = 'FIS-' . now()->format('Ymd-His');

    // Prepare data for insertion
    $data = [
        'application_personnel_id' => $request->application_personnel_id,
        'head_4ps' => $request->head['_4ps'] ?? null,
        'head_ipno' => $request->head['ipno'] ?? null,
        'head_address' => $request->head['address'] ?? null,
        'head_zone' => $request->head['zone'] ?? null,
        'head_barangay' => $request->head['barangay'] ?? null,
        'head_pob' => $request->head['pob'] ?? null,
        'head_dob' => $request->head['dob'] ?? null,
        'head_educ' => $request->head['educ'] ?? null,
        'head_occ' => $request->head['occ'] ?? null,
        'head_religion' => $request->head['religion'] ?? null,
        'serial_number' => $serialNumber,
        'location' => $request->location ?? null,
        'house_total_income' => $request->house['total_income'] ?? null,
        'house_net_income' => $request->house['net_income'] ?? null,
        'other_income' => $request->house['other_income'] ?? null,
        'house_house' => $request->house['house'] ?? null,
        'house_house_value' => $request->house['house_value'] ?? null,
        'house_lot' => $request->house['lot'] ?? null,
        'house_lot_value' => $request->house['lot_value'] ?? null,
        'house_house_rent' => $request->house['house_rent'] ?? null,
        'house_lot_rent' => $request->house['lot_rent'] ?? null,
        'house_water' => $request->house['water'] ?? null,
        'house_electric' => $request->house['electric'] ?? null,
        'family_members' => json_encode($request->family),
        'date_entry' => now(),
    ];

    // Create the intake sheet
    $intakeSheet = FamilyIntakeSheet::create($data);

    // Update application_personnel to mark as submitted
    DB::table('tbl_application_personnel')
        ->where('application_personnel_id', $request->application_personnel_id)
        ->update(['intake_sheet_submitted' => true, 'updated_at' => now()]);

    return response()->json([
        'success' => true, 
        'message' => 'Family intake sheet submitted successfully!'
    ]);
}
}
