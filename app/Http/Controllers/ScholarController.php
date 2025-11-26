<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationPersonnel;
use App\Models\Lydopers;
use App\Models\Scholar;
use App\Models\Announce;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Events\ApplicantRegistered;
use App\Events\ApplicantUpdated;

class ScholarController extends Controller
{
    public function showApplicantsRegForm()
    {
        return view('scholar.applicants_reg');
    }

    // Show scholar settings form
    public function showSettings()
    {
        $scholar = session('scholar');
        if (!$scholar) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'Please login to access settings.']);
        }
        $scholar->load('applicant');
        return view('scholar.scholar_setting', compact('scholar'));
    }

public function updateSettings(Request $request)
{
    $scholar = session('scholar');
    if (!$scholar) {
        return redirect()->route('scholar.login')->withErrors(['error' => 'Please login to update settings.']);
    }
    $scholar->load('applicant');

    $request->validate([
        'applicant_fname' => 'required|string|max:255',
        'applicant_mname' => 'nullable|string|max:255',
        'applicant_lname' => 'required|string|max:255',
        'applicant_suffix' => 'nullable|string|max:10',
        'applicant_bdate' => 'required|date|before:today',
        'applicant_civil_status' => 'required|in:single,married,widowed,divorced',
        'applicant_brgy' => 'required|string|max:255',
        'applicant_email' => 'required|email|unique:tbl_applicant,applicant_email,' . $scholar->applicant->applicant_id . ',applicant_id',
        'applicant_contact_number' => 'required|string|max:15',
        'applicant_school_name' => 'required|string|max:255',
        'password' => 'nullable|string|min:8|confirmed',
        'current_password' => 'required_with:password|string',
    ]);

    $applicant = $scholar->applicant;

    $applicant->applicant_fname = $request->input('applicant_fname');
    $applicant->applicant_mname = $request->input('applicant_mname');
    $applicant->applicant_lname = $request->input('applicant_lname');
    $applicant->applicant_suffix = $request->input('applicant_suffix');
    $applicant->applicant_bdate = $request->input('applicant_bdate');
    $applicant->applicant_civil_status = $request->input('applicant_civil_status');
    $applicant->applicant_brgy = $request->input('applicant_brgy');
    $applicant->applicant_email = $request->input('applicant_email');
    $applicant->applicant_contact_number = $request->input('applicant_contact_number');
    $applicant->applicant_school_name = $request->input('applicant_school_name');
    $applicant->save();

    // Update password if provided (verify current password first)
    if ($request->filled('password')) {
        if (!\Illuminate\Support\Facades\Hash::check($request->input('current_password'), $scholar->scholar_pass)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $scholar->scholar_pass = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        $scholar->save();

        // Refresh session scholar object
        $scholar->load('applicant');
        session(['scholar' => $scholar]);
    }

    return redirect()->back()->with('success', 'Settings updated successfully.');
}

        public function checkEmail(Request $request)
        {
            \Log::info('=== APPLICANT EMAIL CHECK ===');
            \Log::info('Email being checked: ' . $request->email);
            
            $exists = Applicant::where('applicant_email', $request->email)->exists();
            
            \Log::info('Exists in applicants table: ' . ($exists ? 'YES' : 'NO'));
            \Log::info('============================');
            
            return response()->json(['exists' => $exists]);
        }

    public function showLoginForm()
    {
        // Removed announcements fetching as it is no longer needed in login view
        return view('scholar.scholar_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'scholar_username' => 'required|string|regex:/^[a-zA-Z0-9]+$/',
            'scholar_pass' => 'required|string',
        ]);

        $scholar = Scholar::where('scholar_username', '=', $request->input('scholar_username'))->first();

        if (!$scholar) {
            return back()->withInput()->withErrors(['scholar_username' => 'Invalid username.']);
        }

        if (!Hash::check($request->scholar_pass, $scholar->scholar_pass)) {
            return back()->withInput()->withErrors(['scholar_pass' => 'Incorrect password.']);
        }

        if ($scholar->scholar_status !== 'Active') {
            return back()->withInput()->withErrors(['error' => 'That account is inactive.'])->with('showInactiveAlert', true);
        }

        // Store scholar in session with relationships
        $scholar->load('applicant');
        session(['scholar' => $scholar]);

        // Redirect to scholar dashboard
        return redirect()->route('scholar.dashboard')->with('success', 'Login successful.');
    }
    public function showScholarRegistration(Request $request)
{
    // Get scholar ID from signed URL parameters
    $scholarId = $request->scholar_id;

    if (!$scholarId) {
        return redirect()->route('scholar.login')->withErrors([
            'error' => 'Invalid registration link.'
        ]);
    }

    // Check if scholar exists in DB
    $scholar = \App\Models\Scholar::where('scholar_id', $scholarId)->first();

    if (!$scholar) {
        return redirect()->route('scholar.login')->withErrors([
            'error' => 'Invalid registration link.'
        ]);
    }

    // Check if username and password are already set
    // Only allow registration if credentials are not set OR are default values
    if ($scholar->scholar_username && $scholar->scholar_pass) {
        // If both username and password exist and username is not default, prevent registration
        if ($scholar->scholar_username !== 'default_username') {
            return redirect()->route('scholar.login')->withErrors([
                'error' => 'Account already activated.'
            ])->with('showRegistrationError', true);
        }
        // If username is 'default_username' but password exists, still allow registration
        // This handles cases where password was set but username is still default
    }

    // If scholar exists and credentials not set, load registration form
    return view('scholar.scholar_registration', compact('scholar'));
}

 public function storeApplicantsReg(Request $request)
    {
        // ✅ Validation
        $request->validate([
            'applicant_fname' => 'required|string|max:255',
            'applicant_mname' => 'nullable|string|max:255',
            'applicant_lname' => 'required|string|max:255',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'required|in:male,female,other',
            'applicant_bdate' => 'required|date|before:today',
            'applicant_civil_status' => 'required|in:single,married,widowed,divorced',
            'applicant_brgy' => 'required|string|max:255',
            'applicant_email' => 'required|email|unique:tbl_applicant,applicant_email',
            'applicant_contact_number' => 'required|string|max:15',
            'applicant_school_name' => 'required|string|max:255',
            'applicant_school_name_other' => 'nullable|string|max:255',
            'applicant_year_level' => 'required|string|max:50',
            'applicant_course' => 'required|string|max:255',
            'applicant_acad_year' => 'required|string|max:20',
            'application_letter' => 'required|file|mimes:pdf|max:5120',
            'certificate_of_registration' => 'required|file|mimes:pdf|max:5120',
            'grade_slip' => 'required|file|mimes:pdf|max:5120',
            'barangay_indigency' => 'required|file|mimes:pdf|max:5120',
            'student_id' => 'required|file|mimes:pdf|max:5120',
        ]);

        // ✅ Determine school name
        $schoolName = $request->applicant_school_name === 'Others'
            ? $request->applicant_school_name_other
            : $request->applicant_school_name;

        // ✅ Create applicant record
        $applicant = Applicant::create([
            'applicant_fname' => $request->applicant_fname,
            'applicant_mname' => $request->applicant_mname,
            'applicant_lname' => $request->applicant_lname,
            'applicant_suffix' => $request->applicant_suffix,
            'applicant_gender' => $request->applicant_gender,
            'applicant_bdate' => $request->applicant_bdate,
            'applicant_civil_status' => $request->applicant_civil_status,
            'applicant_brgy' => $request->applicant_brgy,
            'applicant_email' => $request->applicant_email,
            'applicant_contact_number' => $request->applicant_contact_number,
            'applicant_school_name' => $schoolName,
            'applicant_year_level' => $request->applicant_year_level,
            'applicant_course' => $request->applicant_course,
            'applicant_acad_year' => $request->applicant_acad_year,
        ]);

        // ✅ Store PDF documents in storage/documents (folder already exists)
        $applicationData = [
            'applicant_id' => $applicant->applicant_id,
            'application_letter' => $this->moveFileToStorage($request->file('application_letter')),
            'cert_of_reg' => $this->moveFileToStorage($request->file('certificate_of_registration')),
            'grade_slip' => $this->moveFileToStorage($request->file('grade_slip')),
            'brgy_indigency' => $this->moveFileToStorage($request->file('barangay_indigency')),
            'student_id' => $this->moveFileToStorage($request->file('student_id')),
            'date_submitted' => now(),
        ];

        // ✅ Create application record
        $application = Application::create($applicationData);

        // ✅ Assign to Mayor’s staff
        $mayorStaff = Lydopers::where('lydopers_role', 'mayor_staff')->first();
        if (!$mayorStaff) {
            return redirect()->back()->withErrors(['error' => 'Mayor staff not found.']);
        }

        ApplicationPersonnel::create([
            'application_id' => $application->application_id,
            'lydopers_id' => $mayorStaff->lydopers_id,
            'initial_screening' => 'Pending',
            'remarks' => 'Waiting',
            'status' => 'Waiting',
        ]);

        // Broadcast new applicant registration
        $currentAcadYear = DB::table("tbl_applicant")
            ->select("applicant_acad_year")
            ->orderBy("applicant_acad_year", "desc")
            ->value("applicant_acad_year");

        $applicantsCurrentYear = $currentAcadYear ? DB::table("tbl_applicant")
            ->where("applicant_acad_year", $currentAcadYear)
            ->count() : 0;

        broadcast(new ApplicantRegistered('total_applicants', $applicantsCurrentYear))->toOthers();

        return redirect()->route('home')->with('success', 'Application submitted successfully!');
    }

    /**
     * ✅ Helper: Move uploaded files into storage/documents/
     */
    private function moveFileToStorage($file)
    {
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $file->move(storage_path('documents'), $fileName);
        return 'documents/' . $fileName;
    }

    public function registerScholar(Request $request)
    {
        $request->validate([
            'scholar_id' => 'required|exists:tbl_scholar,scholar_id',
            'scholar_username' => 'required|string|regex:/^[a-zA-Z0-9]+$/|unique:tbl_scholar,scholar_username',
            'scholar_pass' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'confirm_password' => 'required|string|same:scholar_pass',
        ]);

        $scholar = Scholar::find($request->scholar_id);

        // Additional validation: Check if scholar already has valid credentials
        if ($scholar->scholar_username && $scholar->scholar_pass && $scholar->scholar_username !== 'default_username') {
            return redirect()->route('scholar.login')->withErrors([
                'error' => 'Account already activated. Please use your existing credentials to login.'
            ])->with('showRegistrationError', true);
        }

        $scholar->update([
            'scholar_username' => $request->scholar_username,
            'scholar_pass' => Hash::make($request->scholar_pass),
            'date_activated' => now(),
            'scholar_status' => 'Active',
        ]);

        return redirect()->route('scholar.login')->with('success', 'Account created successfully. You can now log in.');
    }

    public function checkUsername(Request $request)
    {
        $exists = Scholar::where('scholar_username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkScholarId(Request $request)
    {
        $scholarId = $request->scholar_id;
        $scholar = Scholar::where('scholar_id', $scholarId)->first();

        if (!$scholar) {
            return response()->json(['valid' => false, 'message' => 'Invalid Scholar ID.']);
        }

        if ($scholar->scholar_username && $scholar->scholar_pass) {
            return response()->json(['valid' => false, 'message' => 'this code is already used']);
        }

        return response()->json(['valid' => true]);
    }

public function announcements()
{
    // Kunin lamang ang announcements na may type na "Applicants"
    $announcements = Announce::where('announce_type', 'Applicants')
        ->orderBy('created_at', 'desc')
        ->orderBy('announce_id', 'desc')
        ->get();
    
    return view('scholar.scholar_announce', compact('announcements'));
}

    public function dashboard()
    {
        $announcements = Announce::where('announce_type', 'Scholars')->orderBy('date_posted', 'desc')->get();
        return view('scholar.scholar_dash', compact('announcements'));
    }

public function showRenewalApp()
{
    $scholar = session('scholar');
    $renewal = null;
    $approvedRenewalExists = false;
    $settings = \App\Models\Settings::first();
    $badDocuments = [];
    $canRenewForNextYear = false;

    if ($scholar) {
        // Ensure applicant relationship is loaded
        if (!$scholar->relationLoaded('applicant')) {
            $scholar->load('applicant');
        }

        // Get current academic year using new cutoff (July 10)
        $now = now();
        $academicYear = $this->getCurrentAcademicYear($now);

        // Get applicant's starting academic year
        $applicantStartYear = $scholar->applicant->applicant_acad_year;
        
        // Extract years from academic year strings
        $startYearParts = explode('-', $applicantStartYear);
        $currentYearParts = explode('-', $academicYear);
        
        $startYearInt = intval($startYearParts[0]);
        $currentYearInt = intval($currentYearParts[0]);
        
        // Scholar can renew if current academic year is DIFFERENT from start academic year
        // AND current academic year is GREATER than start academic year
        $canRenewForNextYear = ($academicYear !== $applicantStartYear) && ($currentYearInt > $startYearInt);

        // BLOCK RENEWAL: If current academic year matches applicant's starting academic year
        $blockedByStartDateYear = false;
        if ($settings && $settings->renewal_start_date) {
            try {
                // Get the academic year of the renewal start date
                $renewalStartAcademicYear = $this->getCurrentAcademicYear($settings->renewal_start_date);
                
                // Block if applicant's start year matches renewal start academic year
                $blockedByStartDateYear = ($applicantStartYear === $renewalStartAcademicYear);
                
                // ALSO block if current academic year matches applicant's starting academic year
                if ($academicYear === $applicantStartYear) {
                    $blockedByStartDateYear = true;
                }
                
                // If blocked, override canRenewForNextYear to false
                if ($blockedByStartDateYear) {
                    $canRenewForNextYear = false;
                }
            } catch (\Exception $e) {
                // If there's error parsing, just use the basic comparison
                if ($academicYear === $applicantStartYear) {
                    $blockedByStartDateYear = true;
                    $canRenewForNextYear = false;
                }
            }
        } else {
            // If no renewal start date setting, still block if current academic year matches start year
            if ($academicYear === $applicantStartYear) {
                $blockedByStartDateYear = true;
                $canRenewForNextYear = false;
            }
        }

        // Debug logging
        \Log::info('Academic Year Renewal Check:', [
            'applicant_start_year' => $applicantStartYear,
            'current_academic_year' => $academicYear,
            'renewal_start_academic_year' => $renewalStartAcademicYear ?? 'N/A',
            'can_renew' => $canRenewForNextYear,
            'blocked_by_start_date_year' => $blockedByStartDateYear,
            'current_date' => $now->format('Y-m-d')
        ]);

        // Rest of your existing code...
        $renewalSemester = $settings->renewal_semester ?? '1st Semester';

        $renewal = \App\Models\Renewal::where('scholar_id', $scholar->scholar_id)
            ->where('renewal_acad_year', $academicYear)
            ->where('renewal_semester', $renewalSemester)
            ->first();

        $approvedRenewalExists = \App\Models\Renewal::where('scholar_id', $scholar->scholar_id)
            ->where('renewal_acad_year', $academicYear)
            ->where('renewal_semester', $renewalSemester)
            ->where('renewal_status', 'Approved')
            ->exists();

        if ($renewal) {
            $badDocuments = [
                'renewal_cert_of_reg' => $renewal->cert_of_reg_status === 'bad',
                'renewal_grade_slip' => $renewal->grade_slip_status === 'bad',  
                'renewal_brgy_indigency' => $renewal->brgy_indigency_status === 'bad',
            ];
        }
    }

    return view('scholar.renewal_app', compact('renewal', 'settings', 'approvedRenewalExists', 'badDocuments', 'canRenewForNextYear', 'blockedByStartDateYear'));
}

// Add this helper method to determine current semester
private function getCurrentSemester()
{
    $month = now()->month;
    
    if ($month >= 1 && $month <= 5) {
        return '2nd Semester'; // January to May
    } elseif ($month >= 6 && $month <= 10) {
        return '1st Semester'; // June to October
    } else {
        return 'Summer'; // November to December
    }
}
public function submitRenewal(Request $request)
{
    // Check if this is an update or new submission
    $isUpdate = $request->has('renewal_id') && $request->renewal_id;

    if ($isUpdate) {
        // For updates, files are optional
        $request->validate([
            'renewal_semester' => 'required|string|max:20',
            'renewal_acad_year' => 'required|string|max:20',
            'applicant_year_level' => 'required|string|max:50',
            'renewal_cert_of_reg' => 'nullable|file|mimes:pdf|max:5120',
            'renewal_grade_slip' => 'nullable|file|mimes:pdf|max:5120',
            'renewal_brgy_indigency' => 'nullable|file|mimes:pdf|max:5120',
        ]);
    } else {
        // For new submissions, files are required
        $request->validate([
            'renewal_semester' => 'required|string|max:20',
            'renewal_acad_year' => 'required|string|max:20',
            'applicant_year_level' => 'required|string|max:50',
            'renewal_cert_of_reg' => 'required|file|mimes:pdf|max:5120',
            'renewal_grade_slip' => 'required|file|mimes:pdf|max:5120',
            'renewal_brgy_indigency' => 'required|file|mimes:pdf|max:5120',
        ]);
    }

    $scholar = session('scholar');
    if (!$scholar) {
        return redirect()->route('scholar.login')->withErrors(['error' => 'Please login to submit renewal.']);
    }

    // Check if scholar already has an APPROVED renewal for the same academic year AND semester
    $existingApprovedRenewal = \App\Models\Renewal::where('scholar_id', $scholar->scholar_id)
        ->where('renewal_acad_year', $request->renewal_acad_year)
        ->where('renewal_semester', $request->renewal_semester)
        ->where('renewal_status', 'Approved')
        ->exists();

    if ($existingApprovedRenewal && !$isUpdate) {
        return redirect()->back()->withErrors(['error' => 'You already have an approved renewal for ' . $request->renewal_semester . ' of Academic Year ' . $request->renewal_acad_year . '.']);
    }

    // Check renewal deadline
    $settings = \App\Models\Settings::first();
    $now = now();
    if ($settings && $settings->renewal_deadline) {
        if ($now->isAfter($settings->renewal_deadline)) {
            return redirect()->back()->withErrors(['error' => 'Renewal submission deadline has passed.']);
        }
    }
    if ($settings && $settings->renewal_start_date) {
        if ($now->isBefore($settings->renewal_start_date)) {
            return redirect()->back()->withErrors(['error' => 'Renewal submission has not started yet.']);
        }
    }

    // Update applicant year level - USE THE AUTO-POPULATED VALUE FROM THE FORM
    $applicant = $scholar->applicant;
    if (!$applicant) {
        return redirect()->back()->withErrors(['error' => 'Applicant record not found.']);
    }

    // Get the auto-populated year level from the form
    $newYearLevel = $request->input('applicant_year_level');
    
    // Update the applicant's year level in the database
    $applicant->applicant_year_level = $newYearLevel;
    $applicant->save();

    if ($isUpdate) {
        // Update existing renewal
        $renewal = \App\Models\Renewal::find($request->renewal_id);
        if (!$renewal) {
            return redirect()->back()->withErrors(['error' => 'Renewal record not found.']);
        }

        $renewal->renewal_semester = $request->input('renewal_semester');
        $renewal->renewal_acad_year = $request->input('renewal_acad_year');
        $renewal->date_submitted = now();
        $renewal->renewal_status = 'Pending';

        // SET STATUS FIELDS TO 'New' ONLY WHEN FILES ARE UPDATED (for re-review)
        if ($request->hasFile('renewal_cert_of_reg')) {
            $renewal->renewal_cert_of_reg = $this->moveFileToRenewals($request->file('renewal_cert_of_reg'));
            $renewal->cert_of_reg_status = 'New'; // Set to 'New' for re-review
        }
        if ($request->hasFile('renewal_grade_slip')) {
            $renewal->renewal_grade_slip = $this->moveFileToRenewals($request->file('renewal_grade_slip'));
            $renewal->grade_slip_status = 'New'; // Set to 'New' for re-review
        }
        if ($request->hasFile('renewal_brgy_indigency')) {
            $renewal->renewal_brgy_indigency = $this->moveFileToRenewals($request->file('renewal_brgy_indigency'));
            $renewal->brgy_indigency_status = 'New'; // Set to 'New' for re-review
        }

        $renewal->save();
        $message = 'Renewal application updated successfully. Year level updated to: ' . $newYearLevel;
    } else {
        // Create new renewal record
        $certOfRegPath = $this->moveFileToRenewals($request->file('renewal_cert_of_reg'));
        $gradeSlipPath = $this->moveFileToRenewals($request->file('renewal_grade_slip'));
        $brgyIndigencyPath = $this->moveFileToRenewals($request->file('renewal_brgy_indigency'));

        $renewal = new \App\Models\Renewal();
        $renewal->scholar_id = $scholar->scholar_id;
        $renewal->renewal_semester = $request->input('renewal_semester');
        $renewal->renewal_acad_year = $request->input('renewal_acad_year');
        $renewal->renewal_cert_of_reg = $certOfRegPath;
        $renewal->renewal_grade_slip = $gradeSlipPath;
        $renewal->renewal_brgy_indigency = $brgyIndigencyPath;
        $renewal->date_submitted = now();
        $renewal->renewal_status = 'Pending';
        
        // SET INITIAL STATUS AS 'pending' FOR NEW APPLICATIONS (not yet reviewed)
        $renewal->cert_of_reg_status = 'pending';
        $renewal->grade_slip_status = 'pending';
        $renewal->brgy_indigency_status = 'pending';
        
        $renewal->save();
        $message = 'Renewal application submitted successfully. Year level updated to: ' . $newYearLevel;
    }

    return redirect()->back()->with('success', $message);
}
/**
 * Move uploaded files into storage/renewals/
 */
/**
 * Move uploaded files into storage/renewals/
 */
private function moveFileToRenewals($file)
{
    $fileName = uniqid() . '_' . $file->getClientOriginalName();
    $destination = storage_path('renewals'); // storage/app/renewals
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true); // create folder if it doesn't exist
    }
    $file->move($destination, $fileName);
    return 'renewals/' . $fileName;
}


    // Show forgot password form
    public function showForgotPasswordForm() {
        return view('scholar.scholar_forgotpass');
    }

    // Send reset link email
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in tbl_applicant
        $applicant = Applicant::where('applicant_email', $request->email)->first();
        if (!$applicant) {
            return response()->json(['success' => false, 'message' => 'Email not found.']);
        }

        // Check if there is an application and scholar
        $application = Application::where('applicant_id', $applicant->applicant_id)->first();
        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No application found for this email.']);
        }

        $scholar = Scholar::where('application_id', $application->application_id)->first();
        if (!$scholar) {
            return response()->json(['success' => false, 'message' => 'No scholar account found for this email.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save to password_otps table
        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Send OTP Email
        Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($request){
            $message->to($request->email);
            $message->subject('LYDO Scholarship Password Reset OTP');
        });

        // Return JSON response for AJAX
        return response()->json(['success' => true, 'message' => 'OTP sent to your email!']);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $otpRecord = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>', Carbon::now()->subMinutes(10)) // OTP expires in 10 minutes
            ->first();

        if (!$otpRecord) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.']);
        }

        // Delete OTP after verification
        DB::table('password_otps')->where('email', $request->email)->delete();

        // Generate a temporary token for reset password
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        return response()->json(['success' => true, 'token' => $token]);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tbl_applicant,applicant_email',
        ]);

        // Generate new 6-digit OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update password_otps table
        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Send OTP Email
        Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($request){
            $message->to($request->email);
            $message->subject('LYDO Scholarship Password Reset OTP');
        });

        return response()->json(['success' => true, 'message' => 'New OTP sent to your email!']);
    }

    // Show reset password form
    public function showResetForm($token)
    {
        if (!$token) {
            return redirect()->route('scholar.login');
        }
        return view('scholar.scholar_resetpass', ['token' => $token]);
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ]);

        $reset = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'Invalid or expired reset link.']);
        }

        // Find the applicant
        $applicant = Applicant::where('applicant_email', $reset->email)->first();
        if (!$applicant) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'Applicant not found.']);
        }

        // Find the application
        $application = Application::where('applicant_id', $applicant->applicant_id)->first();
        if (!$application) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'Application not found.']);
        }

        // Find the scholar
        $scholar = Scholar::where('application_id', $application->application_id)->first();
        if (!$scholar) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'Scholar account not found.']);
        }

        // Update password
        $scholar->update(['scholar_pass' => Hash::make($request->password)]);

        // Delete token
        DB::table('password_resets')->where('email', $reset->email)->delete();

        return redirect()->route('scholar.login')->with('success', 'Your password has been reset successfully!');
    }


public function showUpdateApplication($applicant_id)
{
    $applicant = Applicant::findOrFail($applicant_id);
    $application = Application::where('applicant_id', $applicant_id)->first();
    
    if (!$application) {
        abort(404, 'Application not found.');
    }

    // Get application personnel record
    $applicationPersonnel = \DB::table('tbl_application_personnel')
        ->where('application_id', $application->application_id)
        ->first();

    if (!$applicationPersonnel) {
        abort(404, 'Application personnel record not found.');
    }

    // Verify token if provided
    $token = request()->query('token');
    if ($token) {
        if (!$applicationPersonnel->update_token || $token !== $applicationPersonnel->update_token) {
            abort(403, 'Invalid or expired update token.');
        }
    }

    // Get bad documents from database - USING THE STATUS FIELDS
    $issues = [];
    $documentReasons = [];

    // Check each document status field for bad documents (using lowercase 'bad')
    $documentStatuses = [
        'application_letter_status' => 'Application Letter',
        'cert_of_reg_status' => 'Certificate of Registration',
        'grade_slip_status' => 'Grade Slip',
        'brgy_indigency_status' => 'Barangay Indigency',
        'student_id_status' => 'Student ID'
    ];

    // Parse individual reasons from JSON if exists
    $individualReasons = [];
    if ($applicationPersonnel->reason) {
        $individualReasons = json_decode($applicationPersonnel->reason, true) ?? [];
    }

    foreach ($documentStatuses as $statusField => $documentName) {
        // Check if status is 'bad' (lowercase)
        if ($applicationPersonnel->$statusField === 'bad') {
            $docKey = str_replace('_status', '', $statusField);
            $issues[] = $docKey;
            
            // Get the specific reason for this document or use default
            $documentReasons[$docKey] = $individualReasons[$docKey] ?? 'Document needs to be updated. Please upload an updated version.';
        }
    }

    // Debug logging
    \Log::info("Applicant ID: $applicant_id");
    \Log::info("Application Personnel Statuses: " . json_encode([
        'application_letter_status' => $applicationPersonnel->application_letter_status,
        'cert_of_reg_status' => $applicationPersonnel->cert_of_reg_status,
        'grade_slip_status' => $applicationPersonnel->grade_slip_status,
        'brgy_indigency_status' => $applicationPersonnel->brgy_indigency_status,
        'student_id_status' => $applicationPersonnel->student_id_status,
        'reason' => $applicationPersonnel->reason
    ]));
    \Log::info("Final Issues: " . json_encode($issues));
    \Log::info("Document Reasons: " . json_encode($documentReasons));

    return view('scholar.applicationupdate', compact('applicant', 'application', 'issues', 'documentReasons'));
} 

public function updateApplication(Request $request, $applicant_id)
{
    $applicant = Applicant::findOrFail($applicant_id);
    $application = Application::where('applicant_id', $applicant_id)->first();
    
    if (!$application) {
        abort(404, 'Application not found.');
    }

    // Get application personnel record to determine issues
    $applicationPersonnel = \DB::table('tbl_application_personnel')
        ->where('application_id', $application->application_id)
        ->first();

    // Get issues from database based on status fields
    $issues = [];
    if ($applicationPersonnel) {
        $documentStatuses = [
            'application_letter_status' => 'application_letter',
            'cert_of_reg_status' => 'cert_of_reg',
            'grade_slip_status' => 'grade_slip',
            'brgy_indigency_status' => 'brgy_indigency',
            'student_id_status' => 'student_id'
        ];

        foreach ($documentStatuses as $statusField => $dbField) {
            if ($applicationPersonnel->$statusField === 'bad') {
                $issues[] = $dbField;
            }
        }
    }

    // Validate only the files that need to be updated (the bad ones)
    $validationRules = [];
    foreach ($issues as $issue) {
        $validationRules[$issue] = 'required|file|mimes:pdf|max:5120';
    }

    $request->validate($validationRules);

    // Update application documents - replace bad documents with new files
    foreach ($issues as $dbField) {
        if ($request->hasFile($dbField)) {
            // Delete old file if exists
            if ($application->$dbField && Storage::exists($application->$dbField)) {
                Storage::delete($application->$dbField);
            }
            
            // Store new file and update database
            $application->$dbField = $this->moveFileToStorage($request->file($dbField));
        }
    }

    $application->save();

    // Update document statuses to 'good' for updated files and clear reason
    if ($applicationPersonnel) {
        $statusColumns = [
            'application_letter' => 'application_letter_status',
            'cert_of_reg' => 'cert_of_reg_status',
            'grade_slip' => 'grade_slip_status',
            'brgy_indigency' => 'brgy_indigency_status',
            'student_id' => 'student_id_status',
        ];

        $updates = ['reason' => null]; // Clear the reason

        foreach ($issues as $dbField) {
            if ($request->hasFile($dbField) && isset($statusColumns[$dbField])) {
                // Update status to 'good' when file is updated
                $updates[$statusColumns[$dbField]] = 'New';
            }
        }

        \DB::table('tbl_application_personnel')
            ->where('application_personnel_id', $applicationPersonnel->application_personnel_id)
            ->update($updates);

        // Log the update for debugging
        \Log::info("Application updated for Applicant ID: $applicant_id");
        \Log::info("Updated statuses: " . json_encode($updates));
        \Log::info("Files updated: " . json_encode(array_keys($request->allFiles())));
    }

return redirect()->route('home')->with('success', 'Application documents updated successfully!');
}

    public function logout(Request $request)
    {
           // Clear the scholar session
        $request->session()->forget('scholar');

        // Redirect to login page with success message
        return redirect()->route('scholar.login')->with('success', 'You have been logged out successfully.');
    }


    public function welcome(Request $request)
    {
        \Log::info("Request received: " . $request->method() . " " . $request->path());
        return response()->json(['message' => 'Welcome to the Scholar API!']);
    }

/**
 * Move uploaded files into storage/documents/
 */
private function getCurrentAcademicYear($date = null)
{
    $now = $date ? \Carbon\Carbon::parse($date) : now();
    $currentYear = (int)$now->year;

    // Academic year ends on July 10 of the year
    $cutoff = \Carbon\Carbon::createFromDate($currentYear, 7, 10)->endOfDay();

    if ($now->greaterThan($cutoff)) {
        // After July 10 -> academic year is currentYear - nextYear (e.g. 2025-2026)
        return $currentYear . '-' . ($currentYear + 1);
    } else {
        // On or before July 10 -> academic year is previousYear - currentYear (e.g. 2024-2025)
        return ($currentYear - 1) . '-' . $currentYear;
    }
}
// Add this method to your ScholarController class

/**
 * Display renewal history for the scholar
 */
public function renewalHistory()
{
    $scholar = session('scholar');
    if (!$scholar) {
        return redirect()->route('scholar.login')->withErrors(['error' => 'Please login to view renewal history.']);
    }

    // Load renewals for the current scholar with pagination
    $renewals = \App\Models\Renewal::where('scholar_id', $scholar->scholar_id)
        ->orderBy('date_submitted', 'desc')
        ->paginate(10);

    return view('scholar.renewal_history', compact('renewals'));
}

/**
 * Get renewal details via AJAX
 */
public function getRenewalDetails($renewalId)
{
    $scholar = session('scholar');
    if (!$scholar) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    // Get renewal details and ensure it belongs to the current scholar
    $renewal = \App\Models\Renewal::where('renewal_id', $renewalId)
        ->where('scholar_id', $scholar->scholar_id)
        ->first();

    if (!$renewal) {
        return response()->json(['success' => false, 'message' => 'Renewal not found'], 404);
    }

    return response()->json([
        'success' => true,
        'renewal' => $renewal
    ]);
}

}