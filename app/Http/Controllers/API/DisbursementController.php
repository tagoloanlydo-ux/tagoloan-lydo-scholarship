<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Disburse;
use App\Models\Scholar;

class DisbursementController extends Controller
{
    /**
     * Get disbursements list
     */
    public function index(Request $request)
    {
        $query = Disburse::with(['scholar.applicant']);

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $query->where('disbursement_status', $request->status);
        }

        if ($request->has('academic_year') && !empty($request->academic_year)) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('scholar_id') && !empty($request->scholar_id)) {
            $query->where('scholar_id', $request->scholar_id);
        }

        $disbursements = $query->orderBy('disbursement_date', 'desc')->paginate(15);

        return $this->paginatedResponse($disbursements, 'Disbursements retrieved successfully');
    }

    /**
     * Get single disbursement
     */
    public function show(Request $request, $disbursementId)
    {
        $disbursement = Disburse::with(['scholar.applicant'])->find($disbursementId);

        if (!$disbursement) {
            return $this->errorResponse('Disbursement not found', 404);
        }

        return $this->successResponse($disbursement, 'Disbursement retrieved successfully');
    }

    /**
     * Create disbursement
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'scholar_id' => 'required|exists:tbl_scholar,scholar_id',
            'disbursement_amount' => 'required|numeric|min:0',
            'disbursement_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $disbursement = Disburse::create([
                'scholar_id' => $request->scholar_id,
                'disbursement_amount' => $request->disbursement_amount,
                'disbursement_date' => $request->disbursement_date,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'disbursement_status' => 'Pending',
            ]);

            return $this->successResponse($disbursement->load('scholar.applicant'), 'Disbursement created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create disbursement: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update disbursement
     */
    public function update(Request $request, $disbursementId)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $disbursement = Disburse::find($disbursementId);
        if (!$disbursement) {
            return $this->errorResponse('Disbursement not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'disbursement_amount' => 'sometimes|numeric|min:0',
            'disbursement_date' => 'sometimes|date',
            'academic_year' => 'sometimes|string|max:20',
            'semester' => 'sometimes|string|max:20',
            'disbursement_status' => 'sometimes|in:Pending,Approved,Disbursed,Cancelled',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $disbursement->update($request->only([
            'disbursement_amount', 'disbursement_date', 'academic_year',
            'semester', 'disbursement_status'
        ]));

        return $this->successResponse($disbursement->load('scholar.applicant'), 'Disbursement updated successfully');
    }

    /**
     * Delete disbursement
     */
    public function destroy(Request $request, $disbursementId)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $disbursement = Disburse::find($disbursementId);
        if (!$disbursement) {
            return $this->errorResponse('Disbursement not found', 404);
        }

        $disbursement->delete();

        return $this->successResponse(null, 'Disbursement deleted successfully');
    }

    /**
     * Generate disbursement voucher PDF
     */
    public function generateVoucher(Request $request, $disbursementId)
    {
        try {
            $disbursement = Disburse::with(['scholar.applicant'])->find($disbursementId);

            if (!$disbursement) {
                return $this->errorResponse('Disbursement not found', 404);
            }

            $pdf = Pdf::loadView('disbursements.voucher', [
                'disbursement' => $disbursement,
                'generated_at' => now(),
            ]);

            return $pdf->download('disbursement_voucher_' . $disbursementId . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate voucher: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate disbursement report PDF
     */
    public function generateReport(Request $request)
    {
        try {
            $query = Disburse::with(['scholar.applicant']);

            // Apply filters
            if ($request->has('status') && !empty($request->status)) {
                $query->where('disbursement_status', $request->status);
            }

            if ($request->has('academic_year') && !empty($request->academic_year)) {
                $query->where('academic_year', $request->academic_year);
            }

            if ($request->has('month') && !empty($request->month)) {
                $query->whereMonth('disbursement_date', $request->month);
            }

            if ($request->has('year') && !empty($request->year)) {
                $query->whereYear('disbursement_date', $request->year);
            }

            $disbursements = $query->orderBy('disbursement_date', 'desc')->get();

            $pdf = Pdf::loadView('disbursements.report', [
                'disbursements' => $disbursements,
                'filters' => $request->all(),
                'total_amount' => $disbursements->sum('disbursement_amount'),
                'generated_at' => now(),
            ]);

            return $pdf->download('disbursement_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get pending disbursements count
     */
    public function pendingCount(Request $request)
    {
        $count = Disburse::where('disbursement_status', 'Pending')->count();

        return $this->successResponse(['count' => $count], 'Pending disbursements count retrieved successfully');
    }

    /**
     * Bulk update disbursement status
     */
    public function bulkUpdateStatus(Request $request)
    {
        if (!$this->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'disbursement_ids' => 'required|array',
            'disbursement_ids.*' => 'integer|exists:tbl_disburse,disbursement_id',
            'status' => 'required|in:Pending,Approved,Disbursed,Cancelled',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            Disburse::whereIn('disbursement_id', $request->disbursement_ids)
                ->update(['disbursement_status' => $request->status]);

            return $this->successResponse([
                'updated_count' => count($request->disbursement_ids)
            ], 'Disbursements updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update disbursements: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get disbursements by scholar
     */
    public function scholarDisbursements(Request $request, $scholarId)
    {
        $scholar = Scholar::find($scholarId);
        if (!$scholar) {
            return $this->errorResponse('Scholar not found', 404);
        }

        $disbursements = Disburse::where('scholar_id', $scholarId)
            ->orderBy('disbursement_date', 'desc')
            ->paginate(10);

        return $this->paginatedResponse($disbursements, 'Scholar disbursements retrieved successfully');
    }

    /**
     * Get disbursement statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [];

            // Total disbursements
            $stats['total_disbursements'] = Disburse::count();
            $stats['total_amount'] = Disburse::sum('disbursement_amount');

            // Status breakdown
            $statusStats = Disburse::select('disbursement_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(disbursement_amount) as amount'))
                ->groupBy('disbursement_status')
                ->get()
                ->keyBy('disbursement_status');

            $stats['status_breakdown'] = [
                'pending' => [
                    'count' => $statusStats['Pending']->count ?? 0,
                    'amount' => $statusStats['Pending']->amount ?? 0,
                ],
                'approved' => [
                    'count' => $statusStats['Approved']->count ?? 0,
                    'amount' => $statusStats['Approved']->amount ?? 0,
                ],
                'disbursed' => [
                    'count' => $statusStats['Disbursed']->count ?? 0,
                    'amount' => $statusStats['Disbursed']->amount ?? 0,
                ],
                'cancelled' => [
                    'count' => $statusStats['Cancelled']->count ?? 0,
                    'amount' => $statusStats['Cancelled']->amount ?? 0,
                ],
            ];

            // Monthly disbursements (last 12 months)
            $stats['monthly_disbursements'] = Disburse::select(
                    DB::raw('YEAR(disbursement_date) as year'),
                    DB::raw('MONTH(disbursement_date) as month'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(disbursement_amount) as amount')
                )
                ->where('disbursement_date', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return $this->successResponse($stats, 'Disbursement statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate statistics: ' . $e->getMessage(), 500);
        }
    }
}
