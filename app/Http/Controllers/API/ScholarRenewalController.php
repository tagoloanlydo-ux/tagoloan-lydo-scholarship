<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Renewal;

class ScholarRenewalController extends Controller
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

    public function index(Request $request)
    {
        $query = Renewal::with(['scholar.applicant']);

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('scholar.applicant', function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('renewal_acad_year', $request->academic_year);
        }

        if ($request->has('semester') && !empty($request->semester)) {
            $query->where('renewal_semester', $request->semester);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('renewal_status', $request->status);
        }

        $renewals = $query->paginate(15);

        return $this->paginatedResponse($renewals, 'Renewals retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scholar_id' => 'required|exists:tbl_scholar,scholar_id',
            'renewal_cert_of_reg' => 'required|string',
            'renewal_grade_slip' => 'required|string',
            'renewal_brgy_indigency' => 'required|string',
            'renewal_semester' => 'required|string|max:20',
            'renewal_acad_year' => 'required|string|max:20',
            'renewal_start_date' => 'nullable|date',
            'renewal_deadline' => 'nullable|date',
            'date_submitted' => 'required|date',
            'renewal_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $renewal = Renewal::create($request->all());
            return $this->successResponse($renewal, 'Renewal created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create renewal: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $renewal = Renewal::with(['scholar.applicant'])->find($id);

        if (!$renewal) {
            return $this->errorResponse('Renewal not found', 404);
        }

        return $this->successResponse($renewal, 'Renewal retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $renewal = Renewal::find($id);

        if (!$renewal) {
            return $this->errorResponse('Renewal not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'scholar_id' => 'required|exists:tbl_scholar,scholar_id',
            'renewal_cert_of_reg' => 'required|string',
            'renewal_grade_slip' => 'required|string',
            'renewal_brgy_indigency' => 'required|string',
            'renewal_semester' => 'required|string|max:20',
            'renewal_acad_year' => 'required|string|max:20',
            'renewal_start_date' => 'nullable|date',
            'renewal_deadline' => 'nullable|date',
            'date_submitted' => 'required|date',
            'renewal_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $renewal->update($request->all());
            return $this->successResponse($renewal, 'Renewal updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update renewal: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $renewal = Renewal::find($id);

        if (!$renewal) {
            return $this->errorResponse('Renewal not found', 404);
        }

        try {
            $renewal->delete();
            return $this->successResponse(null, 'Renewal deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete renewal: ' . $e->getMessage(), 500);
        }
    }

    public function pendingCount()
    {
        $pendingCount = Renewal::where('renewal_status', 'Pending')->count();

        return $this->successResponse(['pending_count' => $pendingCount], 'Pending count retrieved successfully');
    }

    public function getScholarRenewals(Request $request)
    {
        try {
            // Get the authenticated scholar
            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Assuming scholar_id is stored in user table or related
            // You may need to adjust based on your user-scholar relationship
            $scholarId = $user->scholar_id ?? $user->id; // Adjust as needed

            $renewals = Renewal::where('scholar_id', $scholarId)
                ->with(['scholar.applicant'])
                ->orderBy('date_submitted', 'desc')
                ->get();

            // Get settings for renewal availability
            $settings = \App\Models\Settings::first();

            return $this->successResponse([
                'renewal' => $renewals->first(), // Return the latest renewal
                'settings' => $settings
            ], 'Scholar renewal data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve renewals: ' . $e->getMessage(), 500);
        }
    }

    public function submitScholarRenewal(Request $request)
    {
        try {
            // Get the authenticated scholar
            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $scholarId = $user->scholar_id ?? $user->id; // Adjust as needed

            $validator = Validator::make($request->all(), [
                'semester' => 'required|string|max:20',
                'academic_year' => 'required|string|max:20',
                'year_level' => 'required|string|max:20',
                'document_types' => 'required|array',
                'document_types.*' => 'string',
                // Files will be handled separately
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator);
            }

            // Handle file uploads
            $uploadedFiles = [];
            $documentTypes = $request->input('document_types', []);

            // Map document types to file fields (adjust based on your needs)
            $fileFields = [
                'cert_of_reg' => 'cor_file',
                'grade_slip' => 'grade_slip_file',
                'brgy_indigency' => 'indigency_file',
            ];

            foreach ($documentTypes as $type) {
                if (isset($fileFields[$type]) && $request->hasFile($fileFields[$type])) {
                    $file = $request->file($fileFields[$type]);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('renewals', $filename, 'public');
                    $uploadedFiles[$type] = $path;
                }
            }

            // Create renewal record
            $renewal = Renewal::create([
                'scholar_id' => $scholarId,
                'renewal_cert_of_reg' => $uploadedFiles['cert_of_reg'] ?? null,
                'renewal_grade_slip' => $uploadedFiles['grade_slip'] ?? null,
                'renewal_brgy_indigency' => $uploadedFiles['brgy_indigency'] ?? null,
                'renewal_semester' => $request->input('semester'),
                'renewal_acad_year' => $request->input('academic_year'),
                'renewal_year_level' => $request->input('year_level'),
                'date_submitted' => now(),
                'renewal_status' => 'Pending',
            ]);

            return $this->successResponse($renewal, 'Renewal submitted successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit renewal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get renewal history for authenticated scholar (API version of renewalHistory())
     */
    public function getRenewalHistory(Request $request)
    {
        try {
            // Get the authenticated scholar
            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Map user to scholar (adjust based on your user-scholar relationship)
            $scholarId = $user->scholar_id ?? $user->id; // Adjust as needed

            // Get all renewals for this scholar, ordered by most recent first
            $renewals = Renewal::where('scholar_id', $scholarId)
                ->orderBy('renewal_acad_year', 'desc')
                ->orderBy('renewal_semester', 'desc')
                ->orderBy('date_submitted', 'desc')
                ->paginate(10);

            // Format data for mobile app
            $formattedRenewals = $renewals->map(function ($renewal) {
                return [
                    'renewal_id' => $renewal->renewal_id,
                    'academic_year' => $renewal->renewal_acad_year,
                    'semester' => $renewal->renewal_semester,
                    'year_level' => $renewal->renewal_year_level ?? $renewal->applicant_year_level ?? 'N/A',
                    'date_submitted' => $renewal->date_submitted->format('M d, Y'),
                    'status' => $renewal->renewal_status,
                    'remarks' => $renewal->remarks ?? null,
                ];
            });

            return $this->paginatedResponse([
                'renewals' => $formattedRenewals,
                'pagination' => [
                    'current_page' => $renewals->currentPage(),
                    'last_page' => $renewals->lastPage(),
                    'per_page' => $renewals->perPage(),
                    'total' => $renewals->total(),
                    'next_page_url' => $renewals->nextPageUrl(),
                    'prev_page_url' => $renewals->previousPageUrl(),
                ]
            ], 'Renewal history retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve renewal history: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get renewal details for authenticated scholar (API version of getRenewalDetails($renewalId))
     */
    public function getRenewalDetails($renewalId)
    {
        try {
            // Get the authenticated scholar
            $user = auth()->user();
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Map user to scholar (adjust based on your user-scholar relationship)
            $scholarId = $user->scholar_id ?? $user->id; // Adjust as needed

            $renewal = Renewal::where('renewal_id', $renewalId)
                ->where('scholar_id', $scholarId)
                ->first();

            if (!$renewal) {
                return $this->errorResponse('Renewal not found', 404);
            }

            // Format data for mobile app
            $formattedRenewal = [
                'renewal_id' => $renewal->renewal_id,
                'academic_year' => $renewal->renewal_acad_year,
                'semester' => $renewal->renewal_semester,
                'year_level' => $renewal->renewal_year_level ?? $renewal->applicant_year_level ?? 'N/A',
                'date_submitted' => $renewal->date_submitted->toISOString(),
                'status' => $renewal->renewal_status,
                'remarks' => $renewal->remarks ?? null,
                'documents' => [
                    'certificate_of_registration' => [
                        'path' => $renewal->renewal_cert_of_reg ? url('storage/' . $renewal->renewal_cert_of_reg) : null,
                        'status' => $renewal->cert_of_reg_status ?? 'Not reviewed',
                    ],
                    'grade_slip' => [
                        'path' => $renewal->renewal_grade_slip ? url('storage/' . $renewal->renewal_grade_slip) : null,
                        'status' => $renewal->grade_slip_status ?? 'Not reviewed',
                    ],
                    'barangay_indigency' => [
                        'path' => $renewal->renewal_brgy_indigency ? url('storage/' . $renewal->renewal_brgy_indigency) : null,
                        'status' => $renewal->brgy_indigency_status ?? 'Not reviewed',
                    ],
                ],
            ];

            return $this->successResponse($formattedRenewal, 'Renewal details retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve renewal details: ' . $e->getMessage(), 500);
        }
    }
}
