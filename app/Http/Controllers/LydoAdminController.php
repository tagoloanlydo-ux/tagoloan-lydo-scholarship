<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Announce;
use App\Models\Disburse;
use App\Models\Settings;
class LydoAdminController extends Controller
{
    public function getFilteredApplicantsWithRemarks(Request $request)
{
    $query = DB::table('tbl_application_personnel')
        ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
        ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
        ->select('tbl_applicant.*', 'tbl_application_personnel.remarks')
        ->where('tbl_application_personnel.initial_screening', 'Approved');

    // Apply search filter
    if ($request->has('search') && !empty($request->search)) {
        $query->where(function($q) use ($request) {
            $q->where('applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
        });
    }

    // Apply barangay filter
    if ($request->has('barangay') && !empty($request->barangay)) {
        $query->where('applicant_brgy', $request->barangay);
    }

    // Apply academic year filter
    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $query->where('applicant_acad_year', $request->academic_year);
    }

    // Apply remarks filter
    if ($request->has('remarks') && !empty($request->remarks)) {
        $query->where('tbl_application_personnel.remarks', $request->remarks);
    }

    // Apply status filter
    if ($request->has('status') && !empty($request->status)) {
        $query->where('tbl_application_personnel.status', $request->status);
    }

    $applicants = $query->get();

    // Format the data for JSON response
    $formattedApplicants = $applicants->map(function ($applicant) {
        return [
            'applicant_fname' => $applicant->applicant_fname,
            'applicant_lname' => $applicant->applicant_lname,
            'applicant_email' => $applicant->applicant_email,
            'applicant_contact_number' => $applicant->applicant_contact_number,
            'applicant_brgy' => $applicant->applicant_brgy,
            'remarks' => $applicant->remarks,
        ];
    });

    return response()->json([
        'success' => true,
        'applicants' => $formattedApplicants
    ]);
}
    public function report(Request $request)
    {
        // Notifications
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // Total applicants count
        $totalApplicants = DB::table('tbl_applicant')->count();

        // Approved initial screening count
        $approvedInitialCount = DB::table('tbl_application_personnel')
            ->where('initial_screening', 'Approved')
            ->count();

        // Rejected applicants count
        $rejectedApplicantsCount = DB::table('tbl_application_personnel')
            ->where('status', 'Rejected')
            ->count();

        // Inactive scholars count
        $inactiveScholarsCount = DB::table('tbl_scholar')
            ->where('scholar_status', 'inactive')
            ->count();

        // Active scholars count
        $activeScholarsCount = DB::table('tbl_scholar')
            ->where('scholar_status', 'active')
            ->count();

        // Line graph data for approved vs rejected applications trend by academic year
        $years = DB::table('tbl_applicant')
            ->select('applicant_acad_year')
            ->distinct()
            ->orderBy('applicant_acad_year')
            ->pluck('applicant_acad_year')
            ->toArray();

        // If no years data, use current academic year as default
        if (empty($years)) {
            $currentYear = date('Y');
            $years = [$currentYear . '-' . ($currentYear + 1)];
        }

        $approvedApplicationsTrend = [];
        $rejectedApplicationsTrend = [];
        $approvalRateTrend = [];
        $activeScholarsTrend = [];
        $inactiveScholarsTrend = [];

        foreach ($years as $year) {
            $approvedApplications = DB::table('tbl_application_personnel')
                ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
                ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                ->where('tbl_applicant.applicant_acad_year', $year)
                ->where('tbl_application_personnel.status', 'Approved')
                ->count();

            $rejectedApplications = DB::table('tbl_application_personnel')
                ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
                ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                ->where('tbl_applicant.applicant_acad_year', $year)
                ->where('tbl_application_personnel.status', 'Rejected')
                ->count();

            $approvedApplicationsTrend[] = $approvedApplications;
            $rejectedApplicationsTrend[] = $rejectedApplications;

            // Calculate approval rate percentage
            $totalApplications = $approvedApplications + $rejectedApplications;
            $approvalRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 2) : 0;
            $approvalRateTrend[] = $approvalRate;

            // Calculate active and inactive scholars for each year (keeping this for the other chart)
            $activeScholars = DB::table('tbl_scholar')
                ->whereYear('date_activated', $year)
                ->where('scholar_status', 'active')
                ->count();
            $activeScholarsTrend[] = $activeScholars;

            $inactiveScholars = DB::table('tbl_scholar')
                ->whereYear('date_activated', $year)
                ->where('scholar_status', 'inactive')
                ->count();
            $inactiveScholarsTrend[] = $inactiveScholars;
        }

        // Check if this is an AJAX request for filtering
   if ($request->ajax()) {
    if ($request->input('tab') === 'applicants') {
        return $this->getFilteredApplicantsWithRemarks($request);
    } else {
        return $this->getFilteredScholars($request);
    }
}

        // Fetch scholars for the second tab
        $query = DB::table('tbl_scholar')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_scholar.*', 'tbl_applicant.*');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('scholar_status', $request->status);
        }

        $scholars = $query->paginate(15);

        // Get distinct barangays for filter dropdown
        $barangays = DB::table('tbl_applicant')
            ->select('applicant_brgy')
            ->distinct()
            ->orderBy('applicant_brgy', 'asc')
            ->pluck('applicant_brgy');

        // Get distinct statuses for filter dropdown
        $statuses = DB::table('tbl_scholar')
            ->select('scholar_status')
            ->distinct()
            ->orderBy('scholar_status', 'asc')
            ->pluck('scholar_status');

        // Get distinct academic years for filter dropdown
        $academicYears = DB::table('tbl_applicant')
            ->select('applicant_acad_year')
            ->distinct()
            ->orderBy('applicant_acad_year', 'desc')
            ->pluck('applicant_acad_year');

        // Get distinct remarks for filter dropdown with badge and icon mappings
        $remarksData = DB::table('tbl_application_personnel')
            ->select('remarks')
            ->whereNotNull('remarks')
            ->where('remarks', '!=', '')
            ->distinct()
            ->orderBy('remarks', 'asc')
            ->get();

        // Create a mapping of remarks to their display properties
        $remarks = $remarksData->map(function ($remark) {
            // Ensure remarks is a string and not null
            $remarksValue = is_string($remark->remarks) ? $remark->remarks : (string) $remark->remarks;

            // Simple mapping based on remark content
            $badgeClass = 'bg-gray-100 text-gray-800';
            $iconClass = 'fas fa-question-circle';

            if (stripos($remarksValue, 'approved') !== false || stripos($remarksValue, 'passed') !== false) {
                $badgeClass = 'bg-green-100 text-green-800';
                $iconClass = 'fas fa-check-circle';
            } elseif (stripos($remarksValue, 'rejected') !== false || stripos($remarksValue, 'failed') !== false) {
                $badgeClass = 'bg-red-100 text-red-800';
                $iconClass = 'fas fa-times-circle';
            } elseif (stripos($remarksValue, 'pending') !== false || stripos($remarksValue, 'review') !== false) {
                $badgeClass = 'bg-yellow-100 text-yellow-800';
                $iconClass = 'fas fa-clock';
            }

            return (object) [
                'remarks' => $remarksValue,
                'badge_class' => $badgeClass,
                'icon_class' => $iconClass
            ];
        });

        // Fetch applicants with remarks for the third tab (only reviewed initial screening)
        $applicantsQuery = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_applicant.*', 'tbl_application_personnel.remarks')
            ->where('tbl_application_personnel.initial_screening', 'Approved');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $applicantsQuery->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $applicantsQuery->where('applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $applicantsQuery->where('applicant_acad_year', $request->academic_year);
        }

        // Apply remarks filter
        if ($request->has('remarks') && !empty($request->remarks)) {
            $applicantsQuery->where('tbl_application_personnel.remarks', $request->remarks);
        }

        $applicantsWithRemarks = $applicantsQuery->paginate(15);

        // Fix remarks field - ensure it's cast to string to prevent stdClass errors
        $applicantsWithRemarks->getCollection()->transform(function ($applicant) {
            if (isset($applicant->remarks)) {
                // Handle different data types that might be stored in remarks
                if (is_object($applicant->remarks)) {
                    // If it's an object/stdClass, convert to string representation
                    $applicant->remarks = json_encode($applicant->remarks);
                } elseif (is_array($applicant->remarks)) {
                    // If it's an array, convert to JSON string
                    $applicant->remarks = json_encode($applicant->remarks);
                } elseif (!is_string($applicant->remarks)) {
                    // If it's any other non-string type, cast to string
                    $applicant->remarks = (string) $applicant->remarks;
                }
                // If it's null or empty, set to empty string
                if (empty($applicant->remarks) || $applicant->remarks === 'null') {
                    $applicant->remarks = '';
                }
            } else {
                $applicant->remarks = '';
            }
            return $applicant;
        });


        // Fetch school demographic data for the fourth tab
        $schoolDemographics = DB::table('tbl_scholar')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_applicant.applicant_school_name', DB::raw('count(*) as total'))
            ->groupBy('tbl_applicant.applicant_school_name')
            ->get();

        // Fetch barangay demographic data for the fifth tab
        $barangayDemographics = DB::table('tbl_applicant')
            ->select('applicant_brgy', DB::raw('count(*) as total'))
            ->groupBy('applicant_brgy')
            ->get();

        // Fetch renewal applications for the new tab (Approved and Rejected only)
        $renewalQuery = DB::table('tbl_renewal')
            ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_renewal.renewal_id',
                'tbl_renewal.renewal_status',
                'tbl_renewal.date_submitted',
                'tbl_renewal.renewal_semester',
                'tbl_renewal.renewal_acad_year',
                'tbl_applicant.applicant_fname',
                'tbl_applicant.applicant_mname',
                'tbl_applicant.applicant_lname',
                'tbl_applicant.applicant_suffix',
                'tbl_applicant.applicant_school_name',
                'tbl_applicant.applicant_brgy',
                'tbl_applicant.applicant_course',
                'tbl_applicant.applicant_year_level'
            )
            ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected']);

        // Apply filters for initial load
        if ($request->has('search') && !empty($request->search)) {
            $renewalQuery->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $renewalQuery->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $renewalQuery->where('renewal_acad_year', $request->academic_year);
        }

        if ($request->has('status') && !empty($request->status) && in_array($request->status, ['Approved', 'Rejected'])) {
            $renewalQuery->where('renewal_status', $request->status);
        }

        $approvedRenewals = $renewalQuery->get();

        return view('lydo_admin.report', compact(
            'totalApplicants',
            'approvedInitialCount',
            'rejectedApplicantsCount',
            'inactiveScholarsCount',
            'activeScholarsCount',
            'approvedApplicationsTrend',
            'rejectedApplicationsTrend',
            'approvalRateTrend',
            'activeScholarsTrend',
            'inactiveScholarsTrend',
            'scholars',
            'applicantsWithRemarks',
            'schoolDemographics',
            'barangayDemographics',
            'approvedRenewals',
            'notifications',
            'years',
            'barangays',
            'statuses',
            'academicYears',
            'remarks'
        ));
    }

    private function getFilteredScholars(Request $request)
    {
        $query = DB::table('tbl_scholar')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_scholar.*', 'tbl_applicant.*');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        $scholars = $query->get();

        // Format the data for JSON response
        $formattedScholars = $scholars->map(function ($scholar) {
            return [
                'applicant_fname' => $scholar->applicant_fname,
                'applicant_lname' => $scholar->applicant_lname,
                'applicant_school_name' => $scholar->applicant_school_name,
                'applicant_course' => $scholar->applicant_course,
                'applicant_year_level' => $scholar->applicant_year_level,
                'applicant_brgy' => $scholar->applicant_brgy,
                'scholar_status' => $scholar->scholar_status,
            ];
        });

        return response()->json([
            'success' => true,
            'scholars' => $formattedScholars
        ]);
    }
    public function announcement()
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])

            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $announcements = Announce::orderBy('date_posted', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lydo_admin.announcement', compact('notifications', 'announcements'));
    }
    public function updateAnnouncement(Request $request, $id)
    {
        $request->validate([
            'announce_title' => 'required|string|max:255',
            'announce_type' => 'required|string',
            'announce_content' => 'required|string',
        ]);

        $announcement = \App\Models\Announce::findOrFail($id);
        $announcement->announce_title = $request->announce_title;
        $announcement->announce_type = $request->announce_type;
        $announcement->announce_content = $request->announce_content;
        $announcement->save();

        return redirect()->back()->with('success', 'Announcement updated successfully!');
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|string|in:applicants,scholars',
        ]);

        Announce::create([
            'lydopers_id' => $request->session()->get('lydopers')->lydopers_id,
            'announce_title' => $request->announce_title,
            'announce_content' => $request->announce_content,
            'announce_type' => $request->announce_type,
            'date_posted' => now(),
        ]);

        return redirect()->back()->with('success', 'Announcement created successfully!');
    }

    public function deleteAnnouncement($announce_id)
    {
        \App\Models\Announce::where('announce_id', $announce_id)->delete();
        return redirect()->back()->with('success', 'Announcement deleted successfully!');
    }
    public function index(Request $request)
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get(); 

        // Get current academic year (use the most recent academic year from applicants)
        $currentAcademicYear = DB::table('tbl_applicant')
            ->select('applicant_acad_year')
            ->orderBy('applicant_acad_year', 'desc')
            ->value('applicant_acad_year');

        // If no academic year found, use current year as fallback
        if (!$currentAcademicYear) {
            $currentAcademicYear = date('Y') . '-' . (date('Y') + 1);
        }

        // Get counts for dashboard cards
        $totalApplicants = DB::table('tbl_applicant')
            ->where('applicant_acad_year', $currentAcademicYear)
            ->count();

        $totalScholarsWholeYear = DB::table('tbl_scholar')
            ->where('scholar_status', 'active')
            ->count();

        $inactiveScholars = DB::table('tbl_scholar')
            ->where('scholar_status', 'inactive')
            ->count();

        // Get barangay distribution data
        $barangayDistribution = DB::table('tbl_applicant')
            ->select('applicant_brgy', DB::raw('COUNT(*) as count'))
            ->where('applicant_acad_year', $currentAcademicYear)
            ->groupBy('applicant_brgy')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get school distribution data
        $schoolDistribution = DB::table('tbl_applicant')
            ->select('applicant_school_name', DB::raw('COUNT(*) as count'))
            ->where('applicant_acad_year', $currentAcademicYear)
            ->groupBy('applicant_school_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('lydo_admin.dashboard', compact(
            'notifications',
            'totalApplicants',
            'totalScholarsWholeYear',
            'inactiveScholars',
            'currentAcademicYear',
            'barangayDistribution',
            'schoolDistribution'
        ));
    }
    public function lydo()
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get(); 

        $inactiveStaff = DB::table('tbl_lydopers')
            ->where('lydopers_role', 'lydo_staff')
            ->where('lydopers_status', 'inactive')
            ->paginate(15, ['*'], 'inactive_page');

        $activeStaff = DB::table('tbl_lydopers')
            ->where('lydopers_role', 'lydo_staff')
            ->where('lydopers_status', 'active')
            ->paginate(15, ['*'], 'active_page');

        return view('lydo_admin.lydo', compact('notifications', 'inactiveStaff', 'activeStaff'));
    }

   
    public function toggleStatus($id)
    {
        $staff = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();

        if ($staff) {
            $newStatus = $staff->lydopers_status === 'active' ? 'inactive' : 'active';

            DB::table('tbl_lydopers')
                ->where('lydopers_id', $id)
                ->update([
                    'lydopers_status' => $newStatus,
                    'updated_at' => now()
                ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function mayor()
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get(); 

        $inactiveStaff = DB::table('tbl_lydopers')
            ->where('lydopers_role', 'mayor_staff')
            ->where('lydopers_status', 'inactive')
            ->paginate(15, ['*'], 'inactive_page');

        $activeStaff = DB::table('tbl_lydopers')
            ->where('lydopers_role', 'mayor_staff')
            ->where('lydopers_status', 'active')
            ->paginate(15, ['*'], 'active_page');

        return view('lydo_admin.mayor', compact('notifications', 'inactiveStaff', 'activeStaff'));
    }

    public function scholar(Request $request)
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // Get scholars with applicant information - only active status
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
            )
            ->where('s.scholar_status', 'active');

        // Apply filters
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

        $scholars = $query->paginate(30);

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

        return view('lydo_admin.scholar', compact('notifications', 'scholars', 'barangays', 'academicYears'));
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'email_type' => 'required|string|in:account_creation,plain',
            'scholar_id' => 'nullable|string' // For account creation type
        ]);

        $emails = explode(',', $request->email);
        $emails = array_map('trim', $emails); // Remove whitespace
        $subject = $request->subject;
        $body = $request->message;
        $emailType = $request->email_type;

        try {
            if ($emailType === 'account_creation') {
                $scholarId = $request->input('scholar_id');
                Mail::send('emails.account-creation', ['scholar_id' => $scholarId], function ($message) use ($emails, $subject) {
                    $message->to($emails)
                            ->subject($subject);
                });
            } else {
                Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $body], function ($message) use ($emails, $subject) {
                    $message->to($emails)
                            ->subject($subject);
                });
            }

            return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . count($emails) . ' recipient(s)']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    public function sendEmailToApplicants(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*.id' => 'required|string',
            'recipients.*.name' => 'required|string',
            'recipients.*.email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $recipients = $request->recipients;
        $subject = $request->subject;
        $message = $request->message;

        try {
            foreach ($recipients as $recipient) {
                Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $message], function ($mail) use ($recipient, $subject) {
                    $mail->to($recipient['email'])
                         ->subject($subject);
                });
            }

            return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . count($recipients) . ' applicant(s)!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    public function status()
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])

            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch active scholars without renewal applications
        $scholarsWithoutRenewal = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->leftJoin('tbl_renewal as r', 's.scholar_id', '=', 'r.scholar_id')
->select(
    's.scholar_id',
    's.scholar_status',
    'a.applicant_fname',
    'a.applicant_mname',
    'a.applicant_lname',
    'a.applicant_suffix',
    'a.applicant_email',
    'a.applicant_contact_number',
    'a.applicant_school_name',
    'a.applicant_course',
    'a.applicant_year_level',
      'a.applicant_brgy',
    DB::raw("CONCAT(a.applicant_fname, ' ', a.applicant_lname) as full_name")
)
            ->where('s.scholar_status', 'active')
        ->whereNull('r.renewal_id')
        ->paginate(15);

        // Get distinct barangays for filter dropdown
        $barangays = DB::table('tbl_applicant')
            ->select('applicant_brgy')
            ->distinct()
            ->orderBy('applicant_brgy', 'asc')
            ->pluck('applicant_brgy');

    return view('lydo_admin.status', compact('notifications', 'scholarsWithoutRenewal', 'barangays'));
    }

    public function updateScholarStatus(Request $request)
    {
        $request->validate([
            'selected_scholars' => 'required|array',
            'selected_scholars.*' => 'exists:tbl_scholar,scholar_id'
        ]);

        DB::table('tbl_scholar')
            ->whereIn('scholar_id', $request->selected_scholars)
            ->update([
                'scholar_status' => 'inactive',
                'updated_at' => now()
            ]);

        return redirect()->back()->with('success', 'Scholar status updated successfully!');
    }

public function disbursement(Request $request)
{
    // Check if this is an AJAX request for filtering
    if ($request->ajax()) {
        // Get disbursement records with applicant information
        $query = DB::table('tbl_disburse as d')
            ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                'd.disburse_semester',
                'd.disburse_acad_year',
                'd.disburse_amount',
                'd.disburse_date',
                'a.applicant_brgy',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            );

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        // Apply semester filter
        if ($request->has('semester') && !empty($request->semester)) {
            $query->where('d.disburse_semester', $request->semester);
        }

        $disbursements = $query->get();

        // Format the data for JSON response
        $formattedDisbursements = $disbursements->map(function ($disburse) {
            return [
                'full_name' => $disburse->full_name,
                'applicant_brgy' => $disburse->applicant_brgy,
                'disburse_semester' => $disburse->disburse_semester,
                'disburse_acad_year' => $disburse->disburse_acad_year,
                'disburse_amount' => $disburse->disburse_amount,
                'disburse_date' => \Carbon\Carbon::parse($disburse->disburse_date)->format('F d, Y'),
            ];
        });

        return response()->json($formattedDisbursements);
    }

    $notifications = DB::table('tbl_application_personnel')
        ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
        ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
        ->select(
            'tbl_applicant.applicant_fname as name',
            'tbl_application_personnel.status as status',
            'tbl_application_personnel.updated_at as created_at',
            DB::raw("'application' as type")
        )
        ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])

        ->unionAll(
            DB::table('tbl_renewal')
                ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                ->select(
                    'tbl_applicant.applicant_fname as name',
                    'tbl_renewal.renewal_status as status',
                    'tbl_renewal.updated_at as created_at',
                    DB::raw("'renewal' as type")
                )
                ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
        )
        ->orderBy('created_at', 'desc')
        ->get();

    // Get scholars for the create form dropdown - FIXED QUERY
    $scholars = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            's.scholar_id',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy', // ADD THIS LINE
            DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
        )
        ->where('s.scholar_status', 'active')
        ->get();

    // Get disbursement records with applicant information
    $query = DB::table('tbl_disburse as d')
        ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            'd.disburse_id',
            'd.disburse_semester',
            'd.disburse_acad_year',
            'd.disburse_amount',
            'd.disburse_date',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
        );

    // Apply search filter
    if ($request->has('search') && !empty($request->search)) {
        $query->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
        });
    }

    // Apply barangay filter
    if ($request->has('barangay') && !empty($request->barangay)) {
        $query->where('a.applicant_brgy', $request->barangay);
    }

    // Apply academic year filter
    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $query->where('d.disburse_acad_year', $request->academic_year);
    }

    // Apply semester filter
    if ($request->has('semester') && !empty($request->semester)) {
        $query->where('d.disburse_semester', $request->semester);
    }

    $disbursements = $query->paginate(15);

    // Get distinct barangays for filter dropdown
    $barangays = DB::table('tbl_applicant')
        ->select('applicant_brgy')
        ->distinct()
        ->orderBy('applicant_brgy', 'asc')
        ->pluck('applicant_brgy');

    // Get distinct academic years for filter dropdown
    $academicYears = DB::table('tbl_disburse')
        ->select('disburse_acad_year')
        ->distinct()
        ->orderBy('disburse_acad_year', 'desc')
        ->pluck('disburse_acad_year');

    // Get distinct semesters for filter dropdown
    $semesters = DB::table('tbl_disburse')
        ->select('disburse_semester')
        ->distinct()
        ->orderBy('disburse_semester', 'asc')
        ->pluck('disburse_semester');

    // Get signed disbursements
    $signedDisbursements = DB::table('tbl_disburse as d')
        ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            'd.disburse_id',
            'd.disburse_semester',
            'd.disburse_acad_year',
            'd.disburse_amount',
            'd.disburse_date',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
        )
        ->whereNotNull('d.disburse_signature')
        ->paginate(15);

    return view('lydo_admin.disbursement', compact('notifications', 'disbursements', 'barangays', 'academicYears', 'semesters', 'scholars', 'signedDisbursements'));
}
    public function settings()
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])

            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        $settings = Settings::first() ?? new Settings();

        return view('lydo_admin.settings', compact('notifications', 'settings'));
    }

    public function updateDeadlines(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'application_start_date' => 'nullable|date',
                'application_deadline' => 'nullable|date',
                'renewal_start_date' => 'nullable|date',
                'renewal_deadline' => 'nullable|date',
                'renewal_semester' => 'nullable|string|in:1st Semester,2nd Semester,Summer',
            ]);

            // Custom validation for date relationships
            $validator->after(function ($validator) {
                $data = $validator->getData();

                // Check application dates
                if (!empty($data['application_start_date']) && !empty($data['application_deadline'])) {
                    if (strtotime($data['application_start_date']) >= strtotime($data['application_deadline'])) {
                        $validator->errors()->add('application_start_date', 'Application start date must be before the deadline.');
                    }
                }

                // Check renewal dates
                if (!empty($data['renewal_start_date']) && !empty($data['renewal_deadline'])) {
                    if (strtotime($data['renewal_start_date']) >= strtotime($data['renewal_deadline'])) {
                        $validator->errors()->add('renewal_start_date', 'Renewal start date must be before the deadline.');
                    }
                }
            });

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $settings = Settings::first();
            if (!$settings) {
                $settings = new Settings();
            }

            $settings->fill($request->only([
                'application_start_date',
                'application_deadline',
                'renewal_start_date',
                'renewal_deadline',
                'renewal_semester',
            ]));
            $settings->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deadlines updated successfully.'
                ]);
            }

            return redirect()->route('LydoAdmin.settings')->with('success', 'Deadlines updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating deadlines.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while updating deadlines.');
        }
    }

    public function applicants(Request $request)
    {
        $notifications = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])

            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // Get applicants with filtering - only show those with Approved initial screening
        $query = DB::table('tbl_applicant')
            ->join('tbl_application', 'tbl_applicant.applicant_id', '=', 'tbl_application.applicant_id')
            ->join('tbl_application_personnel', 'tbl_application.application_id', '=', 'tbl_application_personnel.application_id')
            ->where('tbl_application_personnel.initial_screening', 'Approved');

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('tbl_application_personnel.status', $request->status);
        }

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        $applicants = $query->paginate(15);

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

        return view('lydo_admin.applicants', compact('notifications', 'applicants', 'barangays', 'academicYears'));
    }

    public function getAllFilteredApplicants(Request $request)
    {
        $query = DB::table('tbl_applicant')
            ->join('tbl_application', 'tbl_applicant.applicant_id', '=', 'tbl_application.applicant_id')
            ->join('tbl_application_personnel', 'tbl_application.application_id', '=', 'tbl_application_personnel.application_id')
            ->where('tbl_application_personnel.initial_screening', 'Approved');

        // Apply the same filters as the main applicants method
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        // Get all applicant IDs that match the filters
        $applicantIds = $query->pluck('applicant_id');

        return response()->json(['applicant_ids' => $applicantIds]);
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

    public function getScholarNames(Request $request)
    {
        $request->validate([
            'scholar_ids' => 'required|array',
            'scholar_ids.*' => 'exists:tbl_scholar,scholar_id'
        ]);

        $scholarIds = $request->scholar_ids;

        $scholars = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                's.scholar_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                'a.applicant_brgy'
            )
            ->whereIn('s.scholar_id', $scholarIds)
            ->get();

        return response()->json([
            'success' => true,
            'scholars' => $scholars
        ]);
    }

    public function updatePersonalInfo(Request $request, $id)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'lydopers_fname' => 'required|string|max:50',
                'lydopers_lname' => 'required|string|max:50',
                'lydopers_email' => 'required|email|max:100',
                'lydopers_address' => 'nullable|string|max:255',
                'lydopers_contact_number' => 'required|regex:/^09\d{9}$/',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            DB::table('tbl_lydopers')
                ->where('lydopers_id', $id)
                ->update([
                    'lydopers_fname' => $request->lydopers_fname,
                    'lydopers_mname' => $request->lydopers_mname,
                    'lydopers_lname' => $request->lydopers_lname,
                    'lydopers_suffix' => $request->lydopers_suffix,
                    'lydopers_email' => $request->lydopers_email,
                    'lydopers_address' => $request->lydopers_address,
                    'lydopers_contact_number' => $request->lydopers_contact_number,
                    'updated_at' => now(),
                ]);

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Personal information updated successfully.',
                    'updated_data' => [
                        'lydopers_fname' => $request->lydopers_fname,
                        'lydopers_mname' => $request->lydopers_mname,
                        'lydopers_lname' => $request->lydopers_lname,
                        'lydopers_suffix' => $request->lydopers_suffix,
                        'lydopers_email' => $request->lydopers_email,
                        'lydopers_address' => $request->lydopers_address,
                        'lydopers_contact_number' => $request->lydopers_contact_number,
                    ]
                ]);
            }

            return redirect()->route('LydoAdmin.settings')->with('success', 'Personal information updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating personal information.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while updating personal information.');
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $staff = DB::table('tbl_lydopers')
                ->where('lydopers_id', $request->session()->get('lydopers')->lydopers_id)
                ->first();

            if (!Hash::check($request->current_password, $staff->lydopers_pass)) {
                // Check if request is AJAX
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect.'
                    ]);
                }
                return back()->with('error', 'Current password is incorrect.');
            }

            DB::table('tbl_lydopers')
                ->where('lydopers_id', $staff->lydopers_id)
                ->update([
                    'lydopers_pass' => Hash::make($request->new_password),
                    'updated_at' => now(),
                ]);

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully.'
                ]);
            }

            return redirect()->route('LydoAdmin.settings')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating password.'
                ], 500);
            }
            return back()->with('error', 'An error occurred while updating password.');
        }
    }

    public function createDisbursement(Request $request)
    {
        // Handle both array format (from disbursement.blade.php) and string format (from scholar.blade.php modal)
        if (is_array($request->scholar_ids)) {
            $request->validate([
                'scholar_ids' => 'required|array',
                'scholar_ids.*' => 'exists:tbl_scholar,scholar_id',
                'amount' => 'required|numeric|min:0',
                'disbursement_date' => 'required|date',
                'semester' => 'required|string|in:1st Semester,2nd Semester,Summer',
                'academic_year' => 'required|string',
            ]);
            $scholarIds = $request->scholar_ids;
        } else {
            $request->validate([
                'scholar_ids' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'disbursement_date' => 'required|date',
                'semester' => 'required|string|in:1st Semester,2nd Semester,Summer',
                'academic_year' => 'required|string',
            ]);
            $scholarIds = explode(',', $request->scholar_ids);
        }

        $lydopersId = session('lydopers')->lydopers_id;
        $academicYear = $request->academic_year;
        $semester = $request->semester;
        $createdCount = 0;
        $skippedScholars = [];
        $skippedNames = [];

        try {
            foreach ($scholarIds as $scholarId) {
                $cleanScholarId = trim($scholarId);

                // Check for existing disbursement for this scholar, year, and semester
                $existing = Disburse::where('scholar_id', $cleanScholarId)
                    ->where('disburse_acad_year', $academicYear)
                    ->where('disburse_semester', $semester)
                    ->exists();

                if ($existing) {
                    // Get scholar name for message
                    $scholarName = DB::table('tbl_scholar as s')
                        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                        ->where('s.scholar_id', $cleanScholarId)
                        ->value(DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname)"));

                    $skippedScholars[] = $cleanScholarId;
                    $skippedNames[] = $scholarName ?: $cleanScholarId;
                } else {
                    Disburse::create([
                        'scholar_id' => $cleanScholarId,
                        'lydopers_id' => $lydopersId,
                        'disburse_semester' => $semester,
                        'disburse_acad_year' => $academicYear,
                        'disburse_amount' => $request->amount,
                        'disburse_date' => $request->disbursement_date,
                    ]);
                    $createdCount++;
                }
            }

            if ($createdCount > 0) {
                $message = "Disbursement created successfully for {$createdCount} scholar(s).";
                if (!empty($skippedNames)) {
                    $skippedList = implode(', ', array_slice($skippedNames, 0, 3));
                    if (count($skippedNames) > 3) {
                        $skippedList .= ' and others';
                    }
                    $message .= " Skipped duplicates for: {$skippedList} (same year and semester already exists).";
                }
                return redirect()->back()->with('success', $message);
            } else {
                $skippedList = implode(', ', array_slice($skippedNames, 0, 3));
                if (count($skippedNames) > 3) {
                    $skippedList .= ' and others';
                }
                return redirect()->back()->with('error', "No new disbursements created. Duplicates already exist for: {$skippedList} (same year and semester).");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create disbursement: ' . $e->getMessage());
        }
    }

    public function generateScholarsPdf(Request $request)
    {
        $query = DB::table('tbl_scholar')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_scholar.*', 'tbl_applicant.*');

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('scholar_status', $request->status);
        }

        $scholars = $query->get();

        // Get filter info for page title
        $filters = [];
        if ($request->search) {
            $filters[] = 'Search: ' . $request->search;
        }
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }
        if ($request->barangay) {
            $filters[] = 'Barangay: ' . $request->barangay;
        }
        if ($request->status) {
            $filters[] = 'Status: ' . ucfirst($request->status);
        }

        $pdf = Pdf::loadView('pdf.scholars-report', compact('scholars', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('scholars-report-' . date('Y-m-d') . '.pdf');
    }

    public function generateScholarsPdfByBarangay(Request $request)
    {
        $query = DB::table('tbl_scholar')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_scholar.*', 'tbl_applicant.*');

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        $scholars = $query->get();

        // Group scholars by barangay
        $scholarsByBarangay = $scholars->groupBy('applicant_brgy');

        $academicYear = $request->academic_year;

        // Generate separate PDFs
        $pdfs = [];
        foreach ($scholarsByBarangay as $barangay => $barangayScholars) {
            $pdf = Pdf::loadView('pdf.scholars-report-by-barangay', compact('barangayScholars', 'barangay', 'academicYear'))
                ->setPaper('a4', 'landscape');

            $pdfs[] = [
                'barangay' => $barangay,
                'pdf' => $pdf
            ];
        }

        // Create a zip file containing all PDFs
        $zip = new \ZipArchive();
        $zipFileName = 'scholars-report-by-barangay-' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($pdfs as $pdfData) {
                $pdfContent = $pdfData['pdf']->output();
                $zip->addFromString($pdfData['barangay'] . '-scholars-report.pdf', $pdfContent);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function generateApplicantsPdf(Request $request)
    {
        $query = DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select('tbl_applicant.*', 'tbl_application_personnel.remarks')
            ->where('tbl_application_personnel.initial_screening', 'Approved');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        // Apply remarks filter
        if ($request->has('remarks') && !empty($request->remarks)) {
            $query->where('tbl_application_personnel.remarks', $request->remarks);
        }

        $applicants = $query->get();

        $filters = [];
        if ($request->search) {
            $filters[] = 'Search: ' . $request->search;
        }
        if ($request->barangay) {
            $filters[] = 'Barangay: ' . $request->barangay;
        }
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }
        if ($request->remarks) {
            $filters[] = 'Remarks: ' . $request->remarks;
        }

        $pdf = Pdf::loadView('pdf.applicants-report', compact('applicants', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('applicants-report-' . date('Y-m-d') . '.pdf');
    }

    public function generateRenewalPdf(Request $request)
    {
        $query = DB::table('tbl_renewal')
            ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
            ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_renewal.renewal_id',
                'tbl_renewal.renewal_status',
                'tbl_renewal.date_submitted',
                'tbl_renewal.renewal_semester',
                'tbl_renewal.renewal_acad_year',
                'tbl_applicant.applicant_fname',
                'tbl_applicant.applicant_mname',
                'tbl_applicant.applicant_lname',
                'tbl_applicant.applicant_suffix',
                'tbl_applicant.applicant_school_name',
                'tbl_applicant.applicant_brgy',
                'tbl_applicant.applicant_course',
                'tbl_applicant.applicant_year_level'
            )
            ->where('tbl_renewal.renewal_status', 'Approved');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('renewal_acad_year', $request->academic_year);
        }

        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('renewal_status', $request->status);
        }

        $renewals = $query->get();

        $filters = [];
        if ($request->search) {
            $filters[] = 'Search: ' . $request->search;
        }
        if ($request->barangay) {
            $filters[] = 'Barangay: ' . $request->barangay;
        }
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }
        if ($request->status) {
            $filters[] = 'Status: ' . $request->status;
        }

        $pdf = Pdf::loadView('pdf.renewal-report', compact('renewals', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('renewal-report-' . date('Y-m-d') . '.pdf');
    }

    public function generateSummaryPdf(Request $request)
    {
        // Get the same data as the summary tab
        $totalApplicants = DB::table('tbl_applicant')->count();
        $approvedInitialCount = DB::table('tbl_application_personnel')->where('initial_screening', 'Approved')->count();
        $approvedStatusCount = DB::table('tbl_application_personnel')->where('status', 'Approved')->count();
        $inactiveScholarsCount = DB::table('tbl_scholar')->where('scholar_status', 'inactive')->count();

        $years = DB::table('tbl_application_personnel')->selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
        $applicantsTrend = [];
        $scholarsTrend = [];

        foreach ($years as $year) {
            $applicantsTrend[] = DB::table('tbl_application_personnel')->whereYear('created_at', $year)->count();
            $scholarsTrend[] = DB::table('tbl_scholar')->whereYear('date_activated', $year)->count();
        }

        $filters = [];
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }

        $pdf = Pdf::loadView('pdf.summary-report', compact(
            'totalApplicants',
            'approvedInitialCount',
            'approvedStatusCount',
            'inactiveScholarsCount',
            'years',
            'applicantsTrend',
            'scholarsTrend',
            'filters'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('summary-report-' . date('Y-m-d') . '.pdf');
    }

    public function getScholarsByBarangay(Request $request)
    {
        $query = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                's.scholar_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            )
            ->where('s.scholar_status', 'active');

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        $scholars = $query->get();

        return response()->json([
            'success' => true,
            'scholars' => $scholars
        ]);
    }

    public function getScholarsWithDisbursement(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|string|in:1st Semester,2nd Semester,Summer'
        ]);

        $scholarIds = DB::table('tbl_disburse')
            ->where('disburse_acad_year', $request->academic_year)
            ->where('disburse_semester', $request->semester)
            ->pluck('scholar_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'scholar_ids' => $scholarIds
        ]);
    }

    public function generateDisbursementPdf(Request $request)
    {
        // Get disbursement records with applicant information
        $query = DB::table('tbl_disburse as d')
            ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                'd.disburse_semester',
                'd.disburse_acad_year',
                'd.disburse_amount',
                'd.disburse_date',
                'a.applicant_brgy',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            );

        // Filter for signed disbursements if type is 'signed'
        if ($request->input('type') === 'signed') {
            $query->whereNotNull('d.disburse_signature');
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
            });
        }

        // Apply barangay filter
        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        // Apply academic year filter
        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        // Apply semester filter
        if ($request->has('semester') && !empty($request->semester)) {
            $query->where('d.disburse_semester', $request->semester);
        }

        $disbursements = $query->get();

        // Get filter info for page title
        $filters = [];
        if ($request->search) {
            $filters[] = 'Search: ' . $request->search;
        }
        if ($request->barangay) {
            $filters[] = 'Barangay: ' . $request->barangay;
        }
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }
        if ($request->semester) {
            $filters[] = 'Semester: ' . $request->semester;
        }

        return view('pdf.disbursement-print', compact('disbursements', 'filters'));
    }
public function getScholarsWithoutDisbursement(Request $request)
{
    try {
        $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|string|in:1st Semester,2nd Semester,Summer'
        ]);

        // Get scholars who don't have disbursements for the selected academic year and semester
        $scholars = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->leftJoin('tbl_disburse as d', function($join) use ($request) {
                $join->on('s.scholar_id', '=', 'd.scholar_id')
                     ->where('d.disburse_acad_year', $request->academic_year)
                     ->where('d.disburse_semester', $request->semester);
            })
            ->select(
                's.scholar_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                'a.applicant_brgy',
                'a.applicant_email',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            )
            ->where('s.scholar_status', 'active')
            ->whereNull('d.disburse_id') // Only scholars without disbursement for this year/semester
            ->get();

        return response()->json([
            'success' => true,
            'scholars' => $scholars
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getScholarsWithoutDisbursement: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading scholars: ' . $e->getMessage()
        ], 500);
    }
}
}
