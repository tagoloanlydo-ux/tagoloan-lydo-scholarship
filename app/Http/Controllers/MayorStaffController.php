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

        $tableApplicants = $query->get();

// Sa application() method, hanapin ang $listApplicants query at palitan ng:
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
    ->whereIn("ap.initial_screening", ["Approved", "Rejected"]) // DITO ANG PALIT - tanggalin ang "Pending"
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



// ✅ Update status

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
            'data' => $tableApplicants,
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
            'data' => $listApplications,
        ]);
    }



    public function getApplicationDetails($applicationPersonnelId)
    {
        try {
            $applicationDetails = DB::table('tbl_application_personnel as ap')
                ->join('tbl_application as a', 'ap.application_id', '=', 'a.application_id')
                ->join('tbl_applicant as app', 'a.applicant_id', '=', 'app.applicant_id')
                ->where('ap.application_personnel_id', $applicationPersonnelId)
                ->select(
                    'ap.application_personnel_id',
                    'app.applicant_fname',
                    'app.applicant_mname',
                    'app.applicant_lname',
                    'app.applicant_suffix',
                    'app.applicant_brgy',
                    'app.applicant_school_name',
                    'app.applicant_course',
                    'app.applicant_year_level',
                    'app.applicant_acad_year',
                    'ap.initial_screening',
                    'ap.remarks',
                    'ap.status',
                    'ap.created_at',
                    'ap.updated_at'
                )
                ->first();

            if (!$applicationDetails) {
                return response('<div class="text-center text-red-500"><i class="bx bx-error text-4xl mb-2"></i><p>Application not found</p></div>', 404);
            }

            $fullName = trim($applicationDetails->applicant_fname . ' ' .
                           ($applicationDetails->applicant_mname ? $applicationDetails->applicant_mname . ' ' : '') .
                           $applicationDetails->applicant_lname .
                           ($applicationDetails->applicant_suffix ? ', ' . $applicationDetails->applicant_suffix : ''));

            $html = '
                <div class="space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-2"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Full Name</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($fullName) . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Barangay</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($applicationDetails->applicant_brgy) . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">School</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($applicationDetails->applicant_school_name) . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Course</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($applicationDetails->applicant_course) . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Year Level</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($applicationDetails->applicant_year_level) . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Academic Year</label>
                                <p class="text-sm text-gray-900 mt-1">' . htmlspecialchars($applicationDetails->applicant_acad_year) . '</p>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                                                       Application Status
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Initial Screening</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' .
                                    ($applicationDetails->initial_screening == 'Reviewed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') . ' mt-1">
                                    ' . htmlspecialchars($applicationDetails->initial_screening) . '
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Remarks</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' .
                                    ($applicationDetails->remarks == 'Ultra Poor' ? 'bg-red-100 text-red-800' :
                                     ($applicationDetails->remarks == 'Poor' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) . ' mt-1">
                                    ' . htmlspecialchars($applicationDetails->remarks) . '
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Status</label>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' .
                                    ($applicationDetails->status == 'Approved' ? 'bg-green-100 text-green-800' :
                                     ($applicationDetails->status == 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) . ' mt-1">
                                    ' . htmlspecialchars($applicationDetails->status) . '
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-clock text-purple-600 mr-2"></i>
                            Application Timeline
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Created At</label>
                                <p class="text-sm text-gray-900 mt-1">' . \Carbon\Carbon::parse($applicationDetails->created_at)->format('M d, Y h:i A') . '</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                                <p class="text-sm text-gray-900 mt-1">' . \Carbon\Carbon::parse($applicationDetails->updated_at)->format('M d, Y h:i A') . '</p>
                            </div>
                        </div>
                    </div>
                </div>
            ';

            return response($html);
        } catch (\Exception $e) {
            Log::error('Error fetching application details: ' . $e->getMessage());
            return response('<div class="text-center text-red-500"><i class="bx bx-error text-4xl mb-2"></i><p>Error loading application details</p></div>', 500);
        }
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
            'email_content' => "Document update email sent for documents: " . implode(', ', $documentNames),
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

        return view('intake_sheet.form', compact('applicant'));
    }

    public function submitIntakeSheetPublic(Request $request)
    {
        $request->validate([
            'application_personnel_id' => 'required|integer',
            'token' => 'required|string',
            'head' => 'required|array',
            'family' => 'required|array',
            'house' => 'required|array',
            'signatures' => 'array',
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

        // Handle signature storage
        $signaturePaths = [];
        if (isset($request->signatures) && is_array($request->signatures)) {
            foreach ($request->signatures as $key => $signatureData) {
                if ($signatureData && strpos($signatureData, 'data:image') === 0) {
                    // Decode base64 image
                    $imageData = explode(',', $signatureData);
                    $image = base64_decode($imageData[1]);

                    // Generate filename
                    $filename = 'signature_' . $request->application_personnel_id . '_' . $key . '_' . time() . '.png';

                    // Store in storage/app/public/signature so files are accessible via /storage/signature/...
                    Storage::put('public/signature/' . $filename, $image);
                    $signaturePaths[$key] = 'signature/' . $filename;
                }
            }
        }

        // Prepare data for insertion
        $data = [
            'application_personnel_id' => $request->application_personnel_id,
            'head_4ps' => $request->head['_4ps'] ?? null,
            'head_ipno' => $request->head['ipno'] ?? null,
            'head_address' => $request->head['address'] ?? null,
            'head_zone' => $request->head['zone'] ?? null,
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
            'house_value' => $request->house['house_value'] ?? null,
            'house_lot' => $request->house['lot'] ?? null,
            'lot_value' => $request->house['lot_value'] ?? null,
            'house_rent' => $request->house['house_rent'] ?? null,
            'lot_rent' => $request->house['lot_rent'] ?? null,
            'house_water' => $request->house['water'] ?? null,
            'house_electric' => $request->house['electric'] ?? null,
            'family_members' => json_encode($request->family),
            'signature_client' => $signaturePaths['client'] ?? null,
            'signature_worker' => $signaturePaths['worker'] ?? null,
            'signature_officer' => $signaturePaths['officer'] ?? null,
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

    public function getIntakeSheet($applicationPersonnelId)
    {
        try {
            \Log::info("Fetching intake sheet for application_personnel_id: {$applicationPersonnelId}");

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
                    'app.applicant_brgy'
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

