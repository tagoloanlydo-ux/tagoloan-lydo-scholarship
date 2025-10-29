<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the applicants.
     */
    public function index()
    {
        $applicants = Applicant::all();

        return response()->json([
            'success' => true,
            'message' => 'Applicants retrieved successfully.',
            'data' => $applicants
        ], 200);
    }

    /**
     * Store a newly created applicant.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_fname' => 'required|string|max:255',
            'applicant_lname' => 'required|string|max:255',
            'applicant_gender' => 'required|string',
            'applicant_bdate' => 'required|date',
            'applicant_email' => 'required|email|unique:tbl_applicant,applicant_email',
            'applicant_contact_number' => 'required|string|max:20',
            'applicant_school_name' => 'required|string|max:255',
            'applicant_year_level' => 'required|string|max:50',
            'applicant_course' => 'required|string|max:255',
            'applicant_acad_year' => 'required|string|max:20',
        ]);

        $applicant = Applicant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Applicant created successfully.',
            'data' => $applicant
        ], 201);
    }

    /**
     * Display the specified applicant.
     */
    public function show($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $applicant
        ], 200);
    }

    /**
     * Update the specified applicant.
     */
    public function update(Request $request, $id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found.'
            ], 404);
        }

        $validated = $request->validate([
            'applicant_fname' => 'sometimes|required|string|max:255',
            'applicant_lname' => 'sometimes|required|string|max:255',
            'applicant_gender' => 'sometimes|required|string',
            'applicant_bdate' => 'sometimes|required|date',
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
    }

    /**
     * Remove the specified applicant.
     */
    public function destroy($id)
    {
        $applicant = Applicant::find($id);

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant not found.'
            ], 404);
        }

        $applicant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Applicant deleted successfully.'
        ], 200);
    }
}
