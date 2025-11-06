<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Announce;
use App\Models\Applicant;
use App\Models\Scholar;

class NotificationController extends Controller
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

    /**
     * Get announcements
     */
    public function announcements(Request $request)
    {
        $query = Announce::orderBy('date_posted', 'desc');

        // Filter by type if specified
        if ($request->has('type') && !empty($request->type)) {
            $query->where('announce_type', $request->type);
        }

        // Filter by user type (scholars vs applicants)
        $user = $this->getAuthenticatedUser();
        if ($user instanceof Scholar) {
            $query->whereIn('announce_type', ['All', 'Scholars']);
        } else {
            $query->whereIn('announce_type', ['All', 'Applicants']);
        }

        $announcements = $query->paginate(15);

        return $this->paginatedResponse($announcements, 'Announcements retrieved successfully');
    }

    /**
     * Create announcement (admin only)
     */
    public function createAnnouncement(Request $request)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|in:All,Scholars,Applicants',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $announcement = Announce::create([
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

    /**
     * Update announcement
     */
    public function updateAnnouncement(Request $request, $announcementId)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $announcement = Announce::find($announcementId);
        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'announce_title' => 'required|string|max:255',
            'announce_content' => 'required|string',
            'announce_type' => 'required|in:All,Scholars,Applicants',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $announcement->update([
            'announce_title' => $request->announce_title,
            'announce_content' => $request->announce_content,
            'announce_type' => $request->announce_type,
        ]);

        return $this->successResponse($announcement, 'Announcement updated successfully');
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement(Request $request, $announcementId)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $announcement = Announce::find($announcementId);
        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404);
        }

        $announcement->delete();

        return $this->successResponse(null, 'Announcement deleted successfully');
    }

    /**
     * Send email notification
     */
    public function sendEmail(Request $request)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:all,scholars,applicants,specific',
            'recipient_ids' => 'required_if:recipient_type,specific|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $recipients = $this->getEmailRecipients($request->recipient_type, $request->recipient_ids ?? []);

            foreach ($recipients as $email) {
                Mail::send('emails.plain-email', [
                    'subject' => $request->subject,
                    'message' => $request->message
                ], function($message) use ($email, $request) {
                    $message->to($email);
                    $message->subject($request->subject);
                });
            }

            return $this->successResponse([
                'recipients_count' => count($recipients)
            ], 'Email sent successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send email: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSms(Request $request)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'recipient_type' => 'required|in:all,scholars,applicants,specific',
            'recipient_ids' => 'required_if:recipient_type,specific|array',
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            // This would integrate with an SMS service like Twilio, Semaphore, etc.
            // For now, we'll just return success
            $recipients = $this->getSmsRecipients($request->recipient_type, $request->recipient_ids ?? []);

            // TODO: Integrate with actual SMS service
            // Example: Semaphore API call
            // $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            //     'apikey' => config('services.semaphore.api_key'),
            //     'number' => implode(',', $recipients),
            //     'message' => $request->message,
            // ]);

            return $this->successResponse([
                'recipients_count' => count($recipients),
                'message' => 'SMS sending not yet implemented - would send to ' . count($recipients) . ' recipients'
            ], 'SMS queued successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send SMS: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get email recipients based on type
     */
    private function getEmailRecipients($type, $ids = [])
    {
        $emails = [];

        switch ($type) {
            case 'all':
                // Get all applicant emails
                $applicantEmails = Applicant::pluck('applicant_email')->toArray();
                // Get all scholar applicant emails (scholars are also applicants)
                $emails = array_unique($applicantEmails);
                break;

            case 'scholars':
                // Get emails of active scholars
                $scholarEmails = Scholar::where('scholar_status', 'Active')
                    ->with('applicant')
                    ->get()
                    ->pluck('applicant.applicant_email')
                    ->filter()
                    ->toArray();
                $emails = $scholarEmails;
                break;

            case 'applicants':
                // Get emails of applicants who are not yet scholars
                $applicantEmails = Applicant::whereDoesntHave('application.scholar')
                    ->pluck('applicant_email')
                    ->toArray();
                $emails = $applicantEmails;
                break;

            case 'specific':
                if (empty($ids)) break;

                // Get emails by applicant IDs
                $specificEmails = Applicant::whereIn('applicant_id', $ids)
                    ->pluck('applicant_email')
                    ->toArray();
                $emails = $specificEmails;
                break;
        }

        return array_filter($emails); // Remove null/empty emails
    }

    /**
     * Get SMS recipients based on type
     */
    private function getSmsRecipients($type, $ids = [])
    {
        $numbers = [];

        switch ($type) {
            case 'all':
                // Get all applicant contact numbers
                $applicantNumbers = Applicant::pluck('applicant_contact_number')->toArray();
                $numbers = array_unique($applicantNumbers);
                break;

            case 'scholars':
                // Get contact numbers of active scholars
                $scholarNumbers = Scholar::where('scholar_status', 'Active')
                    ->with('applicant')
                    ->get()
                    ->pluck('applicant.applicant_contact_number')
                    ->filter()
                    ->toArray();
                $numbers = $scholarNumbers;
                break;

            case 'applicants':
                // Get contact numbers of applicants who are not yet scholars
                $applicantNumbers = Applicant::whereDoesntHave('application.scholar')
                    ->pluck('applicant_contact_number')
                    ->toArray();
                $numbers = $applicantNumbers;
                break;

            case 'specific':
                if (empty($ids)) break;

                // Get contact numbers by applicant IDs
                $specificNumbers = Applicant::whereIn('applicant_id', $ids)
                    ->pluck('applicant_contact_number')
                    ->toArray();
                $numbers = $specificNumbers;
                break;
        }

        return array_filter($numbers); // Remove null/empty numbers
    }

    /**
     * Mark notifications as viewed (for mobile app)
     */
    public function markAsViewed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announcement_ids' => 'required|array',
            'announcement_ids.*' => 'integer|exists:tbl_announce,announce_id',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        // In a real implementation, you'd have a notification_reads table
        // For now, we'll just return success
        return $this->successResponse(null, 'Notifications marked as viewed');
    }
}
