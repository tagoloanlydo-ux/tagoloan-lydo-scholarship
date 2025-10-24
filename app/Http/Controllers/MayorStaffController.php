<?php

declare(strict_types=1);

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Announce;
use App\Http\Controllers\SmsController;

class MayorStaffController extends Controller
{
    /**
     * Display the mayor staff dashboard with statistics and notifications.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Display the application management page for mayor staff.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
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
            ->when($request->filled("search_reviewed"), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where(
                        "a.applicant_fname",
                        "like",
                        "%" . $request->search_reviewed . "%",
                    )->orWhere(
                        "a.applicant_lname",
                        "like",
                        "%" . $request->search_reviewed . "%",
                    );
                });
            })
            ->when($request->filled("barangay_reviewed"), function ($q) use ($request) {
                $q->where("a.applicant_brgy", $request->barangay_reviewed);
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

    /**
     * Update the initial screening remarks for an application.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Delete an application.
     *
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Approve an application and send approval email with intake sheet token.
     *
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\JsonResponse
     */
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

        // Update the initial_screening and generate intake sheet token
        $token = \Illuminate\Support\Str::random(64);
        DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $id)
            ->update(['initial_screening' => 'Approved', 'intake_sheet_token' => $token, 'updated_at' => now()]);

        // Send approval email (only if email is present)
        if ($applicationPersonnel->applicant_email) {
            try {
                $emailData = [
                    'applicant_fname' => $applicationPersonnel->applicant_fname,
                    'applicant_lname' => $applicationPersonnel->applicant_lname,
                    'application_personnel_id' => $id,
                ];

                Mail::send('emails.initial-screening-approval', array_merge($emailData, ['token' => $token]), function ($message) use ($applicationPersonnel) {
                    $message->to($applicationPersonnel->applicant_email)
                        ->subject('Initial Screening Approval - LYDO Scholarship')
                        ->from(config('mail.from.address', 'noreply@lydoscholarship.com'), 'LYDO Scholarship');
                });
            } catch (\Exception $e) {
                // Log the email failure but don't fail the approval
                Log::error('Failed to send approval email to ' . $applicationPersonnel->applicant_email . ': ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Initial screening approved successfully.']);
    }

    /**
     * Reject an application with a reason and send rejection email.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\JsonResponse
     */
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

        // Send rejection email (only if email is present)
        if ($applicationPersonnel->applicant_email) {
            try {
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
            } catch (\Exception $e) {
                // Log the email failure but don't fail the rejection
                Log::error('Failed to send rejection email to ' . $applicationPersonnel->applicant_email . ': ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Initial screening rejected successfully.']);
    }

    /**
     * Get application requirements for a specific application.
     *
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicationRequirements($id)
    {
        // Get application personnel record
        $applicationPersonnel = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $id)
            ->select(
                'a.application_letter',
                'a.cert_of_reg',
                'a.grade_slip',
                'a.brgy_indigency',
                'a.student_id',
                'app.applicant_fname',
                'app.applicant_lname',
                'app.applicant_brgy',
                'app.applicant_school_name',
                'app.applicant_course'
            )
            ->first();

        if (!$applicationPersonnel) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        return response()->json([
            "application" => [
                "application_letter" => $applicationPersonnel->application_letter ? "/storage/" . $applicationPersonnel->application_letter : null,
                "cert_of_reg" => $applicationPersonnel->cert_of_reg ? "/storage/" . $applicationPersonnel->cert_of_reg : null,
                "grade_slip" => $applicationPersonnel->grade_slip ? "/storage/" . $applicationPersonnel->grade_slip : null,
                "brgy_indigency" => $applicationPersonnel->brgy_indigency ? "/storage/" . $applicationPersonnel->brgy_indigency : null,
                "student_id" => $applicationPersonnel->student_id ? "/storage/" . $applicationPersonnel->student_id : null,
            ],
            "applicant" => [
                "applicant_fname" => $applicationPersonnel->applicant_fname,
                "applicant_lname" => $applicationPersonnel->applicant_lname,
                "applicant_brgy" => $applicationPersonnel->applicant_brgy,
                "applicant_school_name" => $applicationPersonnel->applicant_school_name,
                "applicant_course" => $applicationPersonnel->applicant_course,
            ],
        ]);
    }

    /**
     * Display the status management page for mayor staff.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
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

$applications = $query->get();

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
        ->get();

    $barangays = DB::table('tbl_applicant')->distinct()->pluck('applicant_brgy');

    $showBadge = !session('notifications_viewed');

    return view('mayor_staff.status', compact('applications', 'barangays', 'notifications', 'applications','newApplications', 'newRemarks', 'listApplications', 'showBadge'  ));
}

    /**
     * Update the status of an application (Approved/Rejected).
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Application personnel ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Display the settings page for mayor staff.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Display the reports page with various statistics and charts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
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

    /**
     * Generate and print a report based on filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
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

    /**
     * Generate and print a status report based on filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
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

    /**
     * Send an email to an applicant with optional application issues.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Update personal information for a mayor staff member.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Lydopers ID
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Update the password for the logged-in mayor staff member.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Mark notifications as viewed for the logged-in mayor staff member.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNotificationsViewed(Request $request)
    {
        session(['notifications_viewed' => true]);
        return response()->json(['success' => true]);
    }

    // New method for dashboard realtime updates
    public function getDashboardUpdates(Request $request)
    {
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

        $reviewedCount = $totalApplications - $pendingInitial;

        $percentageReviewed = $totalApplications > 0
            ? ($reviewedCount / $totalApplications) * 100
            : 0;

        $pendingStatus = DB::table('tbl_application_personnel')
            ->where('status', 'Pending')
            ->count();

        $reviewedInitial = DB::table('tbl_application_personnel')
            ->where('initial_screening', 'Reviewed')
            ->count();

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

        $remarksDistribution = DB::table('tbl_application_personnel')
            ->select('status as remarks', DB::raw('COUNT(*) as count'))
            ->whereIn('status', ['Approved', 'Rejected'])
            ->groupBy('status')
            ->get();

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

        return response()->json([
            'notifications' => $notifications->values(),
            'applicantsCurrentYear' => $applicantsCurrentYear,
            'percentage' => $percentage,
            'totalApplications' => $totalApplications,
            'pendingInitial' => $pendingInitial,
            'reviewedCount' => $reviewedCount,
            'percentageReviewed' => $percentageReviewed,
            'pendingStatus' => $pendingStatus,
            'reviewedInitial' => $reviewedInitial,
            'recentDecisions' => $recentDecisions,
            'remarksDistribution' => $remarksDistribution,
            'applicantTrend' => $applicantTrend,
        ]);
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

    public function getApplicationsData(Request $request)
    {
        $type = $request->get('type', 'pending'); // 'pending' or 'reviewed'

        $currentAcadYear = now()->format("Y") . "-" . now()->addYear()->format("Y");

        $query = DB::table("tbl_applicant as a")
            ->join("tbl_application as app", "a.applicant_id", "=", "app.applicant_id")
            ->join("tbl_application_personnel as ap", "app.application_id", "=", "ap.application_id")
            ->select(
                "a.applicant_fname",
                "a.applicant_lname",
                "a.applicant_brgy",
                "a.applicant_gender",
                "a.applicant_bdate",
                "ap.application_personnel_id",
                "ap.initial_screening"
            )
            ->where("a.applicant_acad_year", "=", $currentAcadYear);

        if ($type === 'pending') {
            $query->where("ap.initial_screening", "Pending");
        } else {
            $query->whereIn("ap.initial_screening", ["Approved", "Rejected"]);
        }

        // DataTables server-side processing
        $totalRecords = $query->count();

        // Apply search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('a.applicant_fname', 'like', '%' . $searchValue . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $searchValue . '%');
            });
        }

        // Apply barangay filter (custom filter)
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        $filteredRecords = $query->count();

        // Apply ordering
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];

            $columns = ['applicant_fname', 'applicant_lname', 'applicant_brgy', 'applicant_gender', 'applicant_bdate'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }
        }

        // Apply pagination
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $query->skip($start)->take($length);

        $data = $query->get()->map(function ($item, $index) use ($start) {
            return [
                ($start + $index + 1), // #
                $item->applicant_fname . ' ' . $item->applicant_lname, // Name
                $item->applicant_brgy, // Barangay
                $item->applicant_gender, // Gender
                $item->applicant_bdate, // Birthday
                $type === 'pending' ?
                    '<button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openApplicationModal(' . $item->application_personnel_id . ', \'pending\')">View Applications</button>' :
                    '<button type="button" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openApplicationModal(' . $item->application_personnel_id . ', \'reviewed\')">View Requirements</button>', // Applications
                $type === 'pending' ?
                    '<div class="dropdown">
                        <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu(' . $item->application_personnel_id . ')">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdown-menu-' . $item->application_personnel_id . '" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEmailModal(' . $item->application_personnel_id . ', ' . $item->application_personnel_id . ', \'' . $item->applicant_fname . ' ' . $item->applicant_lname . '\', \'\')">
                                <i class="fas fa-envelope mr-2"></i>Send Email
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openDeleteModal(' . $item->application_personnel_id . ', \'' . $item->applicant_fname . ' ' . $item->applicant_lname . '\')">
                                <i class="fas fa-trash mr-2"></i>Delete Application
                            </a>
                        </div>
                    </div>' :
                    '<div class="dropdown">
                        <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu(' . $item->application_personnel_id . ')">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdown-menu-' . $item->application_personnel_id . '" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEditInitialScreeningModal(' . $item->application_personnel_id . ', \'' . $item->initial_screening . '\')">
                                <i class="fas fa-edit mr-2"></i>Edit Initial Screening
                            </a>
                        </div>
                    </div>' // Actions
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
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
                // Get new applications since last_id
                $newApplications = DB::table("tbl_application as app")
                    ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
                    ->select(
                        "app.application_id",
                        "a.applicant_fname",
                        "a.applicant_lname",
                        "app.created_at",
                    )
                    ->where("app.application_id", ">", $lastId)
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

    public function submitIntakeSheet(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer|exists:tbl_application_personnel,application_personnel_id',
            'head' => 'required|array',
            'head.fname' => 'required|string|max:255',
            'head.lname' => 'required|string|max:255',
            'head.sex' => 'required|in:Male,Female',
            'head.address' => 'required|string|max:500',
            'head.zone' => 'nullable|string|max:255',
            'head.barangay' => 'nullable|string|max:255',
            'head.dob' => 'nullable|date',
            'head.pob' => 'nullable|string|max:255',
            'head.educ' => 'nullable|string|max:255',
            'head.occ' => 'nullable|string|max:255',
            'head.religion' => 'nullable|string|max:255',
            'head["_4ps"]' => 'nullable|string|max:255',
            'head.ipno' => 'nullable|string|max:255',
            'head.serial' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'family' => 'required|array',
            'family.*.name' => 'required|string|max:255',
            'family.*.relation' => 'required|string|max:255',
            'family.*.birth' => 'nullable|date',
            'family.*.age' => 'nullable|integer|min:0',
            'family.*.sex' => 'required|in:Male,Female',
            'family.*.civil' => 'nullable|string|max:255',
            'family.*.educ' => 'nullable|string|max:255',
            'family.*.occ' => 'nullable|string|max:255',
            'family.*.income' => 'nullable|numeric|min:0',
            'family.*.remarks' => 'nullable|string|max:255',
            'house' => 'required|array',
            'house.total_income' => 'nullable|numeric|min:0',
            'house.net_income' => 'nullable|numeric|min:0',
            'house.other_income' => 'nullable|numeric|min:0',
            'house.house' => 'nullable|string|max:255',
            'house.lot' => 'nullable|string|max:255',
            'house.house_value' => 'nullable|numeric|min:0',
            'house.lot_value' => 'nullable|numeric|min:0',
            'house.house_rent' => 'nullable|numeric|min:0',
            'house.lot_rent' => 'nullable|numeric|min:0',
            'house.water' => 'nullable|numeric|min:0',
            'house.electric' => 'nullable|numeric|min:0',
            'signatures' => 'nullable|array',
            'signatures.client' => 'nullable|string',
        ]);

        try {
            // Check if intake sheet already exists for this application
            $existingSheet = \App\Models\FamilyIntakeSheet::where('application_personnel_id', $request->application_personnel_id)->first();
            if ($existingSheet) {
                return response()->json(['success' => false, 'message' => 'Family intake sheet already exists for this application.'], 422);
            }

            // Get the logged-in mayor staff ID
            $lydoPersonnelId = session('lydopers')->lydopers_id ?? null;

            // Create the intake sheet
            $intakeSheet = \App\Models\FamilyIntakeSheet::create([
                'application_personnel_id' => $request->application_personnel_id,
                'lydo_personnel_id' => $lydoPersonnelId,
                'head_4ps' => $request->head['_4ps'] ?? null,
                'head_ipno' => $request->head['ipno'] ?? null,
                'head_address' => $request->head['address'] ?? null,
                'head_zone' => $request->head['zone'] ?? null,
                'head_barangay' => $request->head['barangay'] ?? null,
                'head_dob' => $request->head['dob'] ?? null,
                'head_pob' => $request->head['pob'] ?? null,
                'head_educ' => $request->head['educ'] ?? null,
                'head_occ' => $request->head['occ'] ?? null,
                'head_religion' => $request->head['religion'] ?? null,
                'serial_number' => $request->head['serial'] ?? null,
                'location' => $request->location ?? null,
                'house_total_income' => $request->house['total_income'] ?? null,
                'house_net_income' => $request->house['net_income'] ?? null,
                'other_income' => $request->house['other_income'] ?? null,
                'house_house' => $request->house['house'] ?? null,
                'house_lot' => $request->house['lot'] ?? null,
                'house_house_value' => $request->house['house_value'] ?? null,
                'house_lot_value' => $request->house['lot_value'] ?? null,
                'house_house_rent' => $request->house['house_rent'] ?? null,
                'house_lot_rent' => $request->house['lot_rent'] ?? null,
                'house_water' => $request->house['water'] ?? null,
                'house_electric' => $request->house['electric'] ?? null,
                'house_remarks' => $request->house['remarks'] ?? null,
                'family_members' => json_encode($request->family),
                'signature_client' => $request->signatures['client'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Family intake sheet submitted successfully!',
                'intakesheet_id' => $intakeSheet->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting family intake sheet: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to submit intake sheet. Please try again.'], 500);
        }
    }

    public function showIntakeSheet(Request $request, $application_personnel_id)
    {
        // Verify the application_personnel_id exists and is approved
        $applicationPersonnel = DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $application_personnel_id)
            ->where('initial_screening', 'Approved')
            ->first();

        if (!$applicationPersonnel) {
            abort(404, 'Intake sheet not found or not approved.');
        }

        // Verify the token
        $token = $request->query('token');
        if (!$token || $token !== $applicationPersonnel->intake_sheet_token) {
            abort(403, 'Invalid or missing token.');
        }

        // Fetch applicant data
        $applicant = DB::table('tbl_application_personnel as ap')
            ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
            ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->where('ap.application_personnel_id', $application_personnel_id)
            ->select(
                'app.applicant_fname',
                'app.applicant_mname',
                'app.applicant_lname',
                'app.applicant_suffix',
                'app.applicant_gender',
                'app.applicant_bdate',
                'app.applicant_brgy'
            )
            ->first();

        if (!$applicant) {
            abort(404, 'Applicant data not found.');
        }

        return view('Applicants.intakesheet', compact('application_personnel_id', 'applicant'));
    }

    public function submitIntakeSheetPublic(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer|exists:tbl_application_personnel,application_personnel_id',
            'token' => 'required|string',
            'head' => 'required|array',
            'head.fname' => 'required|string|max:255',
            'head.lname' => 'required|string|max:255',
            'head.sex' => 'required|in:Male,Female',
            'head.address' => 'required|string|max:500',
            'head.zone' => 'nullable|string|max:255',
            'head.barangay' => 'nullable|string|max:255',
            'head.dob' => 'nullable|date',
            'head.pob' => 'nullable|string|max:255',
            'head.educ' => 'nullable|string|max:255',
            'head.occ' => 'nullable|string|max:255',
            'head.religion' => 'nullable|string|max:255',
            'head["_4ps"]' => 'nullable|string|max:255',
            'head.ipno' => 'nullable|string|max:255',
            'head.serial' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'family' => 'required|array',
            'family.*.name' => 'required|string|max:255',
            'family.*.relation' => 'required|string|max:255',
            'family.*.birth' => 'nullable|date',
            'family.*.age' => 'nullable|integer|min:0',
            'family.*.sex' => 'required|in:Male,Female',
            'family.*.civil' => 'nullable|string|max:255',
            'family.*.educ' => 'nullable|string|max:255',
            'family.*.occ' => 'nullable|string|max:255',
            'family.*.income' => 'nullable|numeric|min:0',
            'family.*.remarks' => 'nullable|string|max:255',
            'house' => 'required|array',
            'house.total_income' => 'nullable|numeric|min:0',
            'house.net_income' => 'nullable|numeric|min:0',
            'house.other_income' => 'nullable|numeric|min:0',
            'house.house' => 'nullable|string|max:255',
            'house.lot' => 'nullable|string|max:255',
            'house.house_value' => 'nullable|numeric|min:0',
            'house.lot_value' => 'nullable|numeric|min:0',
            'house.house_rent' => 'nullable|numeric|min:0',
            'house.lot_rent' => 'nullable|numeric|min:0',
            'house.water' => 'nullable|numeric|min:0',
            'house.electric' => 'nullable|numeric|min:0',
            'signatures' => 'nullable|array',
            'signatures.client' => 'nullable|string',
        ]);

        try {
            // Verify the token
            $applicationPersonnel = DB::table('tbl_application_personnel')
                ->where('application_personnel_id', $request->application_personnel_id)
                ->where('intake_sheet_token', $request->token)
                ->first();

            if (!$applicationPersonnel) {
                return response()->json(['success' => false, 'message' => 'Invalid token or application not found.'], 403);
            }

            // Check if intake sheet already exists for this application
            $existingSheet = \App\Models\FamilyIntakeSheet::where('application_personnel_id', $request->application_personnel_id)->first();
            if ($existingSheet) {
                return response()->json(['success' => false, 'message' => 'Family intake sheet already exists for this application.'], 422);
            }

            // Verify the application is approved
            if ($applicationPersonnel->initial_screening !== 'Approved') {
                return response()->json(['success' => false, 'message' => 'Application not approved for intake sheet submission.'], 403);
            }

            // Create the intake sheet (no lydo_personnel_id for public submission)
            $intakeSheet = \App\Models\FamilyIntakeSheet::create([
                'application_personnel_id' => $request->application_personnel_id,
                'lydo_personnel_id' => null, // Public submission
                'head_4ps' => $request->head['_4ps'] ?? null,
                'head_ipno' => $request->head['ipno'] ?? null,
                'head_address' => $request->head['address'] ?? null,
                'head_zone' => $request->head['zone'] ?? null,
                'head_barangay' => $request->head['barangay'] ?? null,
                'head_dob' => $request->head['dob'] ?? null,
                'head_pob' => $request->head['pob'] ?? null,
                'head_educ' => $request->head['educ'] ?? null,
                'head_occ' => $request->head['occ'] ?? null,
                'head_religion' => $request->head['religion'] ?? null,
                'serial_number' => $request->head['serial'] ?? null,
                'location' => $request->location ?? null,
                'house_total_income' => $request->house['total_income'] ?? null,
                'house_net_income' => $request->house['net_income'] ?? null,
                'other_income' => $request->house['other_income'] ?? null,
                'house_house' => $request->house['house'] ?? null,
                'house_lot' => $request->house['lot'] ?? null,
                'house_house_value' => $request->house['house_value'] ?? null,
                'house_lot_value' => $request->house['lot_value'] ?? null,
                'house_house_rent' => $request->house['house_rent'] ?? null,
                'house_lot_rent' => $request->house['lot_rent'] ?? null,
                'house_water' => $request->house['water'] ?? null,
                'house_electric' => $request->house['electric'] ?? null,
                'house_remarks' => $request->house['remarks'] ?? null,
                'family_members' => json_encode($request->family),
                'signature_client' => $request->signatures['client'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Family intake sheet submitted successfully!',
                'intake_sheet_id' => $intakeSheet->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting family intake sheet: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to submit intake sheet. Please try again.'], 500);
        }
    }

}
