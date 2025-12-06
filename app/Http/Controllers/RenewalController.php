<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Renewal;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailService;
use App\Events\ApplicantUpdated;
use App\Events\RenewalUpdated;
use Illuminate\Support\Facades\Log;

class RenewalController extends Controller
{
    // ✅ UPDATED: Now handles file uploads for renewal documents
    public function submitScholarRenewal(Request $request)
{
    DB::beginTransaction();
    
    try {
        Log::info('Scholar renewal submission received', $request->all());

        // Validate all fields including files
        $request->validate([
            'scholar_id' => 'required|integer',
            'renewal_semester' => 'required|string',
            'renewal_acad_year' => 'required|string',
            'renewal_cert_of_reg' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'renewal_grade_slip' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'renewal_brgy_indigency' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        Log::info('Creating renewal record...');

        // Prepare renewal data
        $renewalData = [
            'scholar_id' => $request->scholar_id,
            'renewal_semester' => $request->renewal_semester,
            'renewal_acad_year' => $request->renewal_acad_year,
            'renewal_status' => 'Pending',
            'date_submitted' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            
            // ✅ ADD THESE: Set document statuses to "Pending"
            'cert_of_reg_status' => 'Pending',
            'grade_slip_status' => 'Pending', 
            'brgy_indigency_status' => 'Pending',
        ];

        Log::info('Processing renewal document files...');

        // File processing code remains the same...
        $fileFields = [
            'renewal_cert_of_reg',
            'renewal_grade_slip', 
            'renewal_brgy_indigency'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                Log::info("Processing file upload: $field", [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize()
                ]);
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '_' . $field . '.' . $file->getClientOriginalExtension();
                
                // ✅ ABSOLUTE PATH to PRODUCTION storage/documents
                $destinationPath = base_path('../../public_html/storage/documents');
                
                // Create directory if it doesn't exist
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                    Log::info("Created PRODUCTION directory: $destinationPath");
                }
                
                // Move file to destination
                if ($file->move($destinationPath, $filename)) {
                    Log::info("✅ File saved to PRODUCTION: $destinationPath/$filename");
                    $renewalData[$field] = 'documents/' . $filename;
                } else {
                    Log::error("❌ Failed to save file to production: $filename");
                    $renewalData[$field] = '';
                }
            } else {
                Log::warning("No file uploaded for: $field");
                $renewalData[$field] = '';
            }
        }

        // Insert renewal record
        $renewalId = DB::table('tbl_renewal')->insertGetId($renewalData);

        Log::info('Renewal submitted successfully', [
            'renewal_id' => $renewalId,
            'scholar_id' => $request->scholar_id,
            'files_uploaded' => array_filter([
                'cert_of_reg' => !empty($renewalData['renewal_cert_of_reg']),
                'grade_slip' => !empty($renewalData['renewal_grade_slip']),
                'brgy_indigency' => !empty($renewalData['renewal_brgy_indigency']),
            ])
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Renewal submitted successfully',
            'renewal_id' => $renewalId,
            'documents_uploaded' => [
                'cert_of_reg' => !empty($renewalData['renewal_cert_of_reg']),
                'grade_slip' => !empty($renewalData['renewal_grade_slip']),
                'brgy_indigency' => !empty($renewalData['renewal_brgy_indigency']),
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Scholar renewal submission error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit renewal: ' . $e->getMessage()
        ], 500);
    }
}

    // ... REST OF YOUR METHODS REMAIN EXACTLY THE SAME ...

    public function index(Request $request)
    {
        $renewals = Renewal::with(['scholar'])->paginate(15);
        return response()->json($renewals);
    }

    public function store(Request $request)
    {
        $renewal = Renewal::create($request->all());
        return response()->json($renewal, 201);
    }

    public function show($id)
    {
        $renewal = Renewal::with(['scholar'])->find($id);
        return response()->json($renewal);
    }

    public function update(Request $request, $id)
    {
        $renewal = Renewal::find($id);
        $renewal->update($request->all());
        return response()->json($renewal);
    }

    public function destroy($id)
    {
        Renewal::destroy($id);
        return response()->json(['message' => 'Renewal deleted']);
    }

    public function updateRenewalStatus(Request $request, $id)
    {
        try {
            \Log::info("Starting updateRenewalStatus for renewal_id: {$id}", [
                'renewal_status' => $request->renewal_status,
                'reason' => $request->reason
            ]);

            $request->validate([
                "renewal_status" => "required|in:Approved,Rejected",
                "reason" => "nullable|string|max:500",
            ]);

            $updateData = [
                "renewal_status" => $request->renewal_status,
                "updated_at" => now(),
            ];

            if ($request->renewal_status === "Rejected" && $request->filled('reason')) {
                $updateData["rejection_reason"] = $request->reason;
            }

            $updatedRows = DB::table("tbl_renewal")
                ->where("renewal_id", $id)
                ->update($updateData);

            \Log::info("Database update result for renewal_id {$id}: {$updatedRows} rows affected");

            if ($updatedRows === 0) {
                \Log::error("No renewal record found with id: {$id}");
                return response()->json(["success" => false, "message" => "Renewal not found"], 404);
            }

            // Broadcast update
            $currentAcadYear = DB::table("tbl_applicant")
                ->select("applicant_acad_year")
                ->orderBy("applicant_acad_year", "desc")
                ->value("applicant_acad_year");

            $approvedRenewals = DB::table("tbl_renewal")
                ->where("renewal_status", "Approved")
                ->where("renewal_acad_year", $currentAcadYear)
                ->count();

            $pendingRenewals = DB::table("tbl_renewal")
                ->where("renewal_status", "Pending")
                ->where("renewal_acad_year", $currentAcadYear)
                ->count();

            broadcast(new RenewalUpdated('approved_renewals', $approvedRenewals))->toOthers();
            broadcast(new RenewalUpdated('pending_renewals', $pendingRenewals))->toOthers();

            $scholarId = DB::table("tbl_renewal")
                ->where("renewal_id", $id)
                ->value("scholar_id");

            \Log::info("Retrieved scholar_id: {$scholarId} for renewal_id: {$id}");

            if ($scholarId) {
                $applicant = DB::table("tbl_scholar")
                    ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
                    ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
                    ->where("tbl_scholar.scholar_id", $scholarId)
                    ->select("tbl_applicant.applicant_email", "tbl_applicant.applicant_fname", "tbl_applicant.applicant_lname")
                    ->first();

                \Log::info("Applicant query result for scholar_id {$scholarId}:", [
                    'found' => $applicant ? true : false,
                    'email' => $applicant ? $applicant->applicant_email : null
                ]);

                if ($applicant && $applicant->applicant_email) {
                    $emailService = new EmailService();

                    if ($request->renewal_status === "Rejected") {
                        \Log::info("Processing rejection for scholar_id: {$scholarId}");

                        $scholarUpdateResult = DB::table("tbl_scholar")
                            ->where("scholar_id", $scholarId)
                            ->update([
                                "scholar_status" => "Inactive",
                                "updated_at" => now(),
                            ]);

                        \Log::info("Scholar status update result: {$scholarUpdateResult} rows affected");

                        $emailResult = $emailService->sendRejectionEmail($applicant->applicant_email, [
                            'applicant_fname' => $applicant->applicant_fname,
                            'applicant_lname' => $applicant->applicant_lname,
                            'reason' => $request->reason,
                        ]);

                        \Log::info("Rejection email sent result: " . ($emailResult ? 'success' : 'failed'));

                    } elseif ($request->renewal_status === "Approved") {
                        \Log::info("Processing approval for scholar_id: {$scholarId}");

                        $emailResult = $emailService->sendRenewalApprovalEmail($applicant->applicant_email, [
                            'applicant_fname' => $applicant->applicant_fname,
                            'applicant_lname' => $applicant->applicant_lname,
                        ]);

                        \Log::info("Renewal approval email sent result: " . ($emailResult ? 'success' : 'failed'));
                    }
                } else {
                    \Log::warning("No applicant found or no email for scholar_id: {$scholarId}");
                }
            } else {
                \Log::warning("No scholar_id found for renewal_id: {$id}");
            }

            \Log::info("updateRenewalStatus completed successfully for renewal_id: {$id}");
            return response()->json(["success" => true]);

        } catch (\Exception $e) {
            \Log::error("Exception in updateRenewalStatus for renewal_id {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                "success" => false,
                "message" => "An error occurred while updating renewal status",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getRequirements($id)
    {
        $renewal = \DB::table("tbl_renewal")
            ->join(
                "tbl_scholar",
                "tbl_renewal.scholar_id",
                "=",
                "tbl_scholar.scholar_id",
            )
            ->join(
                "tbl_application",
                "tbl_scholar.application_id",
                "=",
                "tbl_application.application_id",
            )
            ->join(
                "tbl_applicant",
                "tbl_application.applicant_id",
                "=",
                "tbl_applicant.applicant_id",
            )
            ->where("tbl_renewal.renewal_id", $id)
            ->select(
                "tbl_applicant.applicant_fname",
                "tbl_applicant.applicant_lname",
                "tbl_applicant.applicant_brgy",
                "tbl_applicant.applicant_school_name",
                "tbl_renewal.renewal_cert_of_reg",
                "tbl_renewal.renewal_grade_slip",
                "tbl_renewal.renewal_brgy_indigency",
                "tbl_renewal.renewal_acad_year",
                "tbl_renewal.renewal_semester",
            )
            ->first();

        return response()->json($renewal);
    }

    public function updateStatus(Request $request, $scholarId)
    {
        $status = $request->input("renewal_status");

        DB::table("tbl_renewal")
            ->where("scholar_id", $scholarId)
            ->latest("renewal_id")
            ->limit(1)
            ->update(["renewal_status" => $status]);

        // ✅ kapag rejected, update scholar status = inactive
        if ($status === "Rejected") {
            DB::table("tbl_scholar")
                ->where("scholar_id", $scholarId)
                ->update(["scholar_status" => "Inactive"]);
        }

        return response()->json(["success" => true]);
    }
public function renewal(Request $request)
{
    $currentAcadYear = DB::table("tbl_applicant")
        ->select("applicant_acad_year")
        ->orderBy("applicant_acad_year", "desc")
        ->value("applicant_acad_year");

    $pendingScreening = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->leftJoin(
            "family_intake_sheets", 
            "tbl_application_personnel.application_personnel_id", 
            "=", 
            "family_intake_sheets.application_personnel_id"
        )
        ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
        ->where("tbl_application_personnel.remarks", "Waiting")
        ->whereNotNull("family_intake_sheets.application_personnel_id")
        ->count();

    $pendingRenewals = DB::table("tbl_renewal")
        ->where("renewal_status", "Pending")
        ->where("renewal_acad_year", $currentAcadYear)
        ->count();

    // Calculate notification badge count
    $unreadNotifications = session('unread_notifications', []);
    $newPendingScreening = $pendingScreening - ($unreadNotifications['pending_screening'] ?? $pendingScreening);
    $newPendingRenewals = $pendingRenewals - ($unreadNotifications['pending_renewals'] ?? $pendingRenewals);
    
    $badgeCount = max(0, $newPendingScreening) + max(0, $newPendingRenewals);

    // Store current counts in session for comparison
    session([
        'current_counts' => [
            'pending_screening' => $pendingScreening,
            'pending_renewals' => $pendingRenewals
        ]
    ]);

    $currentYear = date("Y");

    // ✅ Pending renewals (current year only)
    $tableApplicants = DB::table("tbl_renewal as r")
        ->join("tbl_scholar as s", "r.scholar_id", "=", "s.scholar_id")
        ->join(
            "tbl_application as ap",
            "s.application_id",
            "=",
            "ap.application_id",
        )
        ->join(
            "tbl_applicant as a",
            "ap.applicant_id",
            "=",
            "a.applicant_id",
        )
        ->select(
            "r.renewal_id",
            "a.applicant_id",
            "a.applicant_fname",
            "a.applicant_lname",
            "a.applicant_mname",
             "a.applicant_suffix",
            "a.applicant_brgy",
            "a.applicant_email",
            "a.applicant_school_name",
            "a.applicant_year_level",
            "r.renewal_status",
            "r.renewal_cert_of_reg",
            "r.renewal_grade_slip",
            "r.renewal_brgy_indigency",
            "r.renewal_acad_year",
            "r.renewal_semester",
            "ap.application_id",
            "s.scholar_id",
            "r.created_at" // Added for ordering
        )
        ->where("r.renewal_status", "Pending")
        ->where("r.renewal_acad_year", $currentAcadYear)
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where(
                    "a.applicant_fname",
                    "like",
                    "%$search%",
                )->orWhere("a.applicant_lname", "like", "%$search%");
            });
        })
        ->when($request->barangay, function ($query, $barangay) {
            $query->where("a.applicant_brgy", $barangay);
        })
        ->orderBy("r.created_at", "desc") // NEWLY ADDED - show newest first
        ->paginate();

    $renewals = DB::table("tbl_renewal")
        ->select(
            "renewal_id",
            "scholar_id",
            "renewal_cert_of_reg",
            "renewal_grade_slip",
            "renewal_brgy_indigency",
            "renewal_semester",
            "renewal_acad_year",
            "date_submitted",
            "renewal_status",
        )
        ->orderBy("created_at", "desc") // NEWLY ADDED - show newest first
        ->get()
        ->groupBy("scholar_id");

    // para dropdown filter ng barangay
    $barangays = DB::table("tbl_applicant")
        ->select("applicant_brgy")
        ->distinct()
        ->pluck("applicant_brgy");

    $notifications = DB::table("tbl_application_personnel")
        ->join(
            "tbl_application",
            "tbl_application_personnel.application_id",
            "=",
            "tbl_application.application_id",
        )
        ->join(
            "tbl_applicant",
            "tbl_application.applicant_id",
            "=",
            "tbl_applicant.applicant_id",
        )
        ->select(
            "tbl_application_personnel.*",
            DB::raw("CONCAT(
        tbl_applicant.applicant_fname, ' ',
        COALESCE(tbl_applicant.applicant_mname, ''), ' ',
        tbl_applicant.applicant_lname,
        IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')
    ) as name"),
        )
        ->where(function ($q) {
            $q->where(
                "tbl_application_personnel.initial_screening",
                "Approved",
            )->orWhere("tbl_application_personnel.status", "Renewed");
        })
        ->orderBy("tbl_application_personnel.created_at", "desc")
        ->limit(5)
        ->get();

    $listView = DB::table("tbl_renewal as r")
        ->join("tbl_scholar as s", "r.scholar_id", "=", "s.scholar_id")
        ->join("tbl_application as ap", "s.application_id", "=", "ap.application_id")
        ->join("tbl_applicant as a", "ap.applicant_id", "=", "a.applicant_id")
        ->select(
            "s.scholar_id",
            "r.renewal_id",
            "a.applicant_id",
            "a.applicant_fname",
            "a.applicant_mname",
            "a.applicant_lname",
            "a.applicant_suffix",
            "a.applicant_brgy",
            "a.applicant_year_level",
            "a.applicant_school_name",
            "a.applicant_course",
            "r.rejection_reason",
            "r.renewal_status",
            "r.renewal_semester",
            "r.renewal_acad_year",
            "ap.application_id",
            "r.created_at" // Added for ordering
        )
        ->whereIn("r.renewal_status", ["Approved", "Rejected"])
        ->where("r.renewal_acad_year", $currentAcadYear)
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where("a.applicant_fname", "like", "%$search%")
                  ->orWhere("a.applicant_lname", "like", "%$search%");
            });
        })
        ->when($request->barangay, function ($query, $barangay) {
            $query->where("a.applicant_brgy", $barangay);
        })
        ->orderBy("r.created_at", "desc") // NEWLY ADDED - show newest first
        ->paginate()
        ->appends($request->all());

    return view(
        "lydo_staff.renewal",
        compact(
            "pendingScreening",
            "pendingRenewals",
            "currentAcadYear",
            "tableApplicants",
            "barangays",
            "renewals",
            "notifications",
            "listView",
            "badgeCount"
        ),
    );
}
    public function getApplicantDetails($applicant_id)
    {
        $applicant = DB::table('tbl_applicant')
            ->where('applicant_id', $applicant_id)
            ->first();

        if ($applicant) {
            return response()->json($applicant);
        } else {
            return response()->json(['error' => 'Applicant not found'], 404);
        }
    }

    public function saveRenewalDocumentStatus(Request $request)
    {
        try {
            $request->validate([
                'renewal_id' => 'required|exists:tbl_renewal,renewal_id',
                'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency',
                'status' => 'required|in:good,bad'
            ]);

            $updateData = [
                $request->document_type . '_status' => $request->status,
                'updated_at' => now()
            ];

            DB::table('tbl_renewal')
                ->where('renewal_id', $request->renewal_id)
                ->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Save renewal document status error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveRenewalDocumentComment(Request $request)
    {
        try {
            $request->validate([
                'renewal_id' => 'required|exists:tbl_renewal,renewal_id',
                'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency',
                'comment' => 'nullable|string|max:1000'
            ]);

            $updateData = [
                $request->document_type . '_comment' => $request->comment,
                'updated_at' => now()
            ];

            DB::table('tbl_renewal')
                ->where('renewal_id', $request->renewal_id)
                ->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Save renewal document comment error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getRenewalDocumentStatuses($renewalId)
    {
        try {
            $renewal = DB::table('tbl_renewal')
                ->where('renewal_id', $renewalId)
                ->first();

            if (!$renewal) {
                return response()->json(['success' => false, 'message' => 'Renewal not found'], 404);
            }

            $statuses = [
                'cert_of_reg_status' => $renewal->cert_of_reg_status,
                'grade_slip_status' => $renewal->grade_slip_status,
                'brgy_indigency_status' => $renewal->brgy_indigency_status,
                'cert_of_reg_comment' => $renewal->cert_of_reg_comment,
                'grade_slip_comment' => $renewal->grade_slip_comment,
                'brgy_indigency_comment' => $renewal->brgy_indigency_comment,
            ];

            return response()->json([
                'success' => true,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            \Log::error('Get renewal document statuses error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDocumentComments($renewalId)
    {
        try {
            $renewal = DB::table('tbl_renewal')
                ->where('renewal_id', $renewalId)
                ->select(
                    'cert_of_reg_comment',
                    'grade_slip_comment',
                    'brgy_indigency_comment',
                    'cert_of_reg_status',
                    'grade_slip_status',
                    'brgy_indigency_status'
                )
                ->first();

            if (!$renewal) {
                return response()->json(['success' => false, 'message' => 'Renewal not found'], 404);
            }

            return response()->json([
                'success' => true,
                'comments' => [
                    'cert_of_reg' => $renewal->cert_of_reg_comment,
                    'grade_slip' => $renewal->grade_slip_comment,
                    'brgy_indigency' => $renewal->brgy_indigency_comment,
                ],
                'statuses' => [
                    'cert_of_reg' => $renewal->cert_of_reg_status,
                    'grade_slip' => $renewal->grade_slip_status,
                    'brgy_indigency' => $renewal->brgy_indigency_status,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Get document comments error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function requestDocumentUpdate(Request $request, $renewalId)
    {
        try {
            $request->validate([
                'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency',
                'comment' => 'required|string|max:1000'
            ]);

            // Update the comment
            $updateData = [
                $request->document_type . '_comment' => $request->comment,
                $request->document_type . '_status' => 'bad', // Mark as bad
                'renewal_status' => 'Pending', // Reset to pending for re-evaluation
                'updated_at' => now()
            ];

            DB::table('tbl_renewal')
                ->where('renewal_id', $renewalId)
                ->update($updateData);

            // Get scholar and applicant info for email
            $scholarInfo = DB::table('tbl_renewal as r')
                ->join('tbl_scholar as s', 'r.scholar_id', '=', 's.scholar_id')
                ->join('tbl_application as ap', 's.application_id', '=', 'ap.application_id')
                ->join('tbl_applicant as a', 'ap.applicant_id', '=', 'a.applicant_id')
                ->where('r.renewal_id', $renewalId)
                ->select('a.applicant_email', 'a.applicant_fname', 'a.applicant_lname')
                ->first();

            if ($scholarInfo) {
                $emailService = new EmailService();
                $emailService->sendDocumentUpdateRequest(
                    $scholarInfo->applicant_email,
                    [
                        'applicant_fname' => $scholarInfo->applicant_fname,
                        'applicant_lname' => $scholarInfo->applicant_lname,
                        'document_type' => $this->getDocumentTypeName($request->document_type),
                        'comment' => $request->comment
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Document update request sent successfully']);
        } catch (\Exception $e) {
            \Log::error('Request document update error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

// Sa RenewalController.php, add this method:
public function sendDocumentApprovedNotification(Request $request)
{
    try {
        $request->validate([
            'renewal_id' => 'required|exists:tbl_renewal,renewal_id',
            'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency'
        ]);

        // Get renewal and scholar info
        $renewalInfo = DB::table('tbl_renewal as r')
            ->join('tbl_scholar as s', 'r.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as ap', 's.application_id', '=', 'ap.application_id')
            ->join('tbl_applicant as a', 'ap.applicant_id', '=', 'a.applicant_id')
            ->where('r.renewal_id', $request->renewal_id)
            ->select(
                'a.applicant_email',
                'a.applicant_fname',
                'a.applicant_lname',
                'r.renewal_acad_year',
                'r.renewal_semester'
            )
            ->first();

        if (!$renewalInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Scholar information not found'
            ], 404);
        }

        // Get document type name
        $documentTypeName = $this->getDocumentTypeName($request->document_type);

        // Send approval email
        $emailService = new EmailService();
        $emailResult = $emailService->sendDocumentApprovedEmail(
            $renewalInfo->applicant_email,
            [
                'applicant_fname' => $renewalInfo->applicant_fname,
                'applicant_lname' => $renewalInfo->applicant_lname,
                'document_type' => $documentTypeName,
                'academic_year' => $renewalInfo->renewal_acad_year,
                'semester' => $renewalInfo->renewal_semester
            ]
        );

        if ($emailResult) {
            \Log::info("Document approval email sent to {$renewalInfo->applicant_email} for {$documentTypeName}");
            
            return response()->json([
                'success' => true,
                'message' => 'Approval notification sent successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send approval notification'
            ], 500);
        }

    } catch (\Exception $e) {
        \Log::error('Send document approved notification error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send approval notification: ' . $e->getMessage()
        ], 500);
    }
}

// Also update the markDocumentAsUpdated method to send email:
public function markDocumentAsUpdated(Request $request, $renewalId)
{
    try {
        $request->validate([
            'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency'
        ]);

        // Reset status to null and clear comment to indicate new document
        $updateData = [
            $request->document_type . '_status' => 'new', // Set to 'new' instead of null
            $request->document_type . '_comment' => null,
            'updated_at' => now()
        ];

        $result = DB::table('tbl_renewal')
            ->where('renewal_id', $renewalId)
            ->update($updateData);

        // Get scholar info for potential email
        $scholarInfo = DB::table('tbl_renewal as r')
            ->join('tbl_scholar as s', 'r.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as ap', 's.application_id', '=', 'ap.application_id')
            ->join('tbl_applicant as a', 'ap.applicant_id', '=', 'a.applicant_id')
            ->where('r.renewal_id', $renewalId)
            ->select('a.applicant_email', 'a.applicant_fname', 'a.applicant_lname')
            ->first();

        if ($scholarInfo) {
            \Log::info("Document marked as updated for scholar: {$scholarInfo->applicant_email}");
        }

        return response()->json(['success' => true, 'message' => 'Document marked as updated']);

    } catch (\Exception $e) {
        \Log::error('Mark document as updated error: '.$e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    private function getDocumentTypeName($documentType)
    {
        $names = [
            'cert_of_reg' => 'Certificate of Registration',
            'grade_slip' => 'Grade Slip',
            'brgy_indigency' => 'Barangay Indigency'
        ];

        return $names[$documentType] ?? 'Document';
    }

    public function sendEmailForBadDocuments(Request $request)
    {
        try {
            // Get current academic year
            $currentAcadYear = DB::table("tbl_applicant")
                ->select("applicant_acad_year")
                ->orderBy("applicant_acad_year", "desc")
                ->value("applicant_acad_year");

            // Find all renewals with bad documents for current academic year
            $badDocuments = DB::table('tbl_renewal as r')
                ->join('tbl_scholar as s', 'r.scholar_id', '=', 's.scholar_id')
                ->join('tbl_application as ap', 's.application_id', '=', 'ap.application_id')
                ->join('tbl_applicant as a', 'ap.applicant_id', '=', 'a.applicant_id')
                ->where('r.renewal_acad_year', $currentAcadYear)
                ->where(function ($query) {
                    $query->where('r.cert_of_reg_status', 'bad')
                          ->orWhere('r.grade_slip_status', 'bad')
                          ->orWhere('r.brgy_indigency_status', 'bad');
                })
                ->select(
                    'r.renewal_id',
                    'a.applicant_email',
                    'a.applicant_fname',
                    'a.applicant_lname',
                    'r.cert_of_reg_status',
                    'r.grade_slip_status',
                    'r.brgy_indigency_status',
                    'r.cert_of_reg_comment',
                    'r.grade_slip_comment',
                    'r.brgy_indigency_comment'
                )
                ->get();

            $emailService = new EmailService();
            $sentCount = 0;
            $errors = [];

            foreach ($badDocuments as $renewal) {
                try {
                    // Determine which documents are bad and collect their info
                    $badDocs = [];
                    if ($renewal->cert_of_reg_status === 'bad') {
                        $badDocs[] = [
                            'type' => 'Certificate of Registration',
                            'comment' => $renewal->cert_of_reg_comment
                        ];
                    }
                    if ($renewal->grade_slip_status === 'bad') {
                        $badDocs[] = [
                            'type' => 'Grade Slip',
                            'comment' => $renewal->grade_slip_comment
                        ];
                    }
                    if ($renewal->brgy_indigency_status === 'bad') {
                        $badDocs[] = [
                            'type' => 'Barangay Indigency',
                            'comment' => $renewal->brgy_indigency_comment
                        ];
                    }

                    // Send email for each bad document
                    foreach ($badDocs as $doc) {
                        $emailService->sendDocumentUpdateRequest(
                            $renewal->applicant_email,
                            [
                                'applicant_fname' => $renewal->applicant_fname,
                                'applicant_lname' => $renewal->applicant_lname,
                                'document_type' => $doc['type'],
                                'comment' => $doc['comment'] ?? 'Document requires update'
                            ]
                        );
                    }

                    $sentCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to send email to {$renewal->applicant_email}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Emails sent successfully to {$sentCount} scholars",
                'sent_count' => $sentCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \Log::error('Send email for bad documents error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send emails: ' . $e->getMessage()
            ], 500);
        }
    }
public function checkNewDocuments()
{
    try {
        // Get current academic year
        $currentAcadYear = DB::table("tbl_applicant")
            ->select("applicant_acad_year")
            ->orderBy("applicant_acad_year", "desc")
            ->value("applicant_acad_year");

        // Find renewals with new documents
        $renewalsWithNewDocs = DB::table('tbl_renewal as r')
            ->join('tbl_scholar as s', 'r.scholar_id', '=', 's.scholar_id')
            ->join('tbl_application as ap', 's.application_id', '=', 'ap.application_id')
            ->join('tbl_applicant as a', 'ap.applicant_id', '=', 'a.applicant_id')
            ->where('r.renewal_acad_year', $currentAcadYear)
            ->where('r.renewal_status', 'Pending')
            ->where(function ($query) {
                $query->where('r.cert_of_reg_status', 'new')
                      ->orWhere('r.grade_slip_status', 'new')
                      ->orWhere('r.brgy_indigency_status', 'new');
            })
            ->select(
                'r.renewal_id',
                's.scholar_id',
                'a.applicant_fname',
                'a.applicant_lname',
                'r.cert_of_reg_status',
                'r.grade_slip_status',
                'r.brgy_indigency_status',
                DB::raw("CONCAT(
                    a.applicant_fname, ' ',
                    COALESCE(a.applicant_mname, ''), ' ',
                    a.applicant_lname,
                    IFNULL(CONCAT(' ', a.applicant_suffix), '')
                ) as full_name")
            )
            ->get()
            ->map(function ($item) {
                // Count how many documents are new
                $newCount = 0;
                if ($item->cert_of_reg_status === 'new') $newCount++;
                if ($item->grade_slip_status === 'new') $newCount++;
                if ($item->brgy_indigency_status === 'new') $newCount++;
                
                $item->new_document_count = $newCount;
                return $item;
            });

        return response()->json([
            'success' => true,
            'renewals_with_new_docs' => $renewalsWithNewDocs,
            'count' => $renewalsWithNewDocs->count()
        ]);
    } catch (\Exception $e) {
        \Log::error('Check new documents error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
// App\Http\Controllers\RenewalController.php
public function getDocumentsStatus($scholarId)
{
    // Kunin ang renewal documents para sa scholar na ito
    $documents = RenewalDocument::where('scholar_id', $scholarId)
        ->whereIn('document_type', ['grades', 'registration', 'good_moral'])
        ->select('id', 'document_type', 'document_status', 'updated_at')
        ->get();
    
    return response()->json([
        'success' => true,
        'documents' => $documents
    ]);
}
}