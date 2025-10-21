<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Scholar;

class ScholarController extends ApiController
{
    public function index(Request $request)
    {
        $query = Scholar::with(['applicant']);

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('applicant', function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('scholar_status', $request->status);
        }

        $scholars = $query->paginate(15);

        return $this->paginatedResponse($scholars, 'Scholars retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:tbl_application,application_id',
            'scholar_username' => 'nullable|string|max:50|unique:tbl_scholar',
            'scholar_pass' => 'nullable|string',
            'date_activated' => 'required|date',
            'scholar_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $scholar = Scholar::create($request->all());
            return $this->successResponse($scholar, 'Scholar created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create scholar: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $scholar = Scholar::with(['applicant'])->find($id);

        if (!$scholar) {
            return $this->errorResponse('Scholar not found', 404);
        }

        return $this->successResponse($scholar, 'Scholar retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $scholar = Scholar::find($id);

        if (!$scholar) {
            return $this->errorResponse('Scholar not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:tbl_application,application_id',
            'scholar_username' => 'nullable|string|max:50|unique:tbl_scholar,scholar_username,' . $id . ',scholar_id',
            'scholar_pass' => 'nullable|string',
            'date_activated' => 'required|date',
            'scholar_status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $scholar->update($request->all());
            return $this->successResponse($scholar, 'Scholar updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update scholar: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $scholar = Scholar::find($id);

        if (!$scholar) {
            return $this->errorResponse('Scholar not found', 404);
        }

        try {
            $scholar->delete();
            return $this->successResponse(null, 'Scholar deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete scholar: ' . $e->getMessage(), 500);
        }
    }
}
