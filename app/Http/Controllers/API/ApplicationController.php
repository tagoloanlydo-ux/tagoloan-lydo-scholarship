<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Application;
use App\Models\Applicant;

class ApplicationController extends Controller
{
    /**
     * Submit a new application
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_id' => 'required|exists:tbl_applicant,applicant_id',
            'application_letter' => 'nullable|string',
            'cert_of_reg' => 'nullable|string',
            'grade_slip' => 'nullable|string',
            'brgy_indigency' => 'nullable|string',
            'student_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = Application::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => $application
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application status
     */
    public function status($applicantId)
    {
        try {
            $application = Application::where('applicant_id', $applicantId)->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get application status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application requirements
     */
    public function requirements($applicationId)
    {
        try {
            $application = Application::find($applicationId);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'application_letter' => $application->application_letter,
                    'cert_of_reg' => $application->cert_of_reg,
                    'grade_slip' => $application->grade_slip,
                    'brgy_indigency' => $application->brgy_indigency,
                    'student_id' => $application->student_id,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get requirements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload document for application
     */
    public function uploadDocument(Request $request, $applicationId)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:application_letter,cert_of_reg,grade_slip,brgy_indigency',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $application = Application::find($applicationId);
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('applications', $fileName, 'public');

            $application->update([
                $request->document_type => $fileName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $application
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }
}
