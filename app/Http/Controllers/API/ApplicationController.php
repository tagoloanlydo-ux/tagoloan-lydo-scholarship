<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationPersonnel;

class ApplicationController extends Controller
{
    /**
     * Display all applications with applicant details.
     */
    public function index()
    {
        try {
            $applications = Application::with('applicant')->get();

            // Also get ApplicationPersonnel data for each application
            $applicationsWithPersonnel = $applications->map(function ($application) {
                $personnel = ApplicationPersonnel::where('application_id', $application->application_id)->first();
                $application->application_personnel = $personnel;
                return $application;
            });

            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
                'data' => $applicationsWithPersonnel,
                'count' => $applicationsWithPersonnel->count()
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

            // Get ApplicationPersonnel data for this application
            $personnel = ApplicationPersonnel::where('application_id', $application->application_id)->first();
            $application->application_personnel = $personnel;

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