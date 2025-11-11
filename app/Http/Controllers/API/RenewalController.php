<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Renewal;

class RenewalController extends Controller
{
    public function index(Request $request)
    {
        $renewals = Renewal::with(['scholar'])->paginate(15);
        return response()->json($renewals);
    }

    public function store(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get scholar ID - adjust based on your user-scholar relationship
            $scholarId = $user->scholar_id ?? $user->id;

            $validator = Validator::make($request->all(), [
                'semester' => 'required|string|max:20',
                'academic_year' => 'required|string|max:20',
                'year_level' => 'required|string|max:20',
                'document_types' => 'required|array',
                'document_types.*' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle file uploads - store files and get paths
            $uploadedFiles = [];
            $documentTypes = $request->input('document_types', []);

            // Map document types to file fields and database columns
            $fileMapping = [
                'cert_of_reg' => [
                    'field' => 'cor_file',
                    'db_column' => 'renewal_cert_of_reg'
                ],
                'grade_slip' => [
                    'field' => 'grade_slip_file',
                    'db_column' => 'renewal_grade_slip'
                ],
                'brgy_indigency' => [
                    'field' => 'indigency_file',
                    'db_column' => 'renewal_brgy_indigency'
                ],
            ];

            foreach ($documentTypes as $type) {
                if (isset($fileMapping[$type]) && $request->hasFile($fileMapping[$type]['field'])) {
                    $file = $request->file($fileMapping[$type]['field']);

                    // Validate file
                    if (!$file->isValid()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid file uploaded for {$type}"
                        ], 422);
                    }

                    // Validate file size (5MB)
                    if ($file->getSize() > 5 * 1024 * 1024) {
                        return response()->json([
                            'success' => false,
                            'message' => "File too large for {$type}. Maximum size is 5MB."
                        ], 422);
                    }

                    // Validate file type
                    $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png'];
                    if (!in_array($file->getClientOriginalExtension(), $allowedMimes)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid file type for {$type}. Allowed: PDF, JPG, PNG."
                        ], 422);
                    }

                    // Generate unique filename
                    $filename = 'renewal_' . $scholarId . '_' . $type . '_' . time() . '.' . $file->getClientOriginalExtension();

                    // Store file in storage/app/public/renewals
                    $path = $file->storeAs('renewals', $filename, 'public');

                    $uploadedFiles[$fileMapping[$type]['db_column']] = $path;
                }
            }

            // Check if all required files are uploaded
            $requiredFiles = ['renewal_cert_of_reg', 'renewal_grade_slip', 'renewal_brgy_indigency'];
            $missingFiles = array_diff($requiredFiles, array_keys($uploadedFiles));

            if (!empty($missingFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required documents: ' . implode(', ', $missingFiles)
                ], 422);
            }

            // Create renewal record with file paths (not file content)
            $renewalData = [
                'scholar_id' => $scholarId,
                'renewal_semester' => $request->input('semester'),
                'renewal_acad_year' => $request->input('academic_year'),
                'renewal_year_level' => $request->input('year_level'),
                'date_submitted' => now(),
                'renewal_status' => 'Pending',
                'updated_at' => now(),
                'created_at' => now(),
            ];

            // Merge file paths into renewal data
            $renewalData = array_merge($renewalData, $uploadedFiles);

            // Create renewal record
            $renewal = Renewal::create($renewalData);

            return response()->json([
                'success' => true,
                'message' => 'Renewal submitted successfully',
                'data' => $renewal
            ], 201);

        } catch (\Exception $e) {
            Log::error('Renewal submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit renewal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $renewal = Renewal::with(['scholar'])->find($id);
        return response()->json($renewal);
    }

    public function update(Request $request, $id)
    {
        $renewal = Renewal::find($id);
        $renewal->update($request->all());
        return response()->json($renewal);
    }

    public function destroy($id)
    {
        Renewal::destroy($id);
        return response()->json(['message' => 'Renewal deleted']);
    }

    public function pendingCount()
    {
        $count = Renewal::where('renewal_status', 'Pending')->count();
        return response()->json(['count' => $count]);
    }

    public function getRequirements($id)
    {
        $renewal = Renewal::find($id);
        return response()->json([
            'renewal_cert_of_reg' => $renewal->renewal_cert_of_reg,
            'renewal_grade_slip' => $renewal->renewal_grade_slip,
            'renewal_brgy_indigency' => $renewal->renewal_brgy_indigency,
        ]);
    }
}
