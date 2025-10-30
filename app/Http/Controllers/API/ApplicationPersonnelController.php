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
        $records = ApplicationPersonnel::all();

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
}
