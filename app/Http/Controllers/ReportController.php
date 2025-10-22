<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(['message' => 'Reports endpoint']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Report created'], 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Report updated']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Report deleted']);
    }
}
