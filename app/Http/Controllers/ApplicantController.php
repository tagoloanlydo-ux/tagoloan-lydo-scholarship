<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $applicants = Applicant::paginate(15);
        return response()->json($applicants);
    }

    public function store(Request $request)
    {
        $applicant = Applicant::create($request->all());
        return response()->json($applicant, 201);
    }

    public function show($id)
    {
        $applicant = Applicant::find($id);
        return response()->json($applicant);
    }

    public function update(Request $request, $id)
    {
        $applicant = Applicant::find($id);
        $applicant->update($request->all());
        return response()->json($applicant);
    }

    public function destroy($id)
    {
        Applicant::destroy($id);
        return response()->json(['message' => 'Applicant deleted']);
    }

    public function meta()
    {
        $meta = [
            'total' => Applicant::count(),
            'by_gender' => Applicant::selectRaw('applicant_gender, COUNT(*) as count')->groupBy('applicant_gender')->get(),
            'by_barangay' => Applicant::selectRaw('applicant_brgy, COUNT(*) as count')->groupBy('applicant_brgy')->get(),
        ];
        return response()->json($meta);
    }

    public function distributionByBarangay()
    {
        $distribution = Applicant::selectRaw('applicant_brgy, COUNT(*) as count')
            ->groupBy('applicant_brgy')
            ->orderBy('count', 'desc')
            ->get();
        return response()->json($distribution);
    }

    public function distributionBySchool()
    {
        $distribution = Applicant::selectRaw('applicant_school_name, COUNT(*) as count')
            ->groupBy('applicant_school_name')
            ->orderBy('count', 'desc')
            ->get();
        return response()->json($distribution);
    }
}
