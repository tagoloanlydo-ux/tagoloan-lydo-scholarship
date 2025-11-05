<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Renewal;

class RenewalController extends Controller
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
}
