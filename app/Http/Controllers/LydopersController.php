<?php

namespace App\Http\Controllers;

use App\Models\Lydopers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LydopersController extends Controller
{
        public function showfrontpage()
    {
        return view('auth.front-page');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function showregistrationForm()
    {
        $lydoAdminExists = Lydopers::where('lydopers_role', 'lydo_admin')->exists();
        return view('auth.registration', compact('lydoAdminExists'));
    }
    //parameterized
  public function store(Request $request)
{
    try {
        $data = $request->validate([
            'lydopers_fname' => 'required|string|max:50',
            'lydopers_mname' => 'nullable|string|max:50',
            'lydopers_lname' => 'required|string|max:50',
            'lydopers_suffix' => 'nullable|string|max:10',
            'lydopers_bdate' => 'nullable|date',
            'lydopers_address' => 'nullable|string|max:255',
            'lydopers_email' => 'required|email|max:100|unique:tbl_lydopers,lydopers_email',
            'lydopers_contact_number' => 'required|regex:/^09\d{9}$/',
            'lydopers_username' => 'required|string|max:50|unique:tbl_lydopers,lydopers_username',
            'lydopers_pass' => 'required|string|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/',
            'lydopers_role' => 'required|string',
        ]);

        // ✅ Set lydopers_status based on role
        if (in_array($data['lydopers_role'], ['lydo_staff', 'mayor_staff'])) {
            $data['lydopers_status'] = 'inactive';
        } elseif ($data['lydopers_role'] === 'lydo_admin') {
            $data['lydopers_status'] = 'active';
        } else {
            $data['lydopers_status'] = 'inactive'; // default
        }

        // Encrypt password
        $data['lydopers_pass'] = bcrypt($data['lydopers_pass']);

        Lydopers::create($data);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}

//parameterized
public function login(Request $request)
{
    $request->validate([
        'lydopers_username' => 'required|string|regex:/^[a-zA-Z0-9]+$/',
        'lydopers_pass' => 'required|string',
    ]);
  // 
    $user = Lydopers::where('lydopers_username', '=', $request->input('lydopers_username'))->first();

    if (!$user) {
        // Return with inline error, no SweetAlert
        return back()->withInput()->withErrors(['lydopers_username' => 'Username doesnt exist.']);
    }

    if (!Hash::check($request->lydopers_pass, $user->lydopers_pass)) {
        // Return with inline error, no SweetAlert
        return back()->withInput()->withErrors(['lydopers_pass' => 'Incorrect password.']);
    }

    if ($user->lydopers_status !== 'active') {
        // Return with SweetAlert error for inactive account
        return back()->withInput()->withErrors(['error' => 'Your account is inactive. Please contact the LYDO Admin to reactivate your account'])->with('showInactiveAlert', true);
    }

    // Store user in session
    session(['lydopers' => $user]);

    // Redirect based on role
  switch ($user->lydopers_role) {
    case 'lydo_admin':
        session(['show_welcome' => true]);
        return redirect()->route('LydoAdmin.dashboard');
    case 'lydo_staff':
        session(['show_welcome' => true]);
        return redirect()->route('LydoStaff.dashboard');
    case 'mayor_staff':
        return redirect()->route('MayorStaff.dashboard');
    default:
        return back()->withErrors(['error' => 'Unknown user role.']);
}

}

 public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Step 2: Send OTP
public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    // Check if email exists
    $user = Lydopers::where('lydopers_email', $request->email)->first();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Email not found.']);
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

    // Parameterized
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

        // Get user information including role
        $user = Lydopers::where('lydopers_email', $request->email)->first();

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

        // Get role display name
        $roleDisplayName = ucwords(str_replace('_', ' ', $user->lydopers_role));

        return response()->json([
            'success' => true,
            'token' => $token,
            'role' => $user->lydopers_role,
            'role_display' => $roleDisplayName,
            'message' => "OTP verified successfully! You can now reset your password for your {$roleDisplayName} account."
        ]);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tbl_lydopers,lydopers_email',
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

    // Step 3: Show Reset Form
    public function showResetForm($token)
    {
           if (!$token) {
        return redirect()->route('login'); 
    }
        return view('auth.reset-password', ['token' => $token]);
    }

    // Step 4: Reset Password
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
    return redirect()->route('login')->withErrors(['error' => 'Invalid or expired reset link.']);
}

//parameterized
Lydopers::where('lydopers_email', $reset->email)
        ->update(['lydopers_pass' => Hash::make($request->password)]);

// Delete token
DB::table('password_resets')->where('email', $reset->email)->delete();

return redirect()->route('login')->with('success', 'Your password has been reset successfully!');

}
public function checkEmail(Request $request)
{
    $exists = Lydopers::where('lydopers_email', $request->value)->exists();
    return response()->json(['available' => !$exists]);
}
public function checkUsername(Request $request)
{
    $exists = \DB::table('tbl_lydopers')
        ->where('lydopers_username', $request->value)
        ->exists();

    return response()->json(['available' => !$exists]);
}

public function logout(Request $request)
{
    // Check user type before clearing session
    $isScholar = $request->session()->has('scholar');

    // Clear sessions
    $request->session()->forget('lydopers');
    $request->session()->forget('scholar');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect based on user type
    if ($isScholar) {
        return redirect()->route('scholar.login')->with('success', 'You have been logged out.');
    } else {
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}

}
