<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Scholar;
use App\Models\Applicant;

class AuthController extends Controller
{
    /**
     * Mobile login for different user types
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string', // Changed from email to string to accept username
            'password' => 'required|string',
            'user_type' => 'required|in:user,scholar', // user for staff/admin, scholar for scholars
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            if ($request->user_type === 'scholar') {
                return $this->scholarLogin($request);
            } else {
                return $this->userLogin($request);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login for staff/admin users
     */
    private function userLogin(Request $request)
    {
        // Try to authenticate with lydopers table first (existing system)
        // Try both username and email
        $lydopers = \App\Models\Lydopers::where(function($query) use ($request) {
                $query->where('lydopers_username', $request->email)
                      ->orWhere('lydopers_email', $request->email);
            })
            ->where('lydopers_status', 'active')
            ->first();

        if ($lydopers && Hash::check($request->password, $lydopers->lydopers_pass)) {
            $token = $this->generateApiToken($lydopers);

            return $this->successResponse([
                'user' => [
                    'id' => $lydopers->lydopers_id,
                    'name' => $lydopers->lydopers_fname . ' ' . $lydopers->lydopers_lname,
                    'email' => $lydopers->lydopers_email,
                    'role' => $lydopers->lydopers_role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        }

        // Fallback to Laravel's default users table
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $this->generateApiToken($user);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        }

        return $this->errorResponse('Invalid credentials', 401);
    }

    /**
     * Login for scholars
     */
    private function scholarLogin(Request $request)
    {
        // Find scholar by username or email
        $scholar = null;
        $applicant = null;

        // First try to find by scholar username
        $scholar = Scholar::where('scholar_username', $request->email)->first();

        if ($scholar) {
            // Found by username, get applicant through application
            $applicant = $scholar->applicant;
        } else {
            // Try to find by applicant email
            $applicant = Applicant::where('applicant_email', $request->email)->first();

            if ($applicant) {
                // Get scholar through application
                $application = $applicant->application;
                if ($application) {
                    $scholar = Scholar::where('application_id', $application->application_id)->first();
                }
            }
        }

        if (!$scholar || !$applicant) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Check password
        if (!Hash::check($request->password, $scholar->scholar_pass)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        // Check if active
        if ($scholar->scholar_status !== 'Active') {
            return $this->errorResponse('Account is inactive', 401);
        }

        // Create token for scholar
        $token = $this->generateApiToken($scholar);

        return $this->successResponse([
            'scholar' => [
                'id' => $scholar->scholar_id,
                'username' => $scholar->scholar_username,
                'status' => $scholar->scholar_status,
                'applicant' => $applicant,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Scholar login successful');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Simple logout - just return success
        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user instanceof Scholar) {
            // Scholar profile
            $user->load('applicant');
            return $this->successResponse([
                'profile' => $user,
                'type' => 'scholar'
            ]);
        } else {
            // Staff/Admin profile
            return $this->successResponse([
                'profile' => $user,
                'type' => 'user'
            ]);
        }
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_type' => 'required|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            if ($request->user_type === 'scholar') {
                return $this->sendScholarOtp($request->email);
            } else {
                return $this->sendUserOtp($request->email);
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send OTP: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send OTP for scholar
     */
    private function sendScholarOtp($email)
    {
        $applicant = Applicant::where('applicant_email', $email)->first();

        if (!$applicant) {
            return $this->errorResponse('Email not found', 404);
        }

        // Generate OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save to password_otps table
        DB::table('password_otps')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Send OTP Email
        try {
            Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($email){
                $message->to($email);
                $message->subject('LYDO Scholarship Password Reset OTP');
            });
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send email', 500);
        }

        return $this->successResponse(null, 'OTP sent to your email');
    }

    /**
     * Send OTP for user
     */
    private function sendUserOtp($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->errorResponse('Email not found', 404);
        }

        // Generate OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save to password_otps table
        DB::table('password_otps')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'otp' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        // Send OTP Email
        try {
            Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($email){
                $message->to($email);
                $message->subject('LYDO Password Reset OTP');
            });
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send email', 500);
        }

        return $this->successResponse(null, 'OTP sent to your email');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $otpRecord = DB::table('password_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>', Carbon::now()->subMinutes(10))
            ->first();

        if (!$otpRecord) {
            return $this->errorResponse('Invalid or expired OTP', 400);
        }

        // Delete OTP after verification
        DB::table('password_otps')->where('email', $request->email)->delete();

        // Generate reset token
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        return $this->successResponse(['token' => $token], 'OTP verified successfully');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $reset = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return $this->errorResponse('Invalid or expired reset token', 400);
        }

        try {
            if ($request->user_type === 'scholar') {
                $this->resetScholarPassword($reset->email, $request->password);
            } else {
                $this->resetUserPassword($reset->email, $request->password);
            }

            // Delete token
            DB::table('password_resets')->where('email', $reset->email)->delete();

            return $this->successResponse(null, 'Password reset successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reset password: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reset scholar password
     */
    private function resetScholarPassword($email, $password)
    {
        $applicant = Applicant::where('applicant_email', $email)->first();
        if (!$applicant) {
            throw new \Exception('Applicant not found');
        }

        $application = $applicant->application;
        if (!$application) {
            throw new \Exception('Application not found');
        }

        $scholar = Scholar::where('application_id', $application->application_id)->first();
        if (!$scholar) {
            throw new \Exception('Scholar account not found');
        }

        $scholar->update(['scholar_pass' => Hash::make($password)]);
    }

    /**
     * Reset user password
     */
    private function resetUserPassword($email, $password)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->update(['password' => Hash::make($password)]);
    }

    /**
     * Register new user (for admin setup)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:lydo_admin,lydo_staff,mayor_staff',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $this->generateApiToken($user);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * Generate API token for user
     */
    private function generateApiToken($user)
    {
        // Generate a simple token using user ID and timestamp
        $tokenData = [
            'user_id' => $user->id ?? $user->scholar_id ?? $user->lydopers_id,
            'user_type' => $user instanceof Scholar ? 'scholar' : 
                          ($user instanceof \App\Models\Lydopers ? 'lydopers' : 'user'),
            'timestamp' => time(),
        ];
        
        return base64_encode(json_encode($tokenData));
    }

    /**
     * Success response helper
     */
    private function successResponse($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Error response helper
     */
    private function errorResponse($message = 'Error', $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    /**
     * Validation error response helper
     */
    private function validationErrorResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }
}
