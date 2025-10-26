<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationPersonnel;

class MayorApplicationController extends Controller
{
    public function index(Request $request)
    {
        // Build query and apply search filters BEFORE pagination so the
        // search is performed across all records in the database.
        $query = ApplicationPersonnel::with(['applicant']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('applicant_fname', 'like', "%{$search}%")
                  ->orWhere('applicant_lname', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(15)->appends($request->query());

        return response()->json($applications);
    }

    public function store(Request $request)
    {
        $application = ApplicationPersonnel::create($request->all());
        return response()->json($application, 201);
    }

    public function show($id)
    {
        $application = ApplicationPersonnel::with(['applicant'])->find($id);
        return response()->json($application);
    }

    public function update(Request $request, $id)
    {
        $application = ApplicationPersonnel::find($id);
        $application->update($request->all());
        return response()->json($application);
    }

    public function destroy($id)
    {
        ApplicationPersonnel::destroy($id);
        return response()->json(['message' => 'Application deleted']);
    }

    public function updateStatus(Request $request, $id)
    {
        $application = ApplicationPersonnel::find($id);
        $application->update(['status' => $request->status]);
        return response()->json($application);
    }
}
