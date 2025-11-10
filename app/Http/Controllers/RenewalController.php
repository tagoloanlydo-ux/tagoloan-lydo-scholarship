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


class RenewalController extends Controller
{
    
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
            ->count();

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
                "a.applicant_brgy",
                "a.applicant_email",
                "a.applicant_school_name",
                "r.renewal_status",
                "r.renewal_cert_of_reg",
                "r.renewal_grade_slip",
                "r.renewal_brgy_indigency",
                "r.renewal_acad_year",
                "ap.application_id",
                "s.scholar_id",
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
        "a.applicant_school_name",
        "a.applicant_course",
        "r.renewal_status",
        "r.renewal_acad_year",
        "ap.application_id"
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
    ->paginate() // ✅ use paginate instead of get
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

        // Map document types to actual column names (using correct column names)
        $statusColumnMapping = [
            'cert_of_reg' => 'cert_of_reg_status',
            'grade_slip' => 'grade_slip_status', 
            'brgy_indigency' => 'brgy_indigency_status'
        ];

        $statusColumnName = $statusColumnMapping[$request->document_type] ?? null;
        
        if (!$statusColumnName) {
            return response()->json(['success' => false, 'message' => 'Invalid document type'], 400);
        }

        $updateData = [
            $statusColumnName => $request->status,
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
        \Log::info('=== START saveRenewalDocumentComment ===');
        \Log::info('Save document comment request:', $request->all());
        
        $request->validate([
            'renewal_id' => 'required|exists:tbl_renewal,renewal_id',
            'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency',
            'comment' => 'nullable|string|max:1000'
        ]);

        \Log::info('Validation passed');

        // Map document types to actual column names
        $columnMapping = [
            'cert_of_reg' => 'cert_of_reg_comment',
            'grade_slip' => 'grade_slip_comment', 
            'brgy_indigency' => 'brgy_indigency_comment'
        ];

        $columnName = $columnMapping[$request->document_type] ?? null;
        
        if (!$columnName) {
            \Log::error('Invalid document type provided: ' . $request->document_type);
            return response()->json(['success' => false, 'message' => 'Invalid document type'], 400);
        }

        \Log::info("Using column name: {$columnName} for document type: {$request->document_type}");

        $updateData = [
            $columnName => $request->comment,
            'updated_at' => now()
        ];

        \Log::info('Attempting to update with data:', $updateData);
        \Log::info('Renewal ID: ' . $request->renewal_id);

        // First, check if the renewal exists and get current data
        $currentData = DB::table('tbl_renewal')
            ->where('renewal_id', $request->renewal_id)
            ->first();
            
        if (!$currentData) {
            \Log::error('Renewal not found with ID: ' . $request->renewal_id);
            return response()->json(['success' => false, 'message' => 'Renewal not found'], 404);
        }

        \Log::info('Current renewal data:', (array)$currentData);

        $result = DB::table('tbl_renewal')
            ->where('renewal_id', $request->renewal_id)
            ->update($updateData);

        \Log::info('Update result - affected rows:', ['affected_rows' => $result]);

        // Verify the update
        $updatedData = DB::table('tbl_renewal')
            ->where('renewal_id', $request->renewal_id)
            ->first();
            
        \Log::info('After update - new comment value:', [
            'column' => $columnName,
            'value' => $updatedData->$columnName
        ]);

        return response()->json([
            'success' => true, 
            'affected_rows' => $result,
            'new_value' => $updatedData->$columnName
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Save renewal document comment error: '.$e->getMessage());
        \Log::error('Stack trace: '.$e->getTraceAsString());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function getDocumentComments($renewalId)
{
    try {
        $renewal = DB::table('tbl_renewal')
            ->where('renewal_id', $renewalId)
            ->select(
                'cert_of_reg_comment', // Changed from renewal_cert_of_reg_comment
                'grade_slip_comment',  // Changed from renewal_grade_slip_comment
                'brgy_indigency_comment', // Changed from renewal_brgy_indigency_comment
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

public function getRenewalDocumentStatuses($renewalId)
{
    try {
        $renewal = DB::table('tbl_renewal')
            ->where('renewal_id', $renewalId)
            ->select(
                'cert_of_reg_status',
                'grade_slip_status',
                'brgy_indigency_status',
                'cert_of_reg_comment',
                'grade_slip_comment',
                'brgy_indigency_comment'
            )
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

public function markDocumentAsUpdated(Request $request, $renewalId)
{
    try {
        $request->validate([
            'document_type' => 'required|in:cert_of_reg,grade_slip,brgy_indigency'
        ]);

        // Reset status to null and clear comment to indicate new document
        $updateData = [
            $request->document_type . '_status' => null,
            $request->document_type . '_comment' => null,
            'updated_at' => now()
        ];

        DB::table('tbl_renewal')
            ->where('renewal_id', $renewalId)
            ->update($updateData);

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
        $renewalId = $request->input('renewal_id');
        $badDocuments = $request->input('bad_documents', []);
        
        // Get renewal data with scholar and applicant information
        $renewal = Renewal::with(['scholar.applicant'])
            ->where('renewal_id', $renewalId)
            ->first();
        
        if (!$renewal) {
            return response()->json([
                'success' => false,
                'message' => 'Renewal record not found'
            ], 404);
        }
        
        $scholar = $renewal->scholar;
        $applicant = $scholar->applicant;
        
        // Get document statuses with reasons
        $documentStatuses = RenewalDocumentStatus::where('renewal_id', $renewalId)
            ->whereIn('document_type', $badDocuments)
            ->get()
            ->keyBy('document_type');
        
        // Map document types to human-readable names
        $documentNames = [
            'cert_of_reg' => 'Certificate of Registration',
            'grade_slip' => 'Grade Slip/Transcript of Records',
            'brgy_indigency' => 'Barangay Certificate of Indigency'
        ];
        
        // Prepare bad documents data with reasons
        $badDocumentsData = [];
        foreach ($badDocuments as $docType) {
            $status = $documentStatuses->get($docType);
            $reason = $status ? $status->reason : 'Document does not meet the required standards. Please ensure the document is clear, complete, and up-to-date.';
            
            $badDocumentsData[] = [
                'type' => $docType,
                'name' => $documentNames[$docType] ?? ucfirst(str_replace('_', ' ', $docType)),
                'reason' => $reason
            ];
        }
        
        // Prepare email data
        $emailData = [
            'applicant_fname' => $applicant->applicant_fname,
            'applicant_lname' => $applicant->applicant_lname,
            'bad_documents' => $badDocumentsData
        ];
        
        // Send email using the NEW blade file name
        Mail::send('emails.document-correction-required', $emailData, function($message) use ($applicant) {
            $message->to($applicant->applicant_email)
                   ->subject('LYDO Scholarship - Document Correction Required')
                   ->from('scholarship@lydo.gov.ph', 'LYDO Scholarship Team');
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Correction request email sent successfully to scholar'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error sending document correction email: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send correction notification email'
        ], 500);
    }
}
}
