<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Applicant;

class ApplicantController extends ApiController
{
    public function index(Request $request)
    {
        $query = Applicant::query();

        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_lname', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('applicant_acad_year', $request->academic_year);
        }

        $applicants = $query->paginate(15);

        return $this->paginatedResponse($applicants, 'Applicants retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_fname' => 'required|string|max:50',
            'applicant_mname' => 'nullable|string|max:50',
            'applicant_lname' => 'required|string|max:50',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'required|string|max:10',
            'applicant_bdate' => 'required|date',
            'applicant_civil_status' => 'required|string|max:20',
            'applicant_brgy' => 'required|string|max:100',
            'applicant_email' => 'required|email|max:100|unique:tbl_applicant',
            'applicant_contact_number' => 'required|string|max:20',
            'applicant_school_name' => 'required|string|max:100',
            'applicant_year_level' => 'required|string|max:20',
            'applicant_course' => 'required|string|max:100',
            'applicant_acad_year' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $applicant = Applicant::create($request->all());

            // Create application record
            $application = DB::table('tbl_application')->insertGetId([
                'applicant_id' => $applicant->applicant_id,
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

            return $this->successResponse($applicant, 'Applicant created successfully', 201);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Failed to create applicant: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return $this->errorResponse('Applicant not found', 404);
        }

        return $this->successResponse($applicant, 'Applicant retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return $this->errorResponse('Applicant not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'applicant_fname' => 'required|string|max:50',
            'applicant_mname' => 'nullable|string|max:50',
            'applicant_lname' => 'required|string|max:50',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'required|string|max:10',
            'applicant_bdate' => 'required|date',
            'applicant_civil_status' => 'required|string|max:20',
            'applicant_brgy' => 'required|string|max:100',
            'applicant_email' => 'required|email|max:100|unique:tbl_applicant,applicant_email,' . $id . ',applicant_id',
            'applicant_contact_number' => 'required|string|max:20',
            'applicant_school_name' => 'required|string|max:100',
            'applicant_year_level' => 'required|string|max:20',
            'applicant_course' => 'required|string|max:100',
            'applicant_acad_year' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $applicant->update($request->all());
            return $this->successResponse($applicant, 'Applicant updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update applicant: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return $this->errorResponse('Applicant not found', 404);
        }

        try {
            DB::beginTransaction();

            // Get application ID
            $application = DB::table('tbl_application')
                ->where('applicant_id', $id)
                ->first();

            if ($application) {
                // Delete application personnel record
                DB::table('tbl_application_personnel')
                    ->where('application_id', $application->application_id)
                    ->delete();

                // Delete application
                DB::table('tbl_application')
                    ->where('application_id', $application->application_id)
                    ->delete();
            }

            // Delete applicant
            $applicant->delete();

            DB::commit();

            return $this->successResponse(null, 'Applicant deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Failed to delete applicant: ' . $e->getMessage(), 500);
        }
    }
}
