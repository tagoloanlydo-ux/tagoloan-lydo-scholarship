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

        DB::table("tbl_renewal")
            ->where("renewal_id", $id)
            ->update($updateData);

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
            ->value("scholar_id"); // kuha scholar id

        if ($scholarId) {
            $applicant = DB::table("tbl_scholar")
                ->join("tbl_application", "tbl_scholar.application_id", "=", "tbl_application.application_id")
                ->join("tbl_applicant", "tbl_application.applicant_id", "=", "tbl_applicant.applicant_id")
                ->where("tbl_scholar.scholar_id", $scholarId)
                ->select("tbl_applicant.applicant_email", "tbl_applicant.applicant_fname", "tbl_applicant.applicant_lname")
                ->first();

            if ($applicant) {
                $emailService = new EmailService();
                if ($request->renewal_status === "Rejected") {
                    DB::table("tbl_scholar")
                        ->where("scholar_id", $scholarId)
                        ->update([
                            "scholar_status" => "Inactive",
                            "updated_at" => now(),
                        ]);

                    $emailService->sendRejectionEmail($applicant->applicant_email, [
                        'applicant_fname' => $applicant->applicant_fname,
                        'applicant_lname' => $applicant->applicant_lname,
                        'reason' => $request->reason,
                    ]);
                } elseif ($request->renewal_status === "Approved") {
                    $emailService->sendApprovalEmail($applicant->applicant_email, [
                        'applicant_fname' => $applicant->applicant_fname,
                        'applicant_lname' => $applicant->applicant_lname,
                    ]);
                }
            }
        }

        return response()->json(["success" => true]);
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
// Add to RenewalController.php

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
}
