<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\FamilyIntakeSheet;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Announce;
use App\Models\Disburse;
use App\Models\Settings;
class LydoAdminController extends Controller
{

    public function announcement()
    {

        $announcements = Announce::orderBy('date_posted', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lydo_admin.announcement', compact( 'announcements'));
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

    // ADDED: Count for graduated scholars
    $graduatedScholars = DB::table('tbl_scholar')
        ->where('scholar_status', 'graduated')
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

    // ADDED: Get scholar statistics per academic year for the line chart
    $scholarStatsPerYear = $this->getScholarStatisticsPerYear();

    return view('lydo_admin.dashboard', compact(
        'totalApplicants',
        'totalScholarsWholeYear',
        'inactiveScholars',
        'graduatedScholars',
        'currentAcademicYear',
        'barangayDistribution',
        'schoolDistribution',
        'scholarStatsPerYear' // ADDED: Pass scholar statistics to view
    ));
}

// ADDED: New method to get scholar statistics per academic year
private function getScholarStatisticsPerYear()
{
    // Get all unique academic years from scholars
    $academicYears = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select('a.applicant_acad_year')
        ->distinct()
        ->whereNotNull('a.applicant_acad_year')
        ->orderBy('a.applicant_acad_year', 'asc')
        ->pluck('applicant_acad_year');

    $stats = [];

    foreach ($academicYears as $year) {
        $activeCount = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->where('a.applicant_acad_year', $year)
            ->where('s.scholar_status', 'active')
            ->count();

        $inactiveCount = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->where('a.applicant_acad_year', $year)
            ->where('s.scholar_status', 'inactive')
            ->count();

        $graduatedCount = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->where('a.applicant_acad_year', $year)
            ->where('s.scholar_status', 'graduated')
            ->count();

        $stats[] = [
            'academic_year' => $year,
            'active' => $activeCount,
            'inactive' => $inactiveCount,
            'graduated' => $graduatedCount
        ];
    }

    return $stats;
}

public function lydo()
{
    $inactiveStaff = DB::table('tbl_lydopers')
        ->where('lydopers_role', 'lydo_staff')
        ->where('lydopers_status', 'inactive')
        ->orderBy('lydopers_lname') // Alphabetical by last name
        ->paginate(15);

    $activeStaff = DB::table('tbl_lydopers')
        ->where('lydopers_role', 'lydo_staff')
        ->where('lydopers_status', 'active')
        ->orderBy('lydopers_lname') // Alphabetical by last name
        ->paginate(1500);

    return view('lydo_admin.lydo', compact('inactiveStaff', 'activeStaff'));
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
    // Inactive Mayor Staff
    $inactiveStaff = DB::table('tbl_lydopers')
        ->select(
            '*',
            DB::raw("CONCAT(
                UPPER(LEFT(lydopers_lname,1)), LOWER(SUBSTRING(lydopers_lname,2)),
                ', ',
                UPPER(LEFT(lydopers_fname,1)), LOWER(SUBSTRING(lydopers_fname,2)),
                IF(lydopers_mname IS NOT NULL AND lydopers_mname != '', CONCAT(' ', UPPER(LEFT(lydopers_mname,1)), '.'), ''),
                IF(lydopers_suffix IS NOT NULL AND lydopers_suffix != '', CONCAT(' ', lydopers_suffix), '')
            ) as full_name")
        )
        ->where('lydopers_role', 'mayor_staff')
        ->where('lydopers_status', 'inactive')
        ->orderBy('lydopers_lname', 'asc')
        ->orderBy('lydopers_fname', 'asc')
        ->paginate(1500, ['*'], 'inactive_page');

    // Active Mayor Staff
    $activeStaff = DB::table('tbl_lydopers')
        ->select(
            '*',
            DB::raw("CONCAT(
                UPPER(LEFT(lydopers_lname,1)), LOWER(SUBSTRING(lydopers_lname,2)),
                ', ',
                UPPER(LEFT(lydopers_fname,1)), LOWER(SUBSTRING(lydopers_fname,2)),
                IF(lydopers_mname IS NOT NULL AND lydopers_mname != '', CONCAT(' ', UPPER(LEFT(lydopers_mname,1)), '.'), ''),
                IF(lydopers_suffix IS NOT NULL AND lydopers_suffix != '', CONCAT(' ', lydopers_suffix), '')
            ) as full_name")
        )
        ->where('lydopers_role', 'mayor_staff')
        ->where('lydopers_status', 'active')
        ->orderBy('lydopers_lname', 'asc')
        ->orderBy('lydopers_fname', 'asc')
        ->paginate(15, ['*'], 'active_page');

    return view('lydo_admin.mayor', compact('inactiveStaff', 'activeStaff'));
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
    // Get current renewal settings
    $settings = Settings::first();
    $currentDate = now();
    
    // Initialize variables
    $scholarsWithoutRenewal = collect();
    $showRenewalSection = false;
    $renewalInfo = [
        'semester' => null,
        'start_date' => null,
        'deadline' => null,
        'grace_period_end' => null,
        'is_grace_period' => false,
        'is_after_grace_period' => false
    ];
    
    // Get current academic year from applicants
    $currentAcademicYear = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->orderBy('applicant_acad_year', 'desc')
        ->value('applicant_acad_year');

    if (!$currentAcademicYear) {
        $currentAcademicYear = date('Y') . '-' . (date('Y') + 1);
    }
    
    // Check if we're within renewal period or grace period
    if ($settings && $settings->renewal_start_date && $settings->renewal_deadline) {
        $renewalStartDate = \Carbon\Carbon::parse($settings->renewal_start_date);
        $renewalDeadline = \Carbon\Carbon::parse($settings->renewal_deadline);
        $gracePeriodEnd = $renewalDeadline->copy()->addDays(10);
        
        $renewalInfo = [
            'semester' => $settings->renewal_semester,
            'start_date' => $renewalStartDate,
            'deadline' => $renewalDeadline,
            'grace_period_end' => $gracePeriodEnd,
            'is_grace_period' => $currentDate->greaterThan($renewalDeadline) && $currentDate->lessThanOrEqualTo($gracePeriodEnd),
            'is_after_grace_period' => $currentDate->greaterThan($gracePeriodEnd)
        ];
        
        // Show renewal section if current date is after renewal start date
        if ($currentDate->greaterThanOrEqualTo($renewalStartDate)) {
            $showRenewalSection = true;
            
            // Fetch active scholars without renewal applications for current semester
            // EXCLUDING scholars from the current academic year (new scholars)
            $scholarsWithoutRenewal = DB::table('tbl_scholar as s')
                ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                ->leftJoin('tbl_renewal as r', function($join) use ($settings) {
                    $join->on('s.scholar_id', '=', 'r.scholar_id')
                         ->where('r.renewal_semester', $settings->renewal_semester);
                })
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
                    'a.applicant_acad_year', // Include academic year for debugging
                    DB::raw("CONCAT(
                        UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)), 
                        ', ', 
                        UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                        IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(' ', UPPER(LEFT(a.applicant_mname, 1)), '.'), ''),
                        IF(a.applicant_suffix IS NOT NULL AND a.applicant_suffix != '', CONCAT(' ', a.applicant_suffix), '')
                    ) as full_name")
                )
                ->where('s.scholar_status', 'active')
                ->whereNull('r.renewal_id')
                // EXCLUDE scholars from the current academic year - they don't need renewal yet
                ->where('a.applicant_acad_year', '!=', $currentAcademicYear)
                ->orderBy('a.applicant_lname', 'asc')
                ->orderBy('a.applicant_fname', 'asc')
                ->paginate(15);
            
            // Auto-update status for scholars who missed the deadline + grace period
            if ($currentDate->greaterThan($gracePeriodEnd)) {
                $updatedCount = $this->autoUpdateInactiveScholars($settings->renewal_semester, $currentAcademicYear);
                session()->flash('auto_update_info', "Automatically updated {$updatedCount} scholars to inactive status for missing renewal deadline.");
            }
        }
    }

    // Fetch graduating scholars (4th Year and 5th Year)
    $graduatingScholars = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
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
            'a.applicant_acad_year',
            DB::raw("CONCAT(
                UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)), 
                ', ', 
                UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(' ', UPPER(LEFT(a.applicant_mname, 1)), '.'), ''),
                IF(a.applicant_suffix IS NOT NULL AND a.applicant_suffix != '', CONCAT(' ', a.applicant_suffix), '')
            ) as full_name")
        )
        ->where('s.scholar_status', 'active')
        ->whereIn('a.applicant_year_level', ['4th Year', '5th Year'])
        ->orderBy('a.applicant_lname', 'asc')
        ->orderBy('a.applicant_fname', 'asc')
        ->paginate(15);

    // Get distinct barangays for filter dropdown
    $barangays = DB::table('tbl_applicant')
        ->select('applicant_brgy')
        ->distinct()
        ->orderBy('applicant_brgy', 'asc')
        ->pluck('applicant_brgy');

    return view('lydo_admin.status', compact(
        'scholarsWithoutRenewal', 
        'graduatingScholars', 
        'barangays',
        'showRenewalSection',
        'settings',
        'renewalInfo',
        'currentAcademicYear' // Pass for debugging if needed
    ));
}

/**
 * Automatically update scholar status to inactive for those who missed renewal deadline
 */
private function autoUpdateInactiveScholars($renewalSemester, $currentAcademicYear)
{
    try {
        $scholarsToUpdate = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->leftJoin('tbl_renewal as r', function($join) use ($renewalSemester) {
                $join->on('s.scholar_id', '=', 'r.scholar_id')
                     ->where('r.renewal_semester', $renewalSemester);
            })
            ->where('s.scholar_status', 'active')
            ->whereNull('r.renewal_id')
            // EXCLUDE current academic year scholars from auto-update
            ->where('a.applicant_acad_year', '!=', $currentAcademicYear)
            ->pluck('s.scholar_id')
            ->toArray();

        $updatedCount = 0;

        if (!empty($scholarsToUpdate)) {
            $updatedCount = DB::table('tbl_scholar')
                ->whereIn('scholar_id', $scholarsToUpdate)
                ->update([
                    'scholar_status' => 'inactive',
                    'updated_at' => now()
                ]);

            \Log::info("Automatically updated {$updatedCount} scholars to inactive status for missing renewal deadline for {$renewalSemester}. Excluded current academic year: {$currentAcademicYear}");
        }

        return $updatedCount;
        
    } catch (\Exception $e) {
        \Log::error('Error in autoUpdateInactiveScholars: ' . $e->getMessage());
        return 0;
    }
}

public function markAsGraduated(Request $request)
{
    $request->validate([
        'selected_graduating_scholars' => 'required|array',
        'selected_graduating_scholars.*' => 'exists:tbl_scholar,scholar_id'
    ]);

    $graduatedScholars = [];

    // Get scholar details before updating
    $scholars = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->whereIn('s.scholar_id', $request->selected_graduating_scholars)
        ->select(
            's.scholar_id',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_email',
            'a.applicant_school_name',
            'a.applicant_course',
            'a.applicant_year_level'
        )
        ->get();

    // Update scholar status
    DB::table('tbl_scholar')
        ->whereIn('scholar_id', $request->selected_graduating_scholars)
        ->update([
            'scholar_status' => 'graduated',
            'date_graduated' => now(),
            'updated_at' => now()
        ]);

    // Prepare scholar data for email notification and certificate
    foreach ($scholars as $scholar) {
        $graduatedScholars[] = [
            'email' => $scholar->applicant_email,
            'name' => $scholar->applicant_fname . ' ' . 
                     ($scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '') . 
                     $scholar->applicant_lname . 
                     ($scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : ''),
            'scholar_id' => $scholar->scholar_id,
            'school' => $scholar->applicant_school_name,
            'course' => $scholar->applicant_course,
            'year_level' => $scholar->applicant_year_level
        ];
    }

    // Send graduation notification emails with certificates
    if (!empty($graduatedScholars)) {
        $this->sendGraduationNotification($graduatedScholars);
    }

    return redirect()->back()->with('success', 'Scholars marked as graduated successfully! Email notifications with certificates sent to ' . count($graduatedScholars) . ' scholar(s).');
}

private function sendGraduationNotification($scholars)
{
    try {
        $successCount = 0;
        $errorCount = 0;

        foreach ($scholars as $scholar) {
            try {
                // Generate individual certificate for each scholar
                $certificatePdf = $this->generateIndividualCertificate($scholar);
                
                $emailData = [
                    'scholar_name' => $scholar['name'],
                    'scholar_id' => $scholar['scholar_id'],
                    'school' => $scholar['school'],
                    'course' => $scholar['course'],
                    'graduation_date' => now()->format('F d, Y'),
                    'current_year' => now()->format('Y'),
                ];

                Mail::send('emails.graduation-notification', $emailData, function ($message) use ($scholar, $certificatePdf) {
                    $message->to($scholar['email'])
                            ->subject('Congratulations! Scholarship Graduation - LYDO Scholarship Program')
                            ->attachData($certificatePdf, 
                                       'Graduation-Certificate-' . $scholar['scholar_id'] . '.pdf', 
                                       ['mime' => 'application/pdf']);
                });

                $successCount++;
                \Log::info('Graduation notification sent to: ' . $scholar['email']);

            } catch (\Exception $e) {
                $errorCount++;
                \Log::error('Failed to send graduation notification to ' . $scholar['email'] . ': ' . $e->getMessage());
                // Continue with other scholars even if one fails
                continue;
            }
        }
        
        \Log::info('Graduation notifications completed. Success: ' . $successCount . ', Failed: ' . $errorCount);
        return $successCount > 0;
        
    } catch (\Exception $e) {
        \Log::error('Failed to send graduation notifications: ' . $e->getMessage());
        return false;
    }
}
private function generateIndividualCertificate($scholar)
{
    try {
        $scholarData = [[
            'name' => $scholar['name'],
            'scholar_id' => $scholar['scholar_id'],
            'school' => $scholar['school'],
            'course' => $scholar['course'],
            'year_level' => $scholar['year_level'],
            'graduation_date' => now()->format('F d, Y')
        ]];

        // Get current user session safely
        $currentUser = session('lydopers');
        
        // Get LYDO Admin name safely
        $lydoAdminName = 'LYDO Program Director'; // Default fallback
        if ($currentUser && isset($currentUser->lydopers_fname) && isset($currentUser->lydopers_lname)) {
            $lydoAdminName = $currentUser->lydopers_fname . ' ' . $currentUser->lydopers_lname;
        }

        // Get Mayor Staff name safely
        $mayorStaffName = 'City Mayor'; // Default fallback
        try {
            $mayorStaff = DB::table('tbl_lydopers')
                ->where('lydopers_role', 'mayor_staff')
                ->where('lydopers_status', 'active')
                ->orderBy('lydopers_lname')
                ->first();
                
            if ($mayorStaff && isset($mayorStaff->lydopers_fname) && isset($mayorStaff->lydopers_lname)) {
                $mayorStaffName = $mayorStaff->lydopers_fname . ' ' . $mayorStaff->lydopers_lname;
            }
        } catch (\Exception $e) {
            \Log::warning('Could not fetch mayor staff name: ' . $e->getMessage());
        }

        $pdf = PDF::loadView('pdf.graduation-certificate', [
            'graduatedScholars' => $scholarData,
            'lydoAdminName' => $lydoAdminName,
            'mayorStaffName' => $mayorStaffName
        ])->setPaper('a4', 'portrait')
          ->setOption('enable-local-file-access', true)
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);

        return $pdf->output();
        
    } catch (\Exception $e) {
        \Log::error('Certificate generation error for scholar ' . $scholar['scholar_id'] . ': ' . $e->getMessage());
        throw $e;
    }
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
                DB::raw("
                    CONCAT(
                        UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                        ', ',
                        UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                        ' ',
                        IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(UPPER(LEFT(a.applicant_mname,1)), '.'), ''),
                        ' ',
                        IF(a.applicant_suffix IS NOT NULL, a.applicant_suffix, '')
                    ) as full_name
                ")
            );

        if ($request->input('type') !== 'signed') {
            $query->whereNull('d.disburse_signature');
        }

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

        // ✅ Sort alphabetically by last name
        $query->orderBy('a.applicant_lname', 'asc')
              ->orderBy('a.applicant_fname', 'asc');

        $disbursements = $query->get();

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

    // Scholars query
    $scholarsQuery = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            's.scholar_id',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            'a.applicant_acad_year',
            DB::raw("
                CONCAT(
                    UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                    ', ',
                    UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                    ' ',
                    IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(UPPER(LEFT(a.applicant_mname,1)), '.'), ''),
                    ' ',
                    IF(a.applicant_suffix IS NOT NULL, a.applicant_suffix, '')
                ) as full_name
            ")
        )
        ->where('s.scholar_status', 'active');

    if ($request->has('scholar_academic_year') && !empty($request->scholar_academic_year)) {
        $scholarsQuery->where('a.applicant_acad_year', $request->scholar_academic_year);
    }

    // ✅ Alphabetical
    $scholarsQuery->orderBy('a.applicant_lname', 'asc')
                  ->orderBy('a.applicant_fname', 'asc');

    $scholars = $scholarsQuery->get();

    // Unsigned disbursements
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
            DB::raw("
                CONCAT(
                    UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                    ', ',
                    UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                    ' ',
                    IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(UPPER(LEFT(a.applicant_mname,1)), '.'), ''),
                    ' ',
                    IF(a.applicant_suffix IS NOT NULL, a.applicant_suffix, '')
                ) as full_name
            ")
        )
        ->whereNull('d.disburse_signature');

    if ($request->has('search') && !empty($request->search)) {
        $unsignedQuery->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('barangay') && !empty($request->barangay)) {
        $unsignedQuery->where('a.applicant_brgy', $request->barangay);
    }

    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $unsignedQuery->where('d.disburse_acad_year', $request->academic_year);
    }

    if ($request->has('semester') && !empty($request->semester)) {
        $unsignedQuery->where('d.disburse_semester', $request->semester);
    }

    // ✅ Alphabetical
    $unsignedQuery->orderBy('a.applicant_lname', 'asc')
                  ->orderBy('a.applicant_fname', 'asc');

    $disbursements = $unsignedQuery->get();

    // Dropdowns
    $barangays = DB::table('tbl_applicant')->select('applicant_brgy')->distinct()->orderBy('applicant_brgy', 'asc')->pluck('applicant_brgy');
    $academicYears = DB::table('tbl_disburse')->select('disburse_acad_year')->distinct()->orderBy('disburse_acad_year', 'desc')->pluck('disburse_acad_year');
    $semesters = DB::table('tbl_disburse')->select('disburse_semester')->distinct()->orderBy('disburse_semester', 'asc')->pluck('disburse_semester');

    // Signed disbursements
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
            DB::raw("
                CONCAT(
                    UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                    ', ',
                    UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                    ' ',
                    IF(a.applicant_mname IS NOT NULL AND a.applicant_mname != '', CONCAT(UPPER(LEFT(a.applicant_mname,1)), '.'), ''),
                    ' ',
                    IF(a.applicant_suffix IS NOT NULL, a.applicant_suffix, '')
                ) as full_name
            ")
        )
        ->whereNotNull('d.disburse_signature');

    if ($request->has('search') && !empty($request->search)) {
        $signedQuery->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_mname', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('barangay') && !empty($request->barangay)) {
        $signedQuery->where('a.applicant_brgy', $request->barangay);
    }

    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $signedQuery->where('d.disburse_acad_year', $request->academic_year);
    }

    if ($request->has('semester') && !empty($request->semester)) {
        $signedQuery->where('d.disburse_semester', $request->semester);
    }

    // ✅ Alphabetical
    $signedQuery->orderBy('a.applicant_lname', 'asc')
                ->orderBy('a.applicant_fname', 'asc');

    $signedDisbursements = $signedQuery->paginate(15);

    $scholarAcademicYears = DB::table('tbl_applicant')
        ->join('tbl_application', 'tbl_applicant.applicant_id', '=', 'tbl_application.applicant_id')
        ->join('tbl_scholar', 'tbl_application.application_id', '=', 'tbl_scholar.application_id')
        ->select('applicant_acad_year')
        ->distinct()
        ->where('tbl_scholar.scholar_status', 'active')
        ->orderBy('applicant_acad_year', 'desc')
        ->pluck('applicant_acad_year');

    return view('lydo_admin.disbursement', compact(
        'disbursements', 
        'barangays', 
        'academicYears', 
        'semesters', 
        'scholars', 
        'signedDisbursements',
        'scholarAcademicYears'
    ));
}

    public function settings()
    {
        $settings = Settings::first() ?? new Settings();

        return view('lydo_admin.settings', compact('settings'));
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

    // Enhanced initial screening filter with clearer options
    $initialScreeningStatus = $request->get('initial_screening', 'all');
    if ($initialScreeningStatus && $initialScreeningStatus !== 'all') {
        if ($initialScreeningStatus === 'for_lydo_review') {
            // Show applicants that are approved by Mayor Staff and ready for LYDO review
            $query->where('tbl_application_personnel.initial_screening', 'Approved');
        } else {
            $query->where('tbl_application_personnel.initial_screening', $initialScreeningStatus);
        }
    }

    // Other filters remain the same
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

    // Order by last name, first name alphabetically
    $applicants = $query
        ->orderBy('applicant_lname', 'asc')
        ->orderBy('applicant_fname', 'asc')
        ->orderBy('applicant_mname', 'asc')
        ->get();

    // Filter dropdowns
    $barangays = DB::table('tbl_applicant')
        ->select('applicant_brgy')
        ->distinct()
        ->orderBy('applicant_brgy', 'asc')
        ->pluck('applicant_brgy');

    $academicYears = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->distinct()
        ->orderBy('applicant_acad_year', 'desc')
        ->pluck('applicant_acad_year');

    return view('lydo_admin.applicants', compact('applicants', 'barangays', 'academicYears', 'initialScreeningStatus'));
}
public function getMayorApplicants(Request $request)
{
    try {
        $query = DB::table('tbl_applicant as app')
            ->join('tbl_application as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->join('tbl_application_personnel as ap', 'a.application_id', '=', 'ap.application_id')
            ->select(
                'app.*',
                'ap.initial_screening',
                'ap.status',
                'ap.remarks'
            )
            // ✅ SOLUSYON: Isama ang 'Reviewed' status
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected', 'Reviewed'])
            ->orderBy('app.applicant_lname', 'asc')
            ->orderBy('app.applicant_fname', 'asc')
            ->orderBy('app.applicant_mname', 'asc');

        $applicants = $query->get();

        return response()->json([
            'success' => true,
            'applicants' => $applicants
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting mayor applicants: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading applicants.'
        ], 500);
    }
}

public function getLydoReviewedApplicants(Request $request)
{
    try {
        $query = DB::table('tbl_applicant as app')
            ->join('tbl_application as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->join('tbl_application_personnel as ap', 'a.application_id', '=', 'ap.application_id')
            ->select(
                'app.*',
                'ap.initial_screening',
                'ap.status',
                'ap.remarks'
            )
            ->where('ap.initial_screening', 'Reviewed')
            ->orderBy('app.applicant_lname', 'asc')
            ->orderBy('app.applicant_fname', 'asc')
            ->orderBy('app.applicant_mname', 'asc');

        $applicants = $query->get();

        return response()->json([
            'success' => true,
            'applicants' => $applicants
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting LYDO reviewed applicants: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading applicants.'
        ], 500);
    }
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

// Add these methods to your LydoAdminController.php

public function updatePersonalInfo(Request $request, $id)
{
    try {
        $validator = \Validator::make($request->all(), [
            'lydopers_fname' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'lydopers_mname' => 'nullable|string|max:50|regex:/^[a-zA-Z\s]*$/',
            'lydopers_lname' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'lydopers_suffix' => 'nullable|string|max:10|regex:/^[a-zA-Z\s]*$/',
            'lydopers_email' => 'required|email|max:100|unique:tbl_lydopers,lydopers_email,' . $id . ',lydopers_id',
            'lydopers_address' => 'nullable|string|max:255',
            'lydopers_contact_number' => 'required|numeric|digits_between:10,11',
            'lydopers_bdate' => 'nullable|date|before:today',
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
                'lydopers_bdate' => $request->lydopers_bdate,
                'updated_at' => now(),
            ]);

        // Update session data
        $updatedUser = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();
        session(['lydopers' => $updatedUser]);

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
                    'lydopers_bdate' => $request->lydopers_bdate,
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
            'new_password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
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
            'disbursement_location' => 'required|string',
            'disbursement_time' => 'required|string',
            'barangayFilter' => 'nullable|string', // ADDED
            'scholar_academic_year' => 'nullable|string', // ADDED
        ]);
        $scholarIds = $request->scholar_ids;
    } else {
        $request->validate([
            'scholar_ids' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'semester' => 'required|string|in:1st Semester,2nd Semester,Summer',
            'academic_year' => 'required|string',
            'disbursement_location' => 'required|string',
            'disbursement_time' => 'required|string',
            'barangayFilter' => 'nullable|string', // ADDED
            'scholar_academic_year' => 'nullable|string', // ADDED
        ]);
        $scholarIds = explode(',', $request->scholar_ids);
    }

    $lydopersId = session('lydopers')->lydopers_id;
    $academicYear = $request->academic_year;
    $semester = $request->semester;
    $disbursementDate = $request->disbursement_date;
    $disbursementLocation = $request->disbursement_location;
    $disbursementTime = $request->disbursement_time;
    
    // GET THE FILTER VALUES
    $selectedBarangay = $request->input('barangayFilter', '');
    $selectedAcademicYear = $request->input('scholar_academic_year', '');
    
    $createdCount = 0;
    $skippedScholars = [];
    $skippedNames = [];
    $successfulScholars = [];

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
                    'disburse_date' => $disbursementDate,
                ]);
                
                // Get scholar details for email notification
                $scholar = DB::table('tbl_scholar as s')
                    ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                    ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                    ->where('s.scholar_id', $cleanScholarId)
                    ->select('a.applicant_email', 'a.applicant_fname', 'a.applicant_lname')
                    ->first();

                if ($scholar) {
                    $successfulScholars[] = [
                        'email' => $scholar->applicant_email,
                        'name' => $scholar->applicant_fname . ' ' . $scholar->applicant_lname,
                        'scholar_id' => $cleanScholarId
                    ];
                }
                
                $createdCount++;
            }
        }

        // Send email notifications to successful scholars
        if (!empty($successfulScholars)) {
            $this->sendDisbursementNotification($successfulScholars, $disbursementDate, $semester, $academicYear, $request->amount, $disbursementTime, $disbursementLocation);
        }

        // Create announcement for disbursement if at least one disbursement was created
        if ($createdCount > 0) {
            $this->createDisbursementAnnouncement(
                $disbursementDate, 
                $disbursementLocation, 
                $disbursementTime, 
                $semester, 
                $academicYear, 
                $createdCount, 
                $request->amount, 
                $selectedBarangay, 
                $selectedAcademicYear
            );
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
            
            // Add email notification info
            if (!empty($successfulScholars)) {
                $message .= " Email notifications sent to " . count($successfulScholars) . " scholar(s).";
            }
            
            // Add announcement info
            $message .= " Announcement created for the disbursement schedule.";
            
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
private function createDisbursementAnnouncement($disbursementDate, $location, $time, $semester, $academicYear, $scholarCount, $amount, $selectedBarangay = '', $selectedAcademicYear = '')
{
    try {
        $formattedDate = \Carbon\Carbon::parse($disbursementDate)->format('F d, Y');
        $formattedTime = \Carbon\Carbon::parse($time)->format('h:i A');
        $formattedAmount = number_format($amount, 2);

        // Build the announcement title dynamically
        $announcementTitle = "Disbursement Schedule - {$semester} {$academicYear}";
        if ($selectedBarangay) {
            $announcementTitle .= " - {$selectedBarangay}";
        }
        
        // Build the announcement content with proper formatting
        $barangaySection = "";
        if ($selectedBarangay) {
            $barangaySection = "Barangay: {$selectedBarangay}\n";
        }

        $filteredAcademicYearSection = "";
        if ($selectedAcademicYear && $selectedAcademicYear !== $academicYear) {
            $filteredAcademicYearSection = "Filtered Academic Year: {$selectedAcademicYear}\n";
        }

        $announcementContent = "ATTENTION ALL SCHOLARS!\n\n"
            . "We are pleased to announce the upcoming disbursement schedule for the {$semester} of Academic Year {$academicYear}.\n\n"
            . "DISBURSEMENT DETAILS:\n"
            . "• Date: {$formattedDate}\n"
            . "• Time: {$formattedTime}\n"
            . "• Location: {$location}\n"
            . "• Amount: ₱{$formattedAmount}\n"
            . "• Number of Scholars: {$scholarCount}\n"
            . "• Semester: {$semester}\n"
            . "• Academic Year: {$academicYear}\n"
            . ($barangaySection ? "• {$barangaySection}" : "")
            . ($filteredAcademicYearSection ? "• {$filteredAcademicYearSection}" : "") . "\n"
            . "IMPORTANT REMINDERS:\n"
            . "• Please bring your valid school ID and any required documents\n"
            . "• Be on time to avoid long queues\n"
            . "• Wear proper attire\n"
            . "• Prepare your signature for the disbursement receipt\n\n"
            . "ADDITIONAL INFORMATION:\n"
            . "This disbursement covers your scholarship stipend for the {$semester}. If you have any questions or concerns, please contact the LYDO office during office hours.\n\n"
            . "Thank you for your cooperation and continue to strive for academic excellence!\n\n"
            . "LYDO Scholarship Program\n"
            . "City Government";

        Announce::create([
            'lydopers_id' => session('lydopers')->lydopers_id,
            'announce_title' => $announcementTitle,
            'announce_content' => $announcementContent,
            'announce_type' => 'scholars',
            'date_posted' => now(),
        ]);

        \Log::info("Disbursement announcement created for {$semester} {$academicYear} - Barangay: " . ($selectedBarangay ?: 'All'));

    } catch (\Exception $e) {
        \Log::error('Failed to create disbursement announcement: ' . $e->getMessage());
        // Don't throw error here to avoid disrupting the main disbursement process
    }
}
private function sendDisbursementNotification($scholars, $disbursementDate, $semester, $academicYear, $amount, $disbursementTime = null, $disbursementLocation = null)
{
    try {
        foreach ($scholars as $scholar) {
            $formattedDate = \Carbon\Carbon::parse($disbursementDate)->format('F d, Y');
            $formattedAmount = number_format($amount, 2);
            $formattedTime = $disbursementTime ? \Carbon\Carbon::parse($disbursementTime)->format('h:i A') : 'To be announced';
            $location = $disbursementLocation ?: 'To be announced';
            
            $emailData = [
                'scholar_name' => $scholar['name'],
                'disbursement_date' => $formattedDate,
                'disbursement_time' => $formattedTime,
                'disbursement_location' => $location,
                'semester' => $semester,
                'academic_year' => $academicYear,
                'amount' => $formattedAmount,
                'scholar_id' => $scholar['scholar_id']
            ];

            Mail::send('emails.disbursement-notification', $emailData, function ($message) use ($scholar) {
                $message->to($scholar['email'])
                        ->subject('Disbursement Schedule - LYDO Scholarship');
            });
        }
        
        \Log::info('Disbursement notifications sent to ' . count($scholars) . ' scholars');
        return true;
        
    } catch (\Exception $e) {
        \Log::error('Failed to send disbursement notifications: ' . $e->getMessage());
        return false;
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

        $academicYear = $request->academic_year;
        $semester = $request->semester;

        // Get scholars who DON'T have disbursement for the selected academic year and semester
        $scholars = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->leftJoin('tbl_disburse as d', function($join) use ($academicYear, $semester) {
                $join->on('s.scholar_id', '=', 'd.scholar_id')
                     ->where('d.disburse_acad_year', $academicYear)
                     ->where('d.disburse_semester', $semester);
            })
            ->select(
                's.scholar_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                'a.applicant_brgy',
                'a.applicant_acad_year',
                DB::raw("CONCAT(a.applicant_fname, ' ', COALESCE(a.applicant_mname, ''), ' ', a.applicant_lname, ' ', COALESCE(a.applicant_suffix, '')) as full_name")
            )
            ->where('s.scholar_status', 'active')
            ->whereNull('d.disburse_id') // This ensures we only get scholars without disbursement for the selected year/semester
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

public function generateDisbursementRecordsPdf(Request $request)
{
    try {
        \Log::info('Disbursement Records PDF Request:', $request->all());
        
        set_time_limit(120);
        
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
                DB::raw("
                    CONCAT(
                        UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                        ', ',
                        UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                        ' ',
                        CASE 
                            WHEN a.applicant_mname IS NOT NULL AND a.applicant_mname != '' 
                                THEN CONCAT(UPPER(LEFT(a.applicant_mname,1)), '. ')
                            ELSE ''
                        END,
                        COALESCE(
                            CONCAT(
                                UPPER(LEFT(a.applicant_suffix,1)), LOWER(SUBSTRING(a.applicant_suffix,2))
                            ), ''
                        )
                    ) AS full_name
                ")
            )
            ->whereNull('d.disburse_signature'); // Only unsigned disbursements

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('a.applicant_fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('barangay')) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        if ($request->filled('semester')) {
            $query->where('d.disburse_semester', $request->semester);
        }

        // Get all records without limit
        $unsignedDisbursements = $query
            ->orderBy('a.applicant_lname', 'asc')
            ->orderBy('a.applicant_fname', 'asc')
            ->orderBy('a.applicant_mname', 'asc')
            ->get();

        \Log::info("Final unsigned disbursements count: {$unsignedDisbursements->count()}");

        // Get filter info - ALWAYS include this for all pages
        $filters = [];
        if ($request->filled('search')) $filters[] = 'Search: ' . $request->search;
        if ($request->filled('barangay')) $filters[] = 'Barangay: ' . $request->barangay;
        if ($request->filled('academic_year')) $filters[] = 'Academic Year: ' . $request->academic_year;
        if ($request->filled('semester')) $filters[] = 'Semester: ' . $request->semester;
        $filters[] = 'Status: Pending Signature';
        
        // Add record count to filters so it shows on all pages
        $filters[] = 'Total Records: ' . $unsignedDisbursements->count();

        $title = 'Disbursement Records Report';

        \Log::info("Generating PDF with {$unsignedDisbursements->count()} unsigned disbursements");

        $pdf = Pdf::loadView('pdf.disbursement-records-print', compact('unsignedDisbursements', 'filters', 'title'))
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream('disbursement-records-' . date('Y-m-d-H-i-s') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('Disbursement Records PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
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


public function generateMayorApplicantsPdf(Request $request)
{
    try {
        \Log::info('Mayor PDF Request:', $request->all());
        
        set_time_limit(120);
        
        // Get Mayor Staff applicants (Approved, Rejected, AND Reviewed)
        $query = DB::table('tbl_applicant as app')
            ->join('tbl_application as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->join('tbl_application_personnel as ap', 'a.application_id', '=', 'ap.application_id')
            ->select(
                'app.*',
                'ap.initial_screening',
                'ap.status',
                'ap.remarks'
            )
            ->whereIn('ap.initial_screening', ['Approved', 'Rejected', 'Reviewed']);

        // Apply filters only if they have values
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('app.applicant_fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $query->where('app.applicant_acad_year', $request->academic_year);
        }

        if ($request->filled('initial_screening') && $request->initial_screening !== 'all') {
            $query->where('ap.initial_screening', $request->initial_screening);
        }

        // Get all records without limit
        $applicants = $query
            ->orderBy('app.applicant_lname', 'asc')
            ->orderBy('app.applicant_fname', 'asc')
            ->orderBy('app.applicant_mname', 'asc')
            ->get();

        \Log::info("Final applicants count: {$applicants->count()}");

        // Get filter info - ALWAYS include this for all pages
        $filters = [];
        if ($request->filled('search')) $filters[] = 'Search: ' . $request->search;
        if ($request->filled('barangay')) $filters[] = 'Barangay: ' . $request->barangay;
        if ($request->filled('academic_year')) $filters[] = 'Academic Year: ' . $request->academic_year;
        if ($request->filled('initial_screening') && $request->initial_screening !== 'all') {
            $filters[] = 'Status: ' . $request->initial_screening;
        } else {
            $filters[] = 'Status: All Mayor Staff Applicants (Approved, Rejected, Reviewed)';
        }
        
        // Add record count to filters so it shows on all pages
        $filters[] = 'Total Records: ' . $applicants->count();

        $title = 'Mayor Staff Applicants Report';

        \Log::info("Generating PDF with {$applicants->count()} applicants");

        $pdf = Pdf::loadView('pdf.mayor-applicants-print', compact('applicants', 'filters', 'title'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream('mayor-applicants-' . date('Y-m-d-H-i-s') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('Mayor PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}
public function generateLydoApplicantsPdf(Request $request)
{
    try {
        \Log::info('LYDO PDF Request:', $request->all());
        
        set_time_limit(120);
        
        // Get LYDO Reviewed applicants (ONLY Reviewed)
        $query = DB::table('tbl_applicant as app')
            ->join('tbl_application as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->join('tbl_application_personnel as ap', 'a.application_id', '=', 'ap.application_id')
            ->select(
                'app.*',
                'ap.initial_screening',
                'ap.status',
                'ap.remarks'
            )
            ->where('ap.initial_screening', 'Reviewed');

        // Apply filters
        if ($request->filled('remarks')) {
            $query->where('ap.remarks', $request->remarks);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('app.applicant_fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('app.applicant_email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('barangay')) {
            $query->where('app.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $query->where('app.applicant_acad_year', $request->academic_year);
        }

        // Get all records without limit
        $applicants = $query
            ->orderBy('app.applicant_lname', 'asc')
            ->orderBy('app.applicant_fname', 'asc')
            ->orderBy('app.applicant_mname', 'asc')
            ->get();

        \Log::info("Final applicants count: {$applicants->count()}");

        // Get filter info - ALWAYS include this for all pages
        $filters = [];
        if ($request->filled('search')) $filters[] = 'Search: ' . $request->search;
        if ($request->filled('barangay')) $filters[] = 'Barangay: ' . $request->barangay;
        if ($request->filled('academic_year')) $filters[] = 'Academic Year: ' . $request->academic_year;
        $filters[] = 'Status: Reviewed by LYDO';
        if ($request->filled('remarks')) {
            $filters[] = 'Remarks: ' . $request->remarks;
        }
        
        // Add record count to filters so it shows on all pages
        $filters[] = 'Total Records: ' . $applicants->count();

        $title = 'LYDO Reviewed Applicants Report';

        \Log::info("Generating PDF with {$applicants->count()} applicants");

        $pdf = Pdf::loadView('pdf.lydo-applicants-print', compact('applicants', 'filters', 'title'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream('lydo-reviewed-applicants-' . date('Y-m-d-H-i-s') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('LYDO PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}

public function dashboardData(Request $request)
{
    $currentAcademicYear = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->orderBy('applicant_acad_year', 'desc')
        ->value('applicant_acad_year');

    if (!$currentAcademicYear) {
        $currentAcademicYear = date('Y') . '-' . (date('Y') + 1);
    }

    $totalApplicants = DB::table('tbl_applicant')
        ->where('applicant_acad_year', $currentAcademicYear)
        ->count();

    $totalScholarsWholeYear = DB::table('tbl_scholar')
        ->where('scholar_status', 'active')
        ->count();



    $inactiveScholars = DB::table('tbl_scholar')
        ->where('scholar_status', 'inactive')
        ->count();

    $graduatedScholars = DB::table('tbl_scholar')
        ->where('scholar_status', 'graduated')
        ->count();

    $barangayDistribution = DB::table('tbl_applicant')
        ->select('applicant_brgy', DB::raw('COUNT(*) as count'))
        ->where('applicant_acad_year', $currentAcademicYear)
        ->groupBy('applicant_brgy')
        ->orderBy('count', 'desc')
        ->limit(10)
        ->get();

    $schoolDistribution = DB::table('tbl_applicant')
        ->select('applicant_school_name', DB::raw('COUNT(*) as count'))
        ->where('applicant_acad_year', $currentAcademicYear)
        ->groupBy('applicant_school_name')
        ->orderBy('count', 'desc')
        ->limit(10)
        ->get();

    $scholarStatsPerYear = $this->getScholarStatisticsPerYear();

    return response()->json([
        'totalApplicants' => $totalApplicants,
        'totalScholarsWholeYear' => $totalScholarsWholeYear,
        'inactiveScholars' => $inactiveScholars,
        'graduatedScholars' => $graduatedScholars,
        'currentAcademicYear' => $currentAcademicYear,
        'barangayDistribution' => $barangayDistribution,
        'schoolDistribution' => $schoolDistribution,
        'scholarStatsPerYear' => $scholarStatsPerYear
    ]);
}
public function generateSignedDisbursementPdf(Request $request)
{
    try {
        \Log::info('Signed Disbursement PDF Request:', $request->all());
        
        set_time_limit(120);
        
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
                DB::raw("
                    CONCAT(
                        UPPER(LEFT(a.applicant_lname,1)), LOWER(SUBSTRING(a.applicant_lname,2)),
                        ', ',
                        UPPER(LEFT(a.applicant_fname,1)), LOWER(SUBSTRING(a.applicant_fname,2)),
                        ' ',
                        CASE 
                            WHEN a.applicant_mname IS NOT NULL AND a.applicant_mname != '' 
                                THEN CONCAT(UPPER(LEFT(a.applicant_mname,1)), '. ')
                            ELSE ''
                        END,
                        COALESCE(
                            CONCAT(
                                UPPER(LEFT(a.applicant_suffix,1)), LOWER(SUBSTRING(a.applicant_suffix,2))
                            ), ''
                        )
                    ) AS full_name
                ")
            )
            ->whereNotNull('d.disburse_signature'); // Only signed disbursements

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('a.applicant_fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('a.applicant_email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('barangay')) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->filled('academic_year')) {
            $query->where('d.disburse_acad_year', $request->academic_year);
        }

        if ($request->filled('semester')) {
            $query->where('d.disburse_semester', $request->semester);
        }

        // Get all records without limit
        $signedDisbursements = $query
            ->orderBy('a.applicant_lname', 'asc')
            ->orderBy('a.applicant_fname', 'asc')
            ->orderBy('a.applicant_mname', 'asc')
            ->get();

        \Log::info("Final signed disbursements count: {$signedDisbursements->count()}");

        // Get filter info - ALWAYS include this for all pages
        $filters = [];
        if ($request->filled('search')) $filters[] = 'Search: ' . $request->search;
        if ($request->filled('barangay')) $filters[] = 'Barangay: ' . $request->barangay;
        if ($request->filled('academic_year')) $filters[] = 'Academic Year: ' . $request->academic_year;
        if ($request->filled('semester')) $filters[] = 'Semester: ' . $request->semester;
        $filters[] = 'Status: Signed';
        
        // Add record count to filters so it shows on all pages
        $filters[] = 'Total Records: ' . $signedDisbursements->count();

        $title = 'Signed Disbursements Report';

        \Log::info("Generating PDF with {$signedDisbursements->count()} signed disbursements");

        $pdf = Pdf::loadView('pdf.signed-disbursement-print', compact('signedDisbursements', 'filters', 'title'))
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->stream('signed-disbursement-report-' . date('Y-m-d-H-i-s') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('Signed Disbursement PDF Generation Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}

public function getApplicantDocuments($applicantId)
{
    try {
        $applicant = DB::table('tbl_applicant as app')
            ->join('tbl_application as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->where('app.applicant_id', $applicantId)
            ->select(
                'a.application_letter',
                'a.cert_of_reg',
                'a.grade_slip',
                'a.brgy_indigency',
                'a.student_id'
            )
            ->first();

        if (!$applicant) {
            return response()->json(['success' => false, 'message' => 'Applicant not found.'], 404);
        }

        $documents = [
            'doc_application_letter' => $applicant->application_letter ? asset('storage/' . $applicant->application_letter) : null,
            'doc_cert_reg' => $applicant->cert_of_reg ? asset('storage/' . $applicant->cert_of_reg) : null,
            'doc_grade_slip' => $applicant->grade_slip ? asset('storage/' . $applicant->grade_slip) : null,
            'doc_brgy_indigency' => $applicant->brgy_indigency ? asset('storage/' . $applicant->brgy_indigency) : null,
            'doc_student_id' => $applicant->student_id ? asset('storage/' . $applicant->student_id) : null,
        ];

        return response()->json([
            'success' => true,
            'documents' => $documents
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting applicant documents: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error loading documents.'], 500);
    }
}

public function getApplicationPersonnelId($applicantId)
{
    try {
        $applicationPersonnel = DB::table('tbl_application as a')
            ->join('tbl_application_personnel as ap', 'a.application_id', '=', 'ap.application_id')
            ->where('a.applicant_id', $applicantId)
            ->select('ap.application_personnel_id')
            ->first();

        if (!$applicationPersonnel) {
            return response()->json([
                'success' => false, 
                'message' => 'Application personnel record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'application_personnel_id' => $applicationPersonnel->application_personnel_id
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting application personnel ID: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Error loading application data.'
        ], 500);
    }
}

  public function getIntakeSheet($applicationPersonnelId)
    {
        try {
            \Log::info("=== START INTAKE SHEET DEBUG ===");
            \Log::info("Application Personnel ID: " . $applicationPersonnelId);

            // Get basic applicant data
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
                return response()->json(['success' => false, 'message' => 'Application not found.'], 404);
            }

            $fullName = trim(
                ($appRow->applicant_fname ?? '') .
                ' ' . ($appRow->applicant_mname ? $appRow->applicant_mname . ' ' : '') .
                ($appRow->applicant_lname ?? '') .
                ($appRow->applicant_suffix ? ', ' . $appRow->applicant_suffix : '')
            );

            $documentStatuses = [
                'application_letter' => $this->evaluateDocumentStatus($appRow->application_letter),
                'cert_reg' => $this->evaluateDocumentStatus($appRow->cert_of_reg),
                'grade_slip' => $this->evaluateDocumentStatus($appRow->grade_slip),
                'brgy_indigency' => $this->evaluateDocumentStatus($appRow->brgy_indigency),
                'student_id' => $this->evaluateDocumentStatus($appRow->student_id),
            ];

            // Check if intake sheet exists
            $intakeSheet = FamilyIntakeSheet::where('application_personnel_id', $applicationPersonnelId)->first();

            \Log::info("Intake Sheet Found: " . ($intakeSheet ? 'Yes' : 'No'));

            if (!$intakeSheet) {
                \Log::info("No intake sheet found for ID: " . $applicationPersonnelId);
                
                $data = [
                    'applicant_name' => $fullName,
                    'applicant_gender' => $appRow->applicant_gender ?? null,
                    'remarks' => $appRow->remarks ?? null,
                    'head_barangay' => $appRow->applicant_brgy ?? null,
                    
                    // Document paths
                    'doc_application_letter' => $appRow->application_letter ? asset('storage/' . $appRow->application_letter) : null,
                    'doc_cert_reg' => $appRow->cert_of_reg ? asset('storage/' . $appRow->cert_of_reg) : null,
                    'doc_grade_slip' => $appRow->grade_slip ? asset('storage/' . $appRow->grade_slip) : null,
                    'doc_brgy_indigency' => $appRow->brgy_indigency ? asset('storage/' . $appRow->brgy_indigency) : null,
                    'doc_student_id' => $appRow->student_id ? asset('storage/' . $appRow->student_id) : null,
                    
                    // Empty arrays - using frontend expected field names
                    'family_members' => [],
                    'rv_service_records' => [],
                ];

                return response()->json([
                    'success' => true, 
                    'intakeSheet' => $data,
                    'family_members' => [],
                    'rv_service_records' => []
                ], 200);
            }

            // DEBUG: Check the actual database values
            \Log::info("=== DATABASE VALUES DEBUG ===");
            \Log::info("Family Members DB Value: " . ($intakeSheet->family_members ?? 'NULL'));
            \Log::info("Social Service Records DB Value: " . ($intakeSheet->social_service_records ?? 'NULL'));
            \Log::info("RV Service Records DB Value: " . ($intakeSheet->rv_service_records ?? 'NULL'));

            // Enhanced JSON parsing with better NULL handling
            $familyMembers = $this->parseJsonField($intakeSheet->family_members, 'family_members');
            
            // Use rv_service_records instead of social_service_records
            $socialServiceRecords = $this->parseJsonField($intakeSheet->rv_service_records, 'rv_service_records');

            // CORRECTED TRANSFORMATION: Use lowercase field names that match frontend expectations
            $transformedFamilyMembers = [];
            foreach ($familyMembers as $member) {
                $transformedFamilyMembers[] = [
                    'name' => $member['name'] ?? $member['NAME'] ?? '-',
                    'relationship' => $member['relationship'] ?? $member['relation'] ?? $member['RELATION'] ?? '-',
                    'birthdate' => $member['birthdate'] ?? $member['BIRTHDATE'] ?? $member['birth_date'] ?? '-',
                    'age' => $member['age'] ?? $member['AGE'] ?? '-',
                    'sex' => $member['sex'] ?? $member['gender'] ?? $member['SEX'] ?? '-',
                    'civil_status' => $member['civil_status'] ?? $member['CIVIL STATUS'] ?? $member['civilStatus'] ?? '-',
                    'education' => $member['education'] ?? $member['EDUCATIONAL ATTAINMENT'] ?? $member['educational_attainment'] ?? '-',
                    'occupation' => $member['occupation'] ?? $member['OCCUPATION'] ?? '-',
                    'monthly_income' => $member['monthly_income'] ?? $member['income'] ?? $member['INCOME'] ?? '-',
                    'remarks' => $member['remarks'] ?? $member['REMARKS'] ?? '-'
                ];
            }

            $transformedServiceRecords = [];
            foreach ($socialServiceRecords as $record) {
                $transformedServiceRecords[] = [
                    'date' => $record['date'] ?? $record['DATE'] ?? '-',
                    'problem' => $record['problem'] ?? $record['PROBLEM/NEED'] ?? $record['problem_need'] ?? '-',
                    'action' => $record['action'] ?? $record['ACTION/ASSISTANCE GIVEN'] ?? $record['action_assistance'] ?? '-',
                    'remarks' => $record['remarks'] ?? $record['REMARKS'] ?? '-'
                ];
            }

            \Log::info("Final Counts - Family Members: " . count($transformedFamilyMembers) . ", Social Service Records: " . count($transformedServiceRecords));

            // FORMAT HOUSE AND LOT INFORMATION WITH RENT AMOUNTS
            $houseDisplay = $intakeSheet->house_house ?? null;
            $lotDisplay = $intakeSheet->house_lot ?? null;

            // If house is rented, combine with house_rent amount
            if ($houseDisplay === 'Rent' && !empty($intakeSheet->house_rent)) {
                $houseDisplay = 'Rent - ₱' . number_format($intakeSheet->house_rent, 2);
            }

            // If lot is rented, combine with lot_rent amount
            if ($lotDisplay === 'Rent' && !empty($intakeSheet->lot_rent)) {
                $lotDisplay = 'Rent - ₱' . number_format($intakeSheet->lot_rent, 2);
            }

            // Prepare response data - CORRECTED STRUCTURE
            $data = [
                // Applicant Information
                'applicant_name' => $fullName,
                'applicant_gender' => $appRow->applicant_gender ?? null,
                'remarks' => $appRow->remarks ?? null,
                
                // Head of Family Information
                'head_4ps' => $intakeSheet->head_4ps ?? null,
                'head_ipno' => $intakeSheet->head_ipno ?? null,
                'head_address' => $intakeSheet->head_address ?? null,
                'head_zone' => $intakeSheet->head_zone ?? null,
                'head_barangay' => $intakeSheet->head_barangay ?? $appRow->applicant_brgy ?? null,
                'head_pob' => $intakeSheet->head_pob ?? null,
                'head_dob' => $intakeSheet->head_dob ? (string)$intakeSheet->head_dob : null,
                'head_educ' => $intakeSheet->head_educ ?? null,
                'head_occ' => $intakeSheet->head_occ ?? null,
                'head_religion' => $intakeSheet->head_religion ?? null,
                
                // Household Information
                'serial_number' => $intakeSheet->serial_number ?? null,
                'house_total_income' => $intakeSheet->house_total_income ?? null,
                'house_net_income' => $intakeSheet->house_net_income ?? null,
                'other_income' => $intakeSheet->other_income ?? null,
                'house_house' => $houseDisplay,
                'house_lot' => $lotDisplay,
                'house_electric' => $intakeSheet->house_electric ?? null,
                'house_water' => $intakeSheet->house_water ?? null,
                
                // CORRECTED: Use the exact field names expected by frontend
                'family_members' => $transformedFamilyMembers,
                'rv_service_records' => $transformedServiceRecords,
                
                // Signatures
                'worker_name' => $intakeSheet->worker_name ?? null,
                'officer_name' => $intakeSheet->officer_name ?? null,
                'date_entry' => $intakeSheet->date_entry ? (string)$intakeSheet->date_entry : null,
               
                // Document paths
                'doc_application_letter' => $appRow->application_letter ? asset('storage/' . $appRow->application_letter) : null,
                'doc_cert_reg' => $appRow->cert_of_reg ? asset('storage/' . $appRow->cert_of_reg) : null,
                'doc_grade_slip' => $appRow->grade_slip ? asset('storage/' . $appRow->grade_slip) : null,
                'doc_brgy_indigency' => $appRow->brgy_indigency ? asset('storage/' . $appRow->brgy_indigency) : null,
                'doc_student_id' => $appRow->student_id ? asset('storage/' . $appRow->student_id) : null,

                // Raw values for reference
                'house_rent' => $intakeSheet->house_rent ?? null,
                'lot_rent' => $intakeSheet->lot_rent ?? null,
            ];

            \Log::info("=== END INTAKE SHEET DEBUG ===");

            return response()->json([
                'success' => true, 
                'intakeSheet' => $data,
                // ADD THESE FIELDS FOR FRONTEND COMPATIBILITY
                'family_members' => $transformedFamilyMembers,
                'rv_service_records' => $transformedServiceRecords,
                'debug_info' => [
                    'family_members_count' => count($transformedFamilyMembers),
                    'social_service_records_count' => count($transformedServiceRecords),
                    'has_intake_sheet' => true,
                    'family_members_original' => $familyMembers,
                    'social_service_records_original' => $socialServiceRecords
                ]
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error in getIntakeSheet: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error loading intake sheet data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Evaluate document status based on your criteria
     */
    private function evaluateDocumentStatus($documentPath)
    {
        if (!$documentPath) {
            return 'Missing';
        }
        
        // Add your evaluation logic here
        // This is a placeholder - replace with your actual evaluation criteria
        $fileExists = Storage::exists($documentPath);
        
        if (!$fileExists) {
            return 'Bad';
        }
        
        // You can add more sophisticated checks here
        // For example: file size, format, content validation, etc.
        
        return 'Good';
    }

    /**
     * Helper method to parse JSON fields with comprehensive error handling
     * FIXED: Handles double/triple escaped JSON strings
     */
    private function parseJsonField($fieldValue, $fieldName)
    {
        if ($fieldValue === null) {
            \Log::info("Field '{$fieldName}' is NULL, returning empty array");
            return [];
        }

        if (empty(trim($fieldValue))) {
            \Log::info("Field '{$fieldName}' is empty string, returning empty array");
            return [];
        }

        if ($fieldValue === 'null' || $fieldValue === 'NULL') {
            \Log::info("Field '{$fieldName}' is string 'null', returning empty array");
            return [];
        }

        try {
            // FIX: Remove extra backslashes from escaped JSON
            $cleanedValue = $fieldValue;
            
            // Handle double/triple escaped JSON (common when storing JSON in databases)
            if (str_contains($cleanedValue, '\\"')) {
                // Remove extra backslashes - this fixes the "\\"issue
                $cleanedValue = stripslashes($cleanedValue);
                
                // If still has backslashes, do one more pass
                if (str_contains($cleanedValue, '\\"')) {
                    $cleanedValue = stripslashes($cleanedValue);
                }
            }

            // Debug the cleaning process
            \Log::info("Field '{$fieldName}' cleaning:", [
                'original' => $fieldValue,
                'cleaned' => $cleanedValue,
                'original_length' => strlen($fieldValue),
                'cleaned_length' => strlen($cleanedValue)
            ]);

            $decoded = json_decode($cleanedValue, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning("JSON decode error for {$fieldName}: " . json_last_error_msg());
                \Log::warning("Raw value that failed to decode: " . $fieldValue);
                \Log::warning("Cleaned value that failed: " . $cleanedValue);
                
                // FIX: Try one more approach - manual cleanup for stubborn cases
                if (str_contains($cleanedValue, '\\"')) {
                    $cleanedValue = str_replace('\\"', '"', $cleanedValue);
                    $decoded = json_decode($cleanedValue, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        \Log::warning("Second attempt also failed for {$fieldName}");
                        return [];
                    }
                } else {
                    return [];
                }
            }

            if (!is_array($decoded)) {
                \Log::warning("Decoded {$fieldName} is not an array, type: " . gettype($decoded));
                return [];
            }

            \Log::info("Successfully decoded {$fieldName}, count: " . count($decoded));
            return $decoded;

        } catch (\Exception $e) {
            \Log::error("Exception decoding {$fieldName}: " . $e->getMessage());
            \Log::error("Field value that caused exception: " . $fieldValue);
            return [];
        }
    }



public function deleteStaff($id)
{
    try {
        $staff = DB::table('tbl_lydopers')->where('lydopers_id', $id)->first();
        
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff member not found.'], 404);
        }

        DB::table('tbl_lydopers')->where('lydopers_id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Staff member deleted successfully.']);
        
    } catch (\Exception $e) {
        \Log::error('Error deleting staff: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to delete staff member.'], 500);
    }
}
public function printApplicationHistory($application_personnel_id)
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

// ENHANCED HELPER METHODS
private function safeNumberFormat($value)
{
    if (empty($value) || $value === '' || $value === null) {
        return '';
    }
    
    // If it's already a formatted string with commas, return as is
    if (is_string($value) && preg_match('/^\d{1,3}(,\d{3})*(\.\d{2})?$/', $value)) {
        return $value;
    }
    
    // If it's numeric, format it
    if (is_numeric($value)) {
        return number_format(floatval($value), 2);
    }
    
    // If it's a string that can be converted to number
    $cleanValue = str_replace(',', '', $value);
    if (is_numeric($cleanValue)) {
        return number_format(floatval($cleanValue), 2);
    }
    
    return $value; // Return as-is if not numeric
}

private function safeConvertToFloat($value)
{
    if (empty($value) || $value === '' || $value === null) {
        return 0;
    }
    
    if (is_numeric($value)) {
        return floatval($value);
    }
    
    $cleanValue = str_replace(',', '', $value);
    if (is_numeric($cleanValue)) {
        return floatval($cleanValue);
    }
    
    return 0;
}
// In your LydoAdminController or wherever you send schedule emails
private function sendScheduleNotification($applicantEmail, $applicantName, $scheduleData)
{
    try {
        $emailData = [
            'applicantName' => $applicantName, // ADD THIS LINE
            'scheduleData' => $scheduleData
        ];

        Mail::send('emails.schedule-notification', $emailData, function ($message) use ($applicantEmail, $applicantName) {
            $message->to($applicantEmail)
                    ->subject('Schedule Notification - LYDO Scholarship Program');
        });

        \Log::info("Schedule notification email sent to: {$applicantEmail}");
        return true;
        
    } catch (\Exception $e) {
        \Log::error("Failed to send schedule email to {$applicantEmail}: " . $e->getMessage());
        return false;
    }
}
}

