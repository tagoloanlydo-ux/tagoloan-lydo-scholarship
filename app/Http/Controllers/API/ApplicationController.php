<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Application;
use App\Models\Applicant;

class ApplicationController extends Controller
{
    /**
     * Submit a new application
     */
    public function submit(Request $request)
    {
        // Check if we're creating a new applicant or using existing applicant_id
        if ($request->has('applicant_id')) {
            // Existing applicant - validate as before
            $validator = Validator::make($request->all(), [
                'applicant_id' => 'required|exists:tbl_applicant,applicant_id',
                'application_letter' => 'nullable|string',
                'cert_of_reg' => 'nullable|string',
                'grade_slip' => 'nullable|string',
                'brgy_indigency' => 'nullable|string',
                'student_id' => 'nullable|string',
            ]);
        } else {
            // New applicant - validate applicant data
            $validator = Validator::make($request->all(), [
                'applicant_fname' => 'required|string|max:50',
                'applicant_mname' => 'nullable|string|max:50',
                'applicant_lname' => 'required|string|max:50',
                'applicant_suffix' => 'nullable|string|max:10',
                'applicant_gender' => 'required|string|max:10|in:male,female,other,Male,Female,Other',
                'applicant_bdate' => 'required|date',
                'applicant_civil_status' => 'required|string|max:20|in:single,married,widowed,divorced,Single,Married,Widowed,Divorced',
                'applicant_brgy' => 'required|string|max:100',
                'applicant_email' => 'required|email|max:100|unique:tbl_applicant',
                'applicant_contact_number' => 'required|string|max:20',
                'applicant_school_name' => 'required|string|max:100',
                'applicant_year_level' => 'required|string|max:20',
                'applicant_course' => 'required|string|max:100',
                'applicant_acad_year' => 'required|string|max:20',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            if ($request->has('applicant_id')) {
                // Use existing applicant
                $applicantId = $request->applicant_id;
            } else {
                // Normalize data before creating applicant
                $applicantData = $request->all();
                
                // Normalize gender to lowercase
                if (isset($applicantData['applicant_gender'])) {
                    $applicantData['applicant_gender'] = strtolower($applicantData['applicant_gender']);
                }
                
                // Normalize civil status to lowercase
                if (isset($applicantData['applicant_civil_status'])) {
                    $applicantData['applicant_civil_status'] = strtolower($applicantData['applicant_civil_status']);
                }
                
                // Create new applicant
                $applicant = Applicant::create($applicantData);
                $applicantId = $applicant->applicant_id;
            }

            // Create application record
            $application = DB::table('tbl_application')->insertGetId([
                'applicant_id' => $applicantId,
                'date_submitted' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create application personnel record
            DB::table('tbl_application_personnel')->insert([
                'application_id' => $application,
                'status' => 'Pending',
                'initial_screening' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => ['application_id' => $application, 'applicant_id' => $applicantId]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
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
