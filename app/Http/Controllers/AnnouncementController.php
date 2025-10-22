<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announce;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announce::paginate(15);
        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $announcement = Announce::create($request->all());
        return response()->json($announcement, 201);
    }

    public function show($id)
    {
        $announcement = Announce::find($id);
        return response()->json($announcement);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announce::find($id);
        $announcement->update($request->all());
        return response()->json($announcement);
    }

    public function destroy($id)
    {
        Announce::destroy($id);
        return response()->json(['message' => 'Announcement deleted']);
    }

    public function scholars()
    {
        $announcements = Announce::where('announce_type', 'Scholars')->get();
        return response()->json($announcements);
    }

    public function applicants()
    {
        $announcements = Announce::where('announce_type', 'Applicants')->get();
        return response()->json($announcements);
    }
}
