<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationPersonnel;

class ApplicationPersonnelController extends Controller
{
    /**
     * Display all application personnel records.
     */
    public function index()
    {
        $records = ApplicationPersonnel::with(['application.applicant'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Application personnel list retrieved successfully.',
            'data' => $records
        ], 200);
    }

    /**
     * Store a new application personnel record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|integer|exists:tbl_application,application_id',
            'lydopers_id' => 'required|integer',
            'initial_screening' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'reviewer_comment' => 'nullable|string',
            'is_bad' => 'nullable|boolean',
            'intake_sheet_token' => 'nullable|string',
            'intake_sheet_token_expires_at' => 'nullable|date',
            'update_token' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'application_letter_status' => 'nullable|string|max:50',
            'cert_of_reg_status' => 'nullable|string|max:50',
            'grade_slip_status' => 'nullable|string|max:50',
            'brgy_indigency_status' => 'nullable|string|max:50',
            'student_id_status' => 'nullable|string|max:50',
        ]);

        // Set default status to 'pending' if not provided
        $validated['status'] = $validated['status'] ?? 'pending';

        $record = ApplicationPersonnel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Personnel assignment created successfully.',
            'data' => $record
        ], 201);
    }

    /**
     * Display a specific application personnel record.
     */
    public function show($id)
    {
        $record = ApplicationPersonnel::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record
        ], 200);
    }

    /**
     * Update application personnel record.
     */
    public function update(Request $request, $id)
    {
        $record = ApplicationPersonnel::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        $validated = $request->validate([
            'application_id' => 'sometimes|integer|exists:tbl_application,application_id',
            'lydopers_id' => 'sometimes|integer',
            'initial_screening' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string|max:100',
            'reviewer_comment' => 'nullable|string',
            'is_bad' => 'nullable|boolean',
            'intake_sheet_token' => 'nullable|string',
            'intake_sheet_token_expires_at' => 'nullable|date',
            'update_token' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'application_letter_status' => 'nullable|string|max:50',
            'cert_of_reg_status' => 'nullable|string|max:50',
            'grade_slip_status' => 'nullable|string|max:50',
            'brgy_indigency_status' => 'nullable|string|max:50',
            'student_id_status' => 'nullable|string|max:50',
        ]);

        $record->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully.',
            'data' => $record
        ], 200);
    }

    /**
     * Delete application personnel record.
     */
    public function destroy($id)
    {
        $record = ApplicationPersonnel::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.'
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully.'
        ], 200);
    }

    /**
     * Get applications for mayor staff review - FIXED VERSION
     */
    public function getMayorStaffApplications(Request $request)
    {
        try {
            $applications = ApplicationPersonnel::with([
                    'application.applicant', // Load applicant through application
                    'application' // Load application directly
                ])
                ->where('lydopers_id', $request->auth_user_id)
                ->where('status', 'Waiting')
                ->where('initial_screening', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Mayor staff applications retrieved successfully.',
                'data' => $applications,
                'count' => $applications->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve applications.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mayor staff dashboard data.
     */
    public function getMayorStaffDashboard(Request $request)
    {
        try {
            $userId = $request->auth_user_id;

            // Get counts
            $totalApplications = ApplicationPersonnel::where('lydopers_id', $userId)->count();
            $pendingInitial = ApplicationPersonnel::where('lydopers_id', $userId)
                ->where('initial_screening', 'Pending')
                ->count();
            $reviewedCount = ApplicationPersonnel::where('lydopers_id', $userId)
                ->where('initial_screening', 'Reviewed')
                ->count();

            // Get recent applications
            $recentApplications = ApplicationPersonnel::with(['application.applicant'])
                ->where('lydopers_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_applications' => $totalApplications,
                    'pending_initial' => $pendingInitial,
                    'reviewed_count' => $reviewedCount,
                    'recent_applications' => $recentApplications
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get mayor staff status page data - FIXED VERSION
     */
    public function getMayorStaffStatus(Request $request)
    {
        try {
            $userId = $request->auth_user_id;

            // Get applications assigned to this mayor staff (remove remarks filter for new applications)
            $applications = ApplicationPersonnel::with(['application.applicant'])
                ->where('lydopers_id', $userId)
                ->where('status', 'Waiting')
                ->where('initial_screening', 'Reviewed')
                // Remove or adjust the remarks filter to include 'Pending' status
                ->where(function($query) {
                    $query->whereIn('remarks', ['Poor', 'Ultra Poor'])
                          ->orWhere('remarks', 'Pending');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Get processed applications
            $processedApplications = ApplicationPersonnel::with(['application.applicant'])
                ->where('lydopers_id', $userId)
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $applications,
                    'processed_applications' => $processedApplications
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve status data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update mayor staff application status.
     */
    public function updateMayorStaffStatus(Request $request, $id)
    {
        try {
            $record = ApplicationPersonnel::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            // Verify the record belongs to the authenticated mayor staff
            if ($record->lydopers_id != $request->auth_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this application.'
                ], 403);
            }

            $validated = $request->validate([
                'status' => 'required|in:Approved,Rejected',
                'remarks' => 'nullable|string'
            ]);

            $record->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? $record->remarks,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully.',
                'data' => $record
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check assigned applications
     */
    public function debugMayorStaffApplications(Request $request)
    {
        try {
            $userId = $request->auth_user_id;

            $allApplications = ApplicationPersonnel::with(['application.applicant'])
                ->where('lydopers_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $waitingPending = ApplicationPersonnel::with(['application.applicant'])
                ->where('lydopers_id', $userId)
                ->where('status', 'Waiting')
                ->where('initial_screening', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'all_applications' => $allApplications,
                    'waiting_pending_applications' => $waitingPending,
                    'counts' => [
                        'total' => $allApplications->count(),
                        'waiting_pending' => $waitingPending->count()
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
