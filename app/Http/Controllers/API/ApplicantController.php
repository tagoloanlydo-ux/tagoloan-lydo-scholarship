<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Applicant;

class ApplicantController extends Controller
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

        return response()->json([
            'success' => true,
            'message' => 'Applicants retrieved successfully',
            'data' => $applicants
        ]);
    }

    public function store(Request $request)
    {
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

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

            $applicant = Applicant::create($applicantData);

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

            return response()->json([
                'success' => true,
                'message' => 'Applicant created successfully',
                'data' => $applicant
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create applicant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Applicant retrieved successfully',
            'data' => $applicant
        ]);
    }

    public function update(Request $request, $id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'applicant_fname' => 'required|string|max:50',
            'applicant_mname' => 'nullable|string|max:50',
            'applicant_lname' => 'required|string|max:50',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'required|string|max:10|in:male,female,other,Male,Female,Other',
            'applicant_bdate' => 'required|date',
            'applicant_civil_status' => 'required|string|max:20|in:single,married,widowed,divorced,Single,Married,Widowed,Divorced',
            'applicant_brgy' => 'required|string|max:100',
            'applicant_email' => 'required|email|max:100|unique:tbl_applicant,applicant_email,' . $id . ',applicant_id',
            'applicant_contact_number' => 'required|string|max:20',
            'applicant_school_name' => 'required|string|max:100',
            'applicant_year_level' => 'required|string|max:20',
            'applicant_course' => 'required|string|max:100',
            'applicant_acad_year' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Normalize data before updating
            $applicantData = $request->all();
            
            // Normalize gender to lowercase
            if (isset($applicantData['applicant_gender'])) {
                $applicantData['applicant_gender'] = strtolower($applicantData['applicant_gender']);
            }
            
            // Normalize civil status to lowercase
            if (isset($applicantData['applicant_civil_status'])) {
                $applicantData['applicant_civil_status'] = strtolower($applicantData['applicant_civil_status']);
            }

            $applicant->update($applicantData);
            
            return response()->json([
                'success' => true,
                'message' => 'Applicant updated successfully',
                'data' => $applicant
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update applicant: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found'
            ], 404);
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

            return response()->json([
                'success' => true,
                'message' => 'Applicant deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete applicant: ' . $e->getMessage()
            ], 500);
        }
    }
}