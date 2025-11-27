<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Scholar;
use App\Models\Applicant;
use App\Models\Application;

class ScholarController extends Controller
{
    /**
     * Success response helper
     */
    protected function successResponse($data = [], $message = '', $status = 200)
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
    protected function errorResponse($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    /**
     * Validation error response helper
     */
    protected function validationErrorResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    /**
     * Paginated response helper
     */
    protected function paginatedResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Send OTP for password reset
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_type' => 'required|string|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $email = $request->email;
            $userType = $request->user_type;

            // For scholars, check if email exists in applicant table and has scholar account
            if ($userType === 'scholar') {
                $applicant = Applicant::where('applicant_email', $email)->first();
                
                if (!$applicant) {
                    return $this->errorResponse('Email not found in our records.', 404);
                }

                // Check if there's an application and scholar account
                $application = Application::where('applicant_id', $applicant->applicant_id)->first();
                if (!$application) {
                    return $this->errorResponse('No application found for this email.', 404);
                }

                $scholar = Scholar::where('application_id', $application->application_id)->first();
                if (!$scholar) {
                    return $this->errorResponse('No scholar account found for this email.', 404);
                }
            }

            // Generate 6-digit OTP
            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Save to password_otps table
            DB::table('password_otps')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'otp' => $otp,
                    'user_type' => $userType,
                    'created_at' => Carbon::now()
                ]
            );

            // Send OTP Email
            try {
                Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($email) {
                    $message->to($email);
                    $message->subject('LYDO Scholarship Password Reset OTP');
                });
            } catch (\Exception $e) {
                \Log::error('OTP Email sending failed: ' . $e->getMessage());
                return $this->errorResponse('Failed to send OTP email. Please try again.', 500);
            }

            return $this->successResponse([], 'OTP sent to your email successfully!');

        } catch (\Exception $e) {
            \Log::error('Send OTP Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to send OTP. Please try again.', 500);
        }
    }

    /**
     * Reset password with OTP
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8',
            'user_type' => 'required|string|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $email = $request->email;
            $otp = $request->otp;
            $password = $request->password;
            $userType = $request->user_type;

            // Verify OTP
            $otpRecord = DB::table('password_otps')
                ->where('email', $email)
                ->where('otp', $otp)
                ->where('user_type', $userType)
                ->where('created_at', '>', Carbon::now()->subMinutes(10)) // OTP expires in 10 minutes
                ->first();

            if (!$otpRecord) {
                return $this->errorResponse('Invalid or expired OTP.', 400);
            }

            // Reset password based on user type
            if ($userType === 'scholar') {
                $applicant = Applicant::where('applicant_email', $email)->first();
                
                if (!$applicant) {
                    return $this->errorResponse('Applicant not found.', 404);
                }

                $application = Application::where('applicant_id', $applicant->applicant_id)->first();
                if (!$application) {
                    return $this->errorResponse('Application not found.', 404);
                }

                $scholar = Scholar::where('application_id', $application->application_id)->first();
                if (!$scholar) {
                    return $this->errorResponse('Scholar account not found.', 404);
                }

                // Update scholar password
                $scholar->update([
                    'scholar_pass' => Hash::make($password)
                ]);

                // Delete OTP after successful reset
                DB::table('password_otps')->where('email', $email)->delete();

                return $this->successResponse([], 'Password reset successfully!');

            } else {
                // Handle regular user password reset here if needed
                return $this->errorResponse('User password reset not implemented yet.', 501);
            }

        } catch (\Exception $e) {
            \Log::error('Reset Password Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to reset password. Please try again.', 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_type' => 'required|string|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $email = $request->email;
            $userType = $request->user_type;

            // For scholars, verify email exists
            if ($userType === 'scholar') {
                $applicant = Applicant::where('applicant_email', $email)->first();
                if (!$applicant) {
                    return $this->errorResponse('Email not found in our records.', 404);
                }
            }

            // Generate new 6-digit OTP
            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Update password_otps table
            DB::table('password_otps')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'otp' => $otp,
                    'user_type' => $userType,
                    'created_at' => Carbon::now()
                ]
            );

            // Send OTP Email
            try {
                Mail::send('emails.password-otp', ['otp' => $otp], function($message) use ($email) {
                    $message->to($email);
                    $message->subject('LYDO Scholarship Password Reset OTP - Resent');
                });
            } catch (\Exception $e) {
                \Log::error('Resend OTP Email failed: ' . $e->getMessage());
                return $this->errorResponse('Failed to resend OTP email. Please try again.', 500);
            }

            return $this->successResponse([], 'New OTP sent to your email successfully!');

        } catch (\Exception $e) {
            \Log::error('Resend OTP Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to resend OTP. Please try again.', 500);
        }
    }

    /**
     * Verify OTP (separate endpoint if needed)
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'user_type' => 'required|string|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $otpRecord = DB::table('password_otps')
                ->where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('user_type', $request->user_type)
                ->where('created_at', '>', Carbon::now()->subMinutes(10))
                ->first();

            if (!$otpRecord) {
                return $this->errorResponse('Invalid or expired OTP.', 400);
            }

            return $this->successResponse([], 'OTP verified successfully!');

        } catch (\Exception $e) {
            \Log::error('Verify OTP Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to verify OTP. Please try again.', 500);
        }
    }

    /**
     * Scholar Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|string|in:user,scholar',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $email = $request->email;
            $password = $request->password;
            $userType = $request->user_type;

            if ($userType === 'scholar') {
                // Find scholar by username or email
                $scholar = Scholar::where('scholar_username', $email)
                    ->orWhereHas('application.applicant', function($query) use ($email) {
                        $query->where('applicant_email', $email);
                    })
                    ->with(['application.applicant'])
                    ->first();

                if (!$scholar) {
                    return $this->errorResponse('Invalid credentials.', 401);
                }

                if (!Hash::check($password, $scholar->scholar_pass)) {
                    return $this->errorResponse('Invalid credentials.', 401);
                }

                if ($scholar->scholar_status !== 'Active') {
                    return $this->errorResponse('Your account is inactive.', 403);
                }

                // Generate token (you might want to use Laravel Sanctum or Passport)
                $token = $this->generateToken($scholar);

                $userData = [
                    'id' => $scholar->scholar_id,
                    'username' => $scholar->scholar_username,
                    'email' => $scholar->application->applicant->applicant_email,
                    'name' => $scholar->application->applicant->applicant_fname . ' ' . $scholar->application->applicant->applicant_lname,
                    'role' => 'scholar',
                ];

                return $this->successResponse([
                    'token' => $token,
                    'user' => $userData,
                    'scholar' => $scholar
                ], 'Login successful!');

            } else {
                // Handle regular user login here
                return $this->errorResponse('User login not implemented yet.', 501);
            }

        } catch (\Exception $e) {
            \Log::error('Login Error: ' . $e->getMessage());
            return $this->errorResponse('Login failed. Please try again.', 500);
        }
    }

    /**
     * Generate JWT token (simplified version - consider using Laravel Sanctum)
     */
    private function generateToken($user)
    {
        // This is a simplified token generation
        // In production, use Laravel Sanctum or Passport
        $payload = [
            'user_id' => $user->scholar_id,
            'user_type' => 'scholar',
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];

        return base64_encode(json_encode($payload));
    }

    /**
     * Get scholar profile
     */
    public function getProfile(Request $request)
    {
        try {
            $scholarId = $request->user()->id; // Assuming you have authentication middleware
            $scholar = Scholar::with(['application.applicant'])
                ->find($scholarId);

            if (!$scholar) {
                return $this->errorResponse('Scholar not found.', 404);
            }

            return $this->successResponse([
                'scholar' => $scholar,
                'applicant' => $scholar->application->applicant
            ], 'Profile retrieved successfully.');

        } catch (\Exception $e) {
            \Log::error('Get Profile Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve profile.', 500);
        }
    }

    /**
     * Update scholar profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_fname' => 'required|string|max:255',
            'applicant_mname' => 'nullable|string|max:255',
            'applicant_lname' => 'required|string|max:255',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_bdate' => 'required|date|before:today',
            'applicant_civil_status' => 'required|in:single,married,widowed,divorced',
            'applicant_brgy' => 'required|string|max:255',
            'applicant_contact_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $scholarId = $request->user()->id;
            $scholar = Scholar::with(['application.applicant'])->find($scholarId);

            if (!$scholar) {
                return $this->errorResponse('Scholar not found.', 404);
            }

            $applicant = $scholar->application->applicant;
            $applicant->update($request->all());

            return $this->successResponse([
                'scholar' => $scholar,
                'applicant' => $applicant
            ], 'Profile updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Update Profile Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to update profile.', 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $scholarId = $request->user()->id;
            $scholar = Scholar::find($scholarId);

            if (!$scholar) {
                return $this->errorResponse('Scholar not found.', 404);
            }

            if (!Hash::check($request->current_password, $scholar->scholar_pass)) {
                return $this->errorResponse('Current password is incorrect.', 400);
            }

            $scholar->update([
                'scholar_pass' => Hash::make($request->new_password)
            ]);

            return $this->successResponse([], 'Password changed successfully.');

        } catch (\Exception $e) {
            \Log::error('Change Password Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to change password.', 500);
        }
    }

    /**
     * Get all scholars (for admin purposes)
     */
    public function index(Request $request)
    {
        try {
            $query = Scholar::with(['application.applicant']);

            // Apply filters
            if ($request->has('search') && !empty($request->search)) {
                $query->whereHas('application.applicant', function($q) use ($request) {
                    $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                      ->orWhere('applicant_lname', 'like', '%' . $request->search . '%')
                      ->orWhere('applicant_email', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->where('scholar_status', $request->status);
            }

            $scholars = $query->paginate(15);

            return $this->paginatedResponse($scholars, 'Scholars retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('Get Scholars Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve scholars.', 500);
        }
    }

    /**
     * Get single scholar by ID
     */
    public function show($id)
    {
        try {
            $scholar = Scholar::with(['application.applicant'])->find($id);

            if (!$scholar) {
                return $this->errorResponse('Scholar not found', 404);
            }

            return $this->successResponse($scholar, 'Scholar retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('Get Scholar Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve scholar.', 500);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            // If using tokens, you would revoke them here
            // For JWT, the client just discards the token
            
            return $this->successResponse([], 'Logged out successfully.');

        } catch (\Exception $e) {
            \Log::error('Logout Error: ' . $e->getMessage());
            return $this->errorResponse('Logout failed.', 500);
        }
    }
}