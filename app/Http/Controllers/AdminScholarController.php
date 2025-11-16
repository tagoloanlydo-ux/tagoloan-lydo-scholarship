<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminScholarController extends Controller
{
public function scholar(Request $request)
{
    // Get scholars with applicant information - include both active and inactive
    $query = DB::table('tbl_scholar as s')
        ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
        ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
        ->select(
            's.scholar_id',
            's.scholar_status',
            's.date_activated',
            'a.applicant_id',
            'a.applicant_fname',
            'a.applicant_mname',
            'a.applicant_lname',
            'a.applicant_suffix',
            'a.applicant_brgy',
            'a.applicant_email',
            'a.applicant_contact_number',
            'a.applicant_school_name',
            'a.applicant_course',
            'a.applicant_year_level',
            'a.applicant_acad_year'
        );

    // Apply status filter - default to active
    $statusFilter = $request->get('status', 'active');
    if ($statusFilter === 'active') {
        $query->where('s.scholar_status', 'active');
    } elseif ($statusFilter === 'inactive') {
        $query->where('s.scholar_status', 'inactive');
    }
    // If 'all' is selected, show both active and inactive

    // Apply other filters
    if ($request->has('search') && !empty($request->search)) {
        $query->where(function($q) use ($request) {
            $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
              ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('barangay') && !empty($request->barangay)) {
        $query->where('a.applicant_brgy', $request->barangay);
    }

    if ($request->has('academic_year') && !empty($request->academic_year)) {
        $query->where('a.applicant_acad_year', $request->academic_year);
    }

   $scholars = $query->get();

    // Get distinct barangays for filter dropdown
    $barangays = DB::table('tbl_applicant')
        ->select('applicant_brgy')
        ->distinct()
        ->orderBy('applicant_brgy', 'asc')
        ->pluck('applicant_brgy');

    // Get distinct academic years for filter dropdown
    $academicYears = DB::table('tbl_applicant')
        ->select('applicant_acad_year')
        ->distinct()
        ->orderBy('applicant_acad_year', 'desc')
        ->pluck('applicant_acad_year');

    return view('lydo_admin.scholar', compact( 'scholars', 'barangays', 'academicYears', 'statusFilter'));
}
public function getScholarDocuments($scholar_id)
{
    try {
        $documents = DB::table('tbl_renewal')
            ->where('scholar_id', $scholar_id)
            ->select(
                'renewal_cert_of_reg',
                'renewal_grade_slip',
                'renewal_brgy_indigency',
                'renewal_semester',
                'renewal_acad_year',
                'date_submitted',
                'renewal_status'
            )
            ->get();

        // Process documents to generate proper URLs
        $processedDocuments = $documents->map(function ($doc) {
            return [
                'renewal_cert_of_reg' => $this->getDocumentUrl($doc->renewal_cert_of_reg),
                'renewal_grade_slip' => $this->getDocumentUrl($doc->renewal_grade_slip),
                'renewal_brgy_indigency' => $this->getDocumentUrl($doc->renewal_brgy_indigency),
                'renewal_semester' => $doc->renewal_semester,
                'renewal_acad_year' => $doc->renewal_acad_year,
                'date_submitted' => $doc->date_submitted,
                'renewal_status' => $doc->renewal_status,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $processedDocuments
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching documents: ' . $e->getMessage()
        ], 500);
    }
}
// Helper method to generate proper document URLs
private function getDocumentUrl($filePath)
{
    if (empty($filePath)) {
        return null;
    }

    // If it's already a full URL, return as is
    if (filter_var($filePath, FILTER_VALIDATE_URL)) {
        return $filePath;
    }

    // If it starts with storage/, convert to proper URL
    if (strpos($filePath, 'storage/') === 0) {
        return asset($filePath);
    }

    // If it's just a filename, assume it's in storage/renewal
    if (!str_contains($filePath, '/')) {
        return asset('storage/renewal/' . $filePath);
    }

    // For other cases, try to generate URL
    return asset('storage/' . ltrim($filePath, '/'));
}

    public function sendEmailToScholars(Request $request)
    {
        $request->validate([
            'selected_emails' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_type' => 'required|string|in:bulk,individual',
        ]);

        $selectedEmails = explode(',', $request->selected_emails);
        $selectedEmails = array_map('trim', $selectedEmails);
        $subject = $request->subject;
        $message = $request->message;
        $sendType = $request->send_type;

        try {
            // Get scholar details for the selected emails
            $scholars = DB::table('tbl_scholar as s')
                ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
                ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
                ->whereIn('a.applicant_email', $selectedEmails)
                ->where('s.scholar_status', 'active')
                ->select(
                    'a.applicant_email',
                    'a.applicant_fname',
                    'a.applicant_lname',
                    'a.applicant_mname',
                    'a.applicant_suffix'
                )
                ->get();

            if ($scholars->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No valid scholars found for the selected emails.']);
            }

            $sentCount = 0;

            if ($sendType === 'bulk') {
                // Send one email to all scholars
                $recipientEmails = $scholars->pluck('applicant_email')->toArray();

                Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $message], function ($mail) use ($recipientEmails, $subject) {
                    $mail->to($recipientEmails)
                         ->subject($subject);
                });

                $sentCount = count($recipientEmails);
            } else {
                // Send individual emails
                foreach ($scholars as $scholar) {
                    $personalizedMessage = $message;
                    $fullName = $scholar->applicant_fname . ' ' . ($scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '') . $scholar->applicant_lname . ($scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '');

                    // Replace placeholders if any
                    $personalizedMessage = str_replace('{name}', $fullName, $personalizedMessage);

                    Mail::send('emails.plain-email', ['subject' => $subject, 'emailMessage' => $personalizedMessage], function ($mail) use ($scholar, $subject) {
                        $mail->to($scholar->applicant_email)
                             ->subject($subject);
                    });

                    $sentCount++;
                }
            }

            return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . $sentCount . ' scholar(s)!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }
public function generateScholarsPdf(Request $request)
{
    try {
        // Set time limit for PDF generation
        set_time_limit(120); // 2 minutes
        
        // Get scholars with applicant information - same query as scholar method
        $query = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select(
                's.scholar_id',
                's.scholar_status',
                's.date_activated',
                'a.applicant_id',
                'a.applicant_fname',
                'a.applicant_mname',
                'a.applicant_lname',
                'a.applicant_suffix',
                'a.applicant_brgy',
                'a.applicant_email',
                'a.applicant_contact_number',
                'a.applicant_school_name',
                'a.applicant_course',
                'a.applicant_year_level',
                'a.applicant_acad_year'
            );

        // Apply status filter
        $statusFilter = $request->get('status', 'active');
        if ($statusFilter === 'active') {
            $query->where('s.scholar_status', 'active');
        } elseif ($statusFilter === 'inactive') {
            $query->where('s.scholar_status', 'inactive');
        }

        // Apply other filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('a.applicant_acad_year', $request->academic_year);
        }

        $scholars = $query->get();

        // Get filter info for page title
        $filters = [];
        if ($request->search) {
            $filters[] = 'Search: ' . $request->search;
        }
        if ($request->barangay) {
            $filters[] = 'Barangay: ' . $request->barangay;
        }
        if ($request->academic_year) {
            $filters[] = 'Academic Year: ' . $request->academic_year;
        }
        if ($request->status) {
            $filters[] = 'Status: ' . ucfirst($request->status);
        }

        $pdf = Pdf::loadView('pdf.scholars-print', compact('scholars', 'filters'))
            ->setPaper('a4', 'portrait'); // Changed from 'landscape' to 'portrait'

        return $pdf->stream('scholars-list-' . date('Y-m-d') . '.pdf');
        
    } catch (\Exception $e) {
        \Log::error('PDF Generation Error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
    }
}
    public function getAllFilteredScholars(Request $request)
    {
        $query = DB::table('tbl_scholar as s')
            ->join('tbl_application as app', 's.application_id', '=', 'app.application_id')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->select('s.scholar_id', 'a.applicant_email')
            ->where('s.scholar_status', 'active');

        // Apply the same filters as the main scholar method
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('a.applicant_fname', 'like', '%' . $request->search . '%')
                  ->orWhere('a.applicant_lname', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('barangay') && !empty($request->barangay)) {
            $query->where('a.applicant_brgy', $request->barangay);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('a.applicant_acad_year', $request->academic_year);
        }

        // Get all scholar emails that match the filters
        $scholarEmails = $query->pluck('a.applicant_email');

        return response()->json(['scholar_emails' => $scholarEmails]);
    }

}
