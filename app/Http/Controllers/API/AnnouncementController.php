<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Announce;

class AnnouncementController extends Controller
{
    /**
     * Success response helper
     */
    protected function successResponse($data = [], $message = '', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Error response helper
     */
    protected function errorResponse($message, $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    /**
     * Validation error response helper
     */
    protected function validationErrorResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    /**
     * Paginated response helper
     */
    protected function paginatedResponse($data, $message = '', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public function index(Request $request)
    {
        $query = Announce::query();

        // Apply filters
        if ($request->has('type') && !empty($request->type)) {
            $query->where('announce_type', $request->type);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('announce_title', 'like', '%' . $request->search . '%')
                  ->orWhere('announce_content', 'like', '%' . $request->search . '%');
            });
        }

        $announcements = $query->orderBy('date_posted', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);

        return $this->paginatedResponse($announcements, 'Announcements retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|string|in:applicants,scholars',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $announcement = Announce::create([
                'lydopers_id' => auth()->id() ?? 1, // Default to 1 if not authenticated
                'announce_title' => $request->announce_title,
                'announce_content' => $request->announce_content,
                'announce_type' => $request->announce_type,
                'date_posted' => now(),
            ]);

            return $this->successResponse($announcement, 'Announcement created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create announcement: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $announcement = Announce::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        return $this->successResponse($announcement, 'Announcement retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announce::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|string|in:applicants,scholars',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $announcement->update($request->all());
            return $this->successResponse($announcement, 'Announcement updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update announcement: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $announcement = Announce::find($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        try {
            $announcement->delete();
            return $this->successResponse(null, 'Announcement deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete announcement: ' . $e->getMessage(), 500);
        }
    }
}
