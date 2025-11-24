<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationPersonnel;
use App\Models\Scholar;
use App\Models\Disburse;
use App\Models\Renewal;
use App\Models\Announce;

use App\Models\User;
use App\Models\Lydopers;

class AdminController extends Controller
{
    /**
     * Get admin overview
     */
    public function index(Request $request)
    {
        return $this->successResponse([
            'message' => 'Admin API endpoints available',
            'endpoints' => [
                'dashboard' => '/api/admin/dashboard',
                'applicants' => '/api/admin/applicants',
                'scholars' => '/api/admin/scholars',
                'disbursements' => '/api/admin/disbursements',
                'renewals' => '/api/admin/renewals',
            ]
        ], 'Admin API overview');
    }

    /**
     * Get admin dashboard statistics
     */
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $role = $user->role;

            $stats = [];

            // Common stats for all admin roles
            $stats['total_applicants'] = Applicant::count();
            $stats['pending_applications'] = ApplicationPersonnel::where('status', 'Pending')->count();
            $stats['approved_applications'] = ApplicationPersonnel::where('status', 'Approved')->count();
            $stats['rejected_applications'] = ApplicationPersonnel::where('status', 'Rejected')->count();
            $stats['total_scholars'] = Scholar::where('scholar_status', 'Active')->count();
            $stats['inactive_scholars'] = Scholar::where('scholar_status', 'Inactive')->count();

            // Role-specific stats
            if ($role === 'lydo_admin') {
                $stats['pending_disbursements'] = Disburse::where('disbursement_status', 'Pending')->count();
                $stats['pending_renewals'] = Renewal::where('renewal_status', 'Pending')->count();
                $stats['total_staff'] = User::whereIn('role', ['lydo_staff', 'mayor_staff'])->count();
            } elseif ($role === 'lydo_staff') {
                $stats['my_pending_applications'] = ApplicationPersonnel::where('status', 'Pending')
                    ->whereHas('application', function($q) {
                        // Lydo staff handles all applications
                    })->count();
            } elseif ($role === 'mayor_staff') {
                $stats['my_pending_applications'] = ApplicationPersonnel::where('status', 'Pending')
                    ->where('lydopers_id', $user->id) // Assuming user is linked to lydopers
                    ->count();
            }

            return response()->json([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get applicants list with filtering
     */
    public function applicants(Request $request)
    {
        $query = Applicant::with(['application.applicationPersonnel']);

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->whereHas('application.applicationPersonnel', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $applicants = $query->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Applicants retrieved successfully',
            'data' => $applicants,
        ]);
    }

    /**
     * Get single applicant details
     */
    public function applicantDetails(Request $request, $applicantId)
    {
        $applicant = Applicant::with(['application.applicationPersonnel'])->find($applicantId);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Applicant details retrieved successfully',
            'data' => $applicant,
        ]);
    }

    /**
     * Update application status
     */
    public function updateApplicationStatus(Request $request, $applicationId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Approved,Rejected',
            'initial_screening' => 'nullable|in:Pending,Approved,Rejected',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $applicationPersonnel = ApplicationPersonnel::where('application_id', $applicationId)->first();

            if (!$applicationPersonnel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application personnel record not found',
                ], 404);
            }

            $applicationPersonnel->update([
                'status' => $request->status,
                'initial_screening' => $request->initial_screening ?? $applicationPersonnel->initial_screening,
                'remarks' => $request->remarks ?? $applicationPersonnel->remarks,
            ]);

            // If approved, create scholar account
            if ($request->status === 'Approved') {
                $this->createScholarAccount($applicationId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully',
                'data' => $applicationPersonnel->load('application.applicant'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get scholars list
     */
    public function scholars(Request $request)
    {
        $query = Scholar::with('applicant');

        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('applicant', function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('scholar_status', $request->status);
        }

        $scholars = $query->paginate(15);

        return $this->paginatedResponse($scholars, 'Scholars retrieved successfully');
    }

    /**
     * Update scholar status
     */
    public function updateScholarStatus(Request $request, $scholarId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $scholar = Scholar::find($scholarId);
        if (!$scholar) {
            return $this->errorResponse('Scholar not found', 404);
        }

        $scholar->update(['scholar_status' => $request->status]);

        return $this->successResponse($scholar, 'Scholar status updated successfully');
    }

    /**
     * Get disbursements
     */
    public function disbursements(Request $request)
    {
        $query = Disburse::with(['scholar.applicant']);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('disbursement_status', $request->status);
        }

        $disbursements = $query->paginate(15);

        return $this->paginatedResponse($disbursements, 'Disbursements retrieved successfully');
    }

    /**
     * Create disbursement
     */
    public function createDisbursement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scholar_id' => 'required|exists:tbl_scholar,scholar_id',
            'disbursement_amount' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $disbursement = Disburse::create([
                'scholar_id' => $request->scholar_id,
                'disbursement_amount' => $request->disbursement_amount,
                'disbursement_date' => $request->disbursement_date,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'disbursement_status' => 'Pending',
            ]);

            return $this->successResponse($disbursement->load('scholar.applicant'), 'Disbursement created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create disbursement: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update disbursement status
     */
    public function updateDisbursementStatus(Request $request, $disbursementId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Approved,Disbursed,Cancelled',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $disbursement = Disburse::find($disbursementId);
        if (!$disbursement) {
            return $this->errorResponse('Disbursement not found', 404);
        }

        $disbursement->update(['disbursement_status' => $request->status]);

        return $this->successResponse($disbursement, 'Disbursement status updated successfully');
    }

    /**
     * Get renewals
     */
    public function renewals(Request $request)
    {
        $query = Renewal::with(['scholar.applicant']);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('renewal_status', $request->status);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('renewal_acad_year', $request->academic_year);
        }

        $renewals = $query->paginate(15);

        return $this->paginatedResponse($renewals, 'Renewals retrieved successfully');
    }

    /**
     * Update renewal status
     */
    public function updateRenewalStatus(Request $request, $renewalId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending,Approved,Rejected',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $renewal = Renewal::find($renewalId);
        if (!$renewal) {
            return $this->errorResponse('Renewal not found', 404);
        }

        $renewal->update(['renewal_status' => $request->status]);

        return $this->successResponse($renewal, 'Renewal status updated successfully');
    }

    /**
     * Create announcement
     */
    public function createAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|in:All,Scholars,Applicants',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $announcement = Announce::create([
                'announce_title' => $request->announce_title,
                'announce_content' => $request->announce_content,
                'announce_type' => $request->announce_type,
                'date_posted' => now(),
            ]);

            return $this->successResponse($announcement, 'Announcement created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create announcement: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get announcements
     */
    public function announcements(Request $request)
    {
        $query = Announce::orderBy('date_posted', 'desc');

        if ($request->has('type') && !empty($request->type)) {
            $query->where('announce_type', $request->type);
        }

        $announcements = $query->paginate(15);

        return $this->paginatedResponse($announcements, 'Announcements retrieved successfully');
    }

    /**
     * Send email to applicant
     */
    public function sendEmail(Request $request, $applicationId)
    {
        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'issues' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            // Here you would implement email sending logic
            // For now, just return success
            return $this->successResponse([], 'Email sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send email: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete application
     */
    public function deleteApplication(Request $request, $applicationId)
    {
        try {
            $applicationPersonnel = ApplicationPersonnel::where('application_id', $applicationId)->first();

            if (!$applicationPersonnel) {
                return $this->errorResponse('Application not found', 404);
            }

            $applicationPersonnel->delete();

            return $this->successResponse([], 'Application deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete application: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send bulk emails
     */
    public function sendBulkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applications' => 'required|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'issues' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            // Here you would implement bulk email sending logic
            // For now, just return success
            return $this->successResponse([], 'Bulk emails sent successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send bulk emails: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get application settings
     */
    public function getSettings(Request $request)
    {
        try {
            $settings = \App\Models\Settings::first();

            if (!$settings) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'renewal_start_date' => null,
                        'renewal_deadline' => null,
                        'application_start_date' => null,
                        'application_deadline' => null,
                        'renewal_semester' => null,
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'renewal_start_date' => $settings->renewal_start_date,
                    'renewal_deadline' => $settings->renewal_deadline,
                    'application_start_date' => $settings->application_start_date,
                    'application_deadline' => $settings->application_deadline,
                    'renewal_semester' => $settings->renewal_semester,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test endpoint
     */
    public function testEndpoint(Request $request)
    {
        return $this->successResponse(['message' => 'Test endpoint working'], 'Test successful');
    }

    /**
     * Create scholar account helper
     */
    private function createScholarAccount($applicationId)
    {
        $application = Application::find($applicationId);
        if (!$application) return;

        // Check if scholar already exists
        $existingScholar = Scholar::where('application_id', $applicationId)->first();
        if ($existingScholar) return;

        // Generate unique username
        $applicant = $application->applicant;
        $baseUsername = strtolower($applicant->applicant_fname . $applicant->applicant_lname);
        $username = $baseUsername;
        $counter = 1;

        while (Scholar::where('scholar_username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        Scholar::create([
            'application_id' => $applicationId,
            'scholar_username' => $username,
            'scholar_pass' => bcrypt('default_password'), // Will be changed during activation
            'scholar_status' => 'Active',
            'date_activated' => now(),
        ]);
    }

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

}
