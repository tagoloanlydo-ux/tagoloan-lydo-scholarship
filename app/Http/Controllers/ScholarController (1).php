<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholar;

class ScholarController extends Controller
{
    public function index(Request $request)
    {
        $scholars = Scholar::with(['applicant'])->paginate(15);
        return response()->json($scholars);
    }

    public function store(Request $request)
    {
        $scholar = Scholar::create($request->all());
        return response()->json($scholar, 201);
    }

    public function show($id)
    {
        $scholar = Scholar::with(['applicant'])->find($id);
        return response()->json($scholar);
    }

    public function update(Request $request, $id)
    {
        $scholar = Scholar::find($id);
        $scholar->update($request->all());
        return response()->json($scholar);
    }

    public function destroy($id)
    {
        Scholar::destroy($id);
        return response()->json(['message' => 'Scholar deleted']);
    }

    public function count()
    {
        $count = Scholar::count();
        return response()->json(['count' => $count]);
    }

    public function inactiveCount()
    {
        $count = Scholar::where('scholar_status', 'Inactive')->count();
        return response()->json(['count' => $count]);
    }

    public function updateProfile(Request $request, $id)
    {
        $scholar = Scholar::find($id);
        $scholar->update($request->all());
        return response()->json($scholar);
    }

    public function updateStatus(Request $request, $id)
    {
        $scholar = Scholar::find($id);
        $scholar->update(['scholar_status' => $request->status]);
        return response()->json($scholar);
    }
}