<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationPersonnel;
use App\Models\Lydopers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicantController extends Controller
{
    /**
     * Display all applicants with their applications.
     */
    public function index()
    {
        try {
            // Use 'application' (singular) if one-to-one relationship
            $applicants = Applicant::with('application')->get();

            // Transform file paths to full URLs for Flutter
            $applicants->transform(function ($applicant) {
                if ($applicant->application) {
                    $applicant->application = $this->addFileUrlsToApplication($applicant->application);
                }
                return $applicant;
            });

            return response()->json([
                'success' => true,
                'message' => 'Applicants retrieved successfully.',
                'data' => $applicants,
                'count' => $applicants->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving applicants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve applicants.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific applicant with application.
     */
    public function show($id)
    {
        try {
            $applicant = Applicant::with('application')->find($id);

            if (!$applicant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant not found.'
                ], 404);
            }

            // Transform file paths to full URLs for Flutter
            if ($applicant->application) {
                $applicant->application = $this->addFileUrlsToApplication($applicant->application);
            }

            return response()->json([
                'success' => true,
                'data' => $applicant
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving applicant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve applicant.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add full file URLs to application data for Flutter
     */
    private function addFileUrlsToApplication($application)
    {
        $fileFields = [
            'application_letter',
            'cert_of_reg', 
            'grade_slip',
            'brgy_indigency',
            'student_id'
        ];

        // Create a copy of the application as array to modify
        $appData = $application->toArray();
        
        foreach ($fileFields as $field) {
            if (!empty($appData[$field])) {
                $filename = basename($appData[$field]);
                // Generate full URL for Flutter app
                $appData[$field . '_url'] = 'https://srv1278-files.hstgr.io/3d66eae9e48136e2/files/public_html/storage/documents/' . $filename;
                
                // Keep the original path for backend reference
                $appData[$field . '_path'] = $appData[$field];
                
                // ALSO override the original field with full URL for backward compatibility
                $appData[$field] = 'https://srv1278-files.hstgr.io/3d66eae9e48136e2/files/public_html/storage/documents/' . $filename;
            } else {
                $appData[$field . '_url'] = null;
                $appData[$field . '_path'] = null;
            }
        }

        return $appData;
    }

    /**
     * Store a new applicant AND create an application record in one submission.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('=== APPLICATION SUBMISSION STARTED ===');

            // Validate applicant data using request validate
            $validatedApplicant = $request->validate([
                'applicant_fname' => 'required|string|max:255',
                'applicant_mname' => 'nullable|string|max:255',
                'applicant_lname' => 'required|string|max:255',
                'applicant_suffix' => 'nullable|string|max:50',
                'applicant_gender' => 'required|string|max:50',
                'applicant_bdate' => 'required|date',
                'applicant_civil_status' => 'required|string|max:50',
                'applicant_pob' => 'nullable|string|max:255',
                'applicant_brgy' => 'required|string|max:255',
                'applicant_email' => 'required|email|unique:tbl_applicant,applicant_email',
                'applicant_contact_number' => 'required|string|max:20',
                'applicant_school_name' => 'required|string|max:255',
                'applicant_year_level' => 'required|string|max:50',
                'applicant_course' => 'required|string|max:255',
                'applicant_acad_year' => 'required|string|max:20',
            ]);

            Log::info('Creating applicant record...');

            // Create applicant
            $applicant = Applicant::create($validatedApplicant);
            Log::info('Applicant created successfully', [
                'applicant_id' => $applicant->applicant_id,
                'name' => $applicant->applicant_fname . ' ' . $applicant->applicant_lname
            ]);

            // Prepare application data
            $applicationData = [
                'applicant_id' => $applicant->applicant_id,
                'date_submitted' => now(),
            ];

            Log::info('Processing document files...');

            // ✅ FIXED: Store files in PRODUCTION public_html/storage/documents/
            $fileFields = [
                'application_letter',
                'cert_of_reg',
                'grade_slip',
                'brgy_indigency',
                'student_id'
            ];

            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    Log::info("Processing file upload: $field", [
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize()
                    ]);
                    
                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    
                    // ✅ ABSOLUTE PATH to PRODUCTION storage/documents
                    $destinationPath = base_path('../../public_html/storage/documents');
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                        Log::info("Created PRODUCTION directory: $destinationPath");
                    }
                    
                    // Move file to destination
                    if ($file->move($destinationPath, $filename)) {
                        Log::info("✅ File saved to PRODUCTION: $destinationPath/$filename");
                        // 🔥 CRITICAL FIX: Store consistent path without 'storage/' prefix
                        $applicationData[$field] = 'documents/' . $filename;
                    } else {
                        Log::error("❌ Failed to save file to production: $filename");
                        $applicationData[$field] = '';
                    }
                } else {
                    Log::warning("No file uploaded for: $field");
                    $applicationData[$field] = '';
                }
            }

            Log::info('Creating application record...');

            // Create application record
            $application = Application::create($applicationData);
            Log::info('Application created successfully', [
                'application_id' => $application->application_id,
                'applicant_id' => $application->applicant_id
            ]);

            // 🔥 CRITICAL FIX: Find available mayor staff and assign properly
            $mayorStaff = DB::table('tbl_lydopers')
                ->where('lydopers_role', 'mayor_staff')
                ->where('lydopers_status', 'active')
                ->first();

            if (!$mayorStaff) {
                // Fallback: get any active staff
                $mayorStaff = DB::table('tbl_lydopers')
                    ->where('lydopers_status', 'active')
                    ->first();
            }

            // 🔥 CRITICAL FIX: Create ApplicationPersonnel record with VALID remarks
            $applicationPersonnelData = [
                'application_id' => $application->application_id,
                'lydopers_id' => $mayorStaff ? $mayorStaff->lydopers_id : 1, // Default to 1 if no staff found
                'initial_screening' => 'Pending',
                'remarks' => 'Poor', // 🔥 CHANGE FROM 'Pending' TO VALID POVERTY LEVEL
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $applicationPersonnel = ApplicationPersonnel::create($applicationPersonnelData);
            Log::info('ApplicationPersonnel record created successfully', [
                'application_personnel_id' => $applicationPersonnel->application_personnel_id,
                'application_id' => $applicationPersonnel->application_id,
                'lydopers_id' => $applicationPersonnel->lydopers_id,
                'remarks' => $applicationPersonnel->remarks // Log the actual value
            ]);

            DB::commit();

            Log::info('=== APPLICATION SUBMISSION COMPLETED SUCCESSFULLY ===');

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'data' => [
                    'applicant' => $applicant,
                    'application' => $this->addFileUrlsToApplication($application),
                    'application_personnel' => $applicationPersonnel,
                    'submission_date' => $application->date_submitted
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('APPLICATION SUBMISSION FAILED: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Application submission failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update applicant info.
     */
    public function update(Request $request, $id)
    {
        try {
            $applicant = Applicant::find($id);

            if (!$applicant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant not found.'
                ], 404);
            }

            $validated = $request->validate([
                'applicant_fname' => 'sometimes|required|string|max:255',
                'applicant_mname' => 'nullable|string|max:255',
                'applicant_lname' => 'sometimes|required|string|max:255',
                'applicant_suffix' => 'nullable|string|max:50',
                'applicant_gender' => 'sometimes|required|string|max:50',
                'applicant_bdate' => 'sometimes|required|date',
                'applicant_civil_status' => 'sometimes|required|string|max:50',
                'applicant_pob' => 'nullable|string|max:255',
                'applicant_brgy' => 'sometimes|required|string|max:255',
                'applicant_email' => 'sometimes|required|email|unique:tbl_applicant,applicant_email,' . $id . ',applicant_id',
                'applicant_contact_number' => 'sometimes|required|string|max:20',
                'applicant_school_name' => 'sometimes|required|string|max:255',
                'applicant_year_level' => 'sometimes|required|string|max:50',
                'applicant_course' => 'sometimes|required|string|max:255',
                'applicant_acad_year' => 'sometimes|required|string|max:20',
            ]);

            $applicant->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Applicant updated successfully.',
                'data' => $applicant
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating applicant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update applicant.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an applicant and their application.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $applicant = Applicant::with('application')->find($id);

            if (!$applicant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant not found.'
                ], 404);
            }

            // Delete associated application
            if ($applicant->application) {
                $applicant->application->delete();
                Log::info('Application deleted', ['application_id' => $applicant->application->application_id]);
            }

            // Delete applicant
            $applicant->delete();
            Log::info('Applicant deleted', ['applicant_id' => $id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Applicant and associated application deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting applicant: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete applicant.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}