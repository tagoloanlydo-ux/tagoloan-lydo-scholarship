<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Display all applications with applicant details.
     */
    public function index()
    {
        try {
            $applications = Application::with('applicant')->get();

            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
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
     * Store a new application.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'applicant_id' => 'required|exists:tbl_applicant,applicant_id',
                'application_letter' => 'nullable|string|max:255',
                'cert_of_reg' => 'nullable|string|max:255',
                'grade_slip' => 'nullable|string|max:255',
                'brgy_indigency' => 'nullable|string|max:255',
                'student_id' => 'nullable|string|max:255',
                'date_submitted' => 'required|date',
            ]);

            $application = Application::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully.',
                'data' => $application
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific application with applicant details.
     */
    public function show($id)
    {
        try {
            $application = Application::with('applicant')->find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $application
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update application details.
     */
    public function update(Request $request, $id)
    {
        try {
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            $validated = $request->validate([
                'applicant_id' => 'sometimes|integer|exists:tbl_applicant,applicant_id',
                'application_letter' => 'nullable|string|max:255',
                'cert_of_reg' => 'nullable|string|max:255',
                'grade_slip' => 'nullable|string|max:255',
                'brgy_indigency' => 'nullable|string|max:255',
                'student_id' => 'nullable|string|max:255',
                'date_submitted' => 'nullable|date',
            ]);

            $application->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully.',
                'data' => $application
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an application.
     */
    public function destroy($id)
    {
        try {
            $application = Application::find($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found.'
                ], 404);
            }

            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
