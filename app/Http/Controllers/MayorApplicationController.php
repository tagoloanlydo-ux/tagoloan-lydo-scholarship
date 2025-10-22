<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationPersonnel;

class MayorApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = ApplicationPersonnel::with(['applicant'])->paginate(15);
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
