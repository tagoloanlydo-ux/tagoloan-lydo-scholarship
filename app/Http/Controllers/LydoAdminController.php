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

    // Change from get() to paginate()
    $inactiveStaff = DB::table('tbl_lydopers')
        ->where('lydopers_role', 'lydo_staff')
        ->where('lydopers_status', 'inactive')
        ->paginate(15); // Add pagination

    // Change from get() to paginate()
    $activeStaff = DB::table('tbl_lydopers')
        ->where('lydopers_role', 'lydo_staff')
        ->where('lydopers_status', 'active')
        ->paginate(15); // Add pagination

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

    // Get scholars with applicant information - include both active and inactive
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
        );

    // Apply status filter - default to active
    $statusFilter = $request->get('status', 'active');
    if ($statusFilter === 'active') {
        $query->where('s.scholar_status', 'active');
    } elseif ($statusFilter === 'inactive') {
        $query->where('s.scholar_status', 'inactive');
    }
    // If 'all' is selected, show both active and inactive

    // Apply other filters
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

    $scholars = $query->get(0);

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

    return view('lydo_admin.scholar', compact('notifications', 'scholars', 'barangays', 'academicYears', 'statusFilter'));
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

        // Filter for unsigned disbursements if type is not specified
        if ($request->input('type') !== 'signed') {
            $query->whereNull('d.disburse_signature');
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

    // Get UNSIGNED disbursement records (where disburse_signature is NULL)
    $unsignedQuery = DB::table('tbl_disburse as d')
        ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            'd.disburse_id',
            'd.disburse_semester',
            'd.disburse_acad_year',
            'd.disburse_amount',
            'd.disburse_date',
            'd.disburse_signature',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
        )
        ->whereNull('d.disburse_signature'); // Only unsigned disbursements

    // Apply search filter for unsigned
    if ($request->has('search') && !empty($request->search)) {
        $unsignedQuery->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
        });
    }

    // Apply barangay filter for unsigned
    if ($request->has('barangay') && !empty($request->barangay)) {
        $unsignedQuery->where('a.applicant_brgy', $request->barangay);
    }

    // Apply academic year filter for unsigned
    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $unsignedQuery->where('d.disburse_acad_year', $request->academic_year);
    }

    // Apply semester filter for unsigned
    if ($request->has('semester') && !empty($request->semester)) {
        $unsignedQuery->where('d.disburse_semester', $request->semester);
    }

    $disbursements = $unsignedQuery->get();

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

    // Get SIGNED disbursements (where disburse_signature is NOT NULL)
    $signedQuery = DB::table('tbl_disburse as d')
        ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            'd.disburse_id',
            'd.disburse_semester',
            'd.disburse_acad_year',
            'd.disburse_amount',
            'd.disburse_date',
            'd.disburse_signature',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
        )
        ->whereNotNull('d.disburse_signature'); // Only signed disbursements

    // Apply search filter for signed
    if ($request->has('search') && !empty($request->search)) {
        $signedQuery->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
        });
    }

    // Apply barangay filter for signed
    if ($request->has('barangay') && !empty($request->barangay)) {
        $signedQuery->where('a.applicant_brgy', $request->barangay);
    }

    // Apply academic year filter for signed
    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $signedQuery->where('d.disburse_acad_year', $request->academic_year);
    }

    // Apply semester filter for signed
    if ($request->has('semester') && !empty($request->semester)) {
        $signedQuery->where('d.disburse_semester', $request->semester);
    }

    $signedDisbursements = $signedQuery->paginate(15);

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

    // Get applicants with filtering - show both Approved and Rejected initial screening
    $query = DB::table('tbl_applicant')
        ->join('tbl_application', 'tbl_applicant.applicant_id', '=', 'tbl_application.applicant_id')
        ->join('tbl_application_personnel', 'tbl_application.application_id', '=', 'tbl_application_personnel.application_id')
        ->select(
            'tbl_applicant.*',
            'tbl_application.application_letter',
            'tbl_application.cert_of_reg',
            'tbl_application.grade_slip',
            'tbl_application.brgy_indigency',
            'tbl_application.student_id',
            'tbl_application.date_submitted',
            'tbl_application_personnel.initial_screening',
            'tbl_application_personnel.status'
        );

    // Apply initial screening status filter - default to Approved
    $initialScreeningStatus = $request->get('initial_screening', 'Approved');
    if ($initialScreeningStatus && $initialScreeningStatus !== 'all') {
        $query->where('tbl_application_personnel.initial_screening', $initialScreeningStatus);
    }

    // Apply other filters
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

    $applicants = $query->get();

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

    return view('lydo_admin.applicants', compact('notifications', 'applicants', 'barangays', 'academicYears', 'initialScreeningStatus'));
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

    public function generateDisbursementPdf(Request $request)
    {
        // Get only signed disbursement records with applicant information
        $query = DB::table('tbl_disburse as d')
            ->join('tbl_scholar as s', 'd.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                'd.disburse_semester',
                'd.disburse_acad_year',
                'd.disburse_amount',
                'd.disburse_date',
                'd.disburse_signature',
                'a.applicant_brgy',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            )
            ->whereNotNull('d.disburse_signature'); // Only signed disbursements

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        if ($request->has('semester') && !empty($request->semester)) {
            $query->where('d.disburse_semester', $request->semester);
        }

        $signedDisbursements = $query->get();

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

        $pdf = Pdf::loadView('pdf.disbursement-print', compact('signedDisbursements', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('disbursement-report-' . date('Y-m-d') . '.pdf');
    }
public function generateDisbursementRecordsPdf(Request $request)
{
    try {
        // Set time limit for PDF generation
        set_time_limit(120); // 2 minutes
        
        // Get only UNSIGNED disbursement records with applicant information
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
            )
            ->whereNull('d.disburse_signature'); // Only unsigned disbursements

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('a.applicant_fname', 'like', '%' . $search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $search . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        if ($request->has('semester') && !empty($request->semester)) {
            $query->where('d.disburse_semester', $request->semester);
        }

        // Limit results for PDF generation
        $unsignedDisbursements = $query->limit(1000)->get();

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

        $pdf = Pdf::loadView('pdf.disbursement-records-print', compact('unsignedDisbursements', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('disbursement-records-' . date('Y-m-d') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
    }
}
public function getScholarDocuments($scholar_id)
{
    try {
        $documents = DB::table('tbl_renewal')
            ->where('scholar_id', $scholar_id)
            ->select(
                'renewal_cert_of_reg',
                'renewal_grade_slip',
                'renewal_brgy_indigency',
                'renewal_semester',
                'renewal_acad_year',
                'date_submitted',
                'renewal_status'
            )
            ->get();

        // Process documents to generate proper URLs
        $processedDocuments = $documents->map(function ($doc) {
            return [
                'renewal_cert_of_reg' => $this->getDocumentUrl($doc->renewal_cert_of_reg),
                'renewal_grade_slip' => $this->getDocumentUrl($doc->renewal_grade_slip),
                'renewal_brgy_indigency' => $this->getDocumentUrl($doc->renewal_brgy_indigency),
                'renewal_semester' => $doc->renewal_semester,
                'renewal_acad_year' => $doc->renewal_acad_year,
                'date_submitted' => $doc->date_submitted,
                'renewal_status' => $doc->renewal_status,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $processedDocuments
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching documents: ' . $e->getMessage()
        ], 500);
    }
}

// Helper method to generate proper document URLs
private function getDocumentUrl($filePath)
{
    if (empty($filePath)) {
        return null;
    }

    // If it's already a full URL, return as is
    if (filter_var($filePath, FILTER_VALIDATE_URL)) {
        return $filePath;
    }

    // If it starts with storage/, convert to proper URL
    if (strpos($filePath, 'storage/') === 0) {
        return asset($filePath);
    }

    // If it's just a filename, assume it's in storage/renewal
    if (!str_contains($filePath, '/')) {
        return asset('storage/renewal/' . $filePath);
    }

    // For other cases, try to generate URL
    return asset('storage/' . ltrim($filePath, '/'));
}


public function generateScholarsPdf(Request $request)
{
    try {
        // Set time limit for PDF generation
        set_time_limit(120); // 2 minutes
        
        // Get scholars with applicant information - same query as scholar method
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
            );

        // Apply status filter
        $statusFilter = $request->get('status', 'active');
        if ($statusFilter === 'active') {
            $query->where('s.scholar_status', 'active');
        } elseif ($statusFilter === 'inactive') {
            $query->where('s.scholar_status', 'inactive');
        }

        // Apply other filters
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

        $scholars = $query->get();

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
        if ($request->status) {
            $filters[] = 'Status: ' . ucfirst($request->status);
        }

        $pdf = Pdf::loadView('pdf.scholars-print', compact('scholars', 'filters'))
            ->setPaper('a4', 'portrait'); // Changed from 'landscape' to 'portrait'

        return $pdf->stream('scholars-list-' . date('Y-m-d') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
    }
}
public function generateApplicantsPdf(Request $request)
{
    try {
        // Set time limit for PDF generation
        set_time_limit(120); // 2 minutes
        
        // Get applicants with filtering - same query as applicants method
        $query = DB::table('tbl_applicant')
            ->join('tbl_application', 'tbl_applicant.applicant_id', '=', 'tbl_application.applicant_id')
            ->join('tbl_application_personnel', 'tbl_application.application_id', '=', 'tbl_application_personnel.application_id')
            ->select(
                'tbl_applicant.*',
                'tbl_application_personnel.initial_screening',
                'tbl_application_personnel.status'
            );

        // Apply initial screening status filter
        $initialScreeningStatus = $request->get('initial_screening', 'Approved');
        if ($initialScreeningStatus && $initialScreeningStatus !== 'all') {
            $query->where('tbl_application_personnel.initial_screening', $initialScreeningStatus);
        }

        // Apply other filters
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

        $applicants = $query->get();

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
        if ($request->initial_screening) {
            $filters[] = 'Initial Screening: ' . $request->initial_screening;
        }

        $pdf = Pdf::loadView('pdf.applicants-print', compact('applicants', 'filters'))
            ->setPaper('a4', 'portrait'); // Changed to portrait

        return $pdf->stream('applicants-list-' . date('Y-m-d') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
    }
}
}

