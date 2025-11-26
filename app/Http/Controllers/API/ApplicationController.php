<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationPersonnel;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Store a new application with file uploads
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'applicant_id' => 'required|integer|exists:tbl_applicant,applicant_id',
                'application_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'cert_of_reg' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'grade_slip' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'brgy_indigency' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'student_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            // Process file uploads
            $filePaths = [];
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
                    
                    // Generate unique filename
                    $filename = time() . '_' . uniqid() . '_' . $field . '.' . $file->getClientOriginalExtension();
                    
                    // ✅ Store files in the exact staging location
                    $customPath = 'staging/storage/documents/' . $filename;
                    
                    // Use the local disk and custom path
                    Storage::disk('local')->put($customPath, file_get_contents($file));
                    
                    // Save the custom path for database
                    $filePaths[$field] = $customPath;
                }
            }

            // Create application record
            $application = Application::create([
                'applicant_id' => $request->applicant_id,
                'application_letter' => $filePaths['application_letter'],
                'cert_of_reg' => $filePaths['cert_of_reg'],
                'grade_slip' => $filePaths['grade_slip'],
                'brgy_indigency' => $filePaths['brgy_indigency'],
                'student_id' => $filePaths['student_id'],
                'date_submitted' => now()->format('Y-m-d'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'data' => $application,
                'file_paths' => $filePaths // For debugging
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file URL for accessing uploaded documents
     */
    public function getFile($filename)
    {
        try {
            $path = 'staging/storage/documents/' . $filename;
            
            if (!Storage::disk('local')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.'
                ], 404);
            }

            // Return the file
            return Storage::disk('local')->response($path);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve file.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get full file URL (for Flutter app)
     */
    public function getFileUrl($filename)
    {
        try {
            $path = 'staging/storage/documents/' . $filename;
            
            if (!Storage::disk('local')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.'
                ], 404);
            }

            // Return the direct URL
            $fullUrl = 'https://srv1278-files.hstgr.io/3d66eae9e48136e2/files/public_html/' . $path;
            
            return response()->json([
                'success' => true,
                'url' => $fullUrl
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get file URL.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display all applications with applicant details.
     */
    public function index()
    {
        try {
            $applications = Application::with('applicant')->get();

            // Add full file URLs to each application
            $applicationsWithUrls = $applications->map(function ($application) {
                $personnel = ApplicationPersonnel::where('application_id', $application->application_id)->first();
                $application->application_personnel = $personnel;
                
                // Add full URLs for file access in Flutter
                $application->application_letter_url = $this->generateFileUrl($application->application_letter);
                $application->cert_of_reg_url = $this->generateFileUrl($application->cert_of_reg);
                $application->grade_slip_url = $this->generateFileUrl($application->grade_slip);
                $application->brgy_indigency_url = $this->generateFileUrl($application->brgy_indigency);
                $application->student_id_url = $this->generateFileUrl($application->student_id);
                
                return $application;
            });

            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully.',
                'data' => $applicationsWithUrls,
                'count' => $applicationsWithUrls->count()
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

            // Add full URLs for file access
            $application->application_letter_url = $this->generateFileUrl($application->application_letter);
            $application->cert_of_reg_url = $this->generateFileUrl($application->cert_of_reg);
            $application->grade_slip_url = $this->generateFileUrl($application->grade_slip);
            $application->brgy_indigency_url = $this->generateFileUrl($application->brgy_indigency);
            $application->student_id_url = $this->generateFileUrl($application->student_id);

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
     * Helper function to generate full file URLs
     */
    private function generateFileUrl($filePath)
    {
        if (!$filePath) return null;
        
        // Extract just the filename from the path
        $filename = basename($filePath);
        
        // Return the full direct URL
        return 'https://srv1278-files.hstgr.io/3d66eae9e48136e2/files/public_html/staging/storage/documents/' . $filename;
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