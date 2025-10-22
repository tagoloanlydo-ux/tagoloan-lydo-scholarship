<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Applicant;
use App\Models\Scholar;
use App\Models\Application;
use App\Models\Disburse;
use App\Models\Renewal;

class ReportController extends Controller
{
    /**
     * Get report statistics
     */
    public function statistics(Request $request)
    {
        try {
            $stats = [];

            // Basic counts
            $stats['total_applicants'] = Applicant::count();
            $stats['total_scholars'] = Scholar::where('scholar_status', 'Active')->count();
            $stats['total_applications'] = Application::count();

            // Status breakdowns
            $applicationStatuses = DB::table('tbl_application_personnel')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $stats['application_statuses'] = [
                'pending' => $applicationStatuses['Pending']->count ?? 0,
                'approved' => $applicationStatuses['Approved']->count ?? 0,
                'rejected' => $applicationStatuses['Rejected']->count ?? 0,
            ];

            // Disbursement stats
            $stats['total_disbursements'] = Disburse::count();
            $stats['pending_disbursements'] = Disburse::where('disbursement_status', 'Pending')->count();
            $stats['completed_disbursements'] = Disburse::where('disbursement_status', 'Disbursed')->count();

            // Renewal stats
            $stats['total_renewals'] = Renewal::count();
            $stats['pending_renewals'] = Renewal::where('renewal_status', 'Pending')->count();
            $stats['approved_renewals'] = Renewal::where('renewal_status', 'Approved')->count();

            // Distribution by barangay
            $stats['barangay_distribution'] = Applicant::select('applicant_brgy', DB::raw('COUNT(*) as count'))
                ->groupBy('applicant_brgy')
                ->orderBy('count', 'desc')
                ->get();

            // Distribution by school
            $stats['school_distribution'] = Applicant::select('applicant_school_name', DB::raw('COUNT(*) as count'))
                ->groupBy('applicant_school_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Monthly applications (last 12 months)
            $stats['monthly_applications'] = Application::select(
                    DB::raw('YEAR(date_submitted) as year'),
                    DB::raw('MONTH(date_submitted) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('date_submitted', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return $this->successResponse($stats, 'Statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate applicants report PDF
     */
    public function applicantsReport(Request $request)
    {
        try {
            $query = Applicant::with(['application.applicationPersonnel']);

            // Apply filters
            if ($request->has('barangay') && !empty($request->barangay)) {
                $query->where('applicant_brgy', $request->barangay);
            }

            if ($request->has('academic_year') && !empty($request->academic_year)) {
                $query->where('applicant_acad_year', $request->academic_year);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->whereHas('application.applicationPersonnel', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $applicants = $query->get();

            $pdf = Pdf::loadView('reports.applicants', [
                'applicants' => $applicants,
                'filters' => $request->all(),
                'generated_at' => now(),
            ]);

            return $pdf->download('applicants_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate applicants report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate scholars report PDF
     */
    public function scholarsReport(Request $request)
    {
        try {
            $query = Scholar::with('applicant');

            if ($request->has('status') && !empty($request->status)) {
                $query->where('scholar_status', $request->status);
            }

            $scholars = $query->get();

            $pdf = Pdf::loadView('reports.scholars', [
                'scholars' => $scholars,
                'filters' => $request->all(),
                'generated_at' => now(),
            ]);

            return $pdf->download('scholars_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate scholars report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate disbursements report PDF
     */
    public function disbursementsReport(Request $request)
    {
        try {
            $query = Disburse::with(['scholar.applicant']);

            if ($request->has('status') && !empty($request->status)) {
                $query->where('disbursement_status', $request->status);
            }

            if ($request->has('academic_year') && !empty($request->academic_year)) {
                $query->where('academic_year', $request->academic_year);
            }

            $disbursements = $query->get();

            $pdf = Pdf::loadView('reports.disbursements', [
                'disbursements' => $disbursements,
                'filters' => $request->all(),
                'generated_at' => now(),
            ]);

            return $pdf->download('disbursements_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate disbursements report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate renewals report PDF
     */
    public function renewalsReport(Request $request)
    {
        try {
            $query = Renewal::with(['scholar.applicant']);

            if ($request->has('status') && !empty($request->status)) {
                $query->where('renewal_status', $request->status);
            }

            if ($request->has('academic_year') && !empty($request->academic_year)) {
                $query->where('renewal_acad_year', $request->academic_year);
            }

            $renewals = $query->get();

            $pdf = Pdf::loadView('reports.renewals', [
                'renewals' => $renewals,
                'filters' => $request->all(),
                'generated_at' => now(),
            ]);

            return $pdf->download('renewals_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate renewals report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate comprehensive summary report PDF
     */
    public function summaryReport(Request $request)
    {
        try {
            // Gather all statistics
            $stats = $this->statistics($request)->getData()->data;

            $pdf = Pdf::loadView('reports.summary', [
                'stats' => $stats,
                'generated_at' => now(),
            ]);

            return $pdf->download('summary_report_' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate summary report: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export applicants data as CSV
     */
    public function exportApplicants(Request $request)
    {
        try {
            $query = Applicant::with(['application.applicationPersonnel']);

            // Apply filters same as applicants report
            if ($request->has('barangay') && !empty($request->barangay)) {
                $query->where('applicant_brgy', $request->barangay);
            }

            if ($request->has('academic_year') && !empty($request->academic_year)) {
                $query->where('applicant_acad_year', $request->academic_year);
            }

            if ($request->has('status') && !empty($request->status)) {
                $query->whereHas('application.applicationPersonnel', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $applicants = $query->get();

            $filename = 'applicants_export_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($applicants) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID', 'First Name', 'Last Name', 'Email', 'Contact', 'Barangay',
                    'School', 'Course', 'Year Level', 'Academic Year', 'GWA',
                    'Income', 'Parent Income', 'Siblings', 'Status', 'Date Submitted'
                ]);

                // CSV data
                foreach ($applicants as $applicant) {
                    fputcsv($file, [
                        $applicant->applicant_id,
                        $applicant->applicant_fname,
                        $applicant->applicant_mname ? $applicant->applicant_fname . ' ' . $applicant->applicant_mname : $applicant->applicant_fname,
                        $applicant->applicant_lname,
                        $applicant->applicant_email,
                        $applicant->applicant_contact_number,
                        $applicant->applicant_brgy,
                        $applicant->applicant_school_name,
                        $applicant->applicant_course,
                        $applicant->applicant_year_level,
                        $applicant->applicant_acad_year,
                        $applicant->applicant_gwa,
                        $applicant->applicant_income,
                        $applicant->applicant_parent_income,
                        $applicant->applicant_siblings,
                        $applicant->application->applicationPersonnel->status ?? 'N/A',
                        $applicant->application->date_submitted ?? 'N/A',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export applicants: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get available filter options
     */
    public function filterOptions(Request $request)
    {
        try {
            $options = [];

            // Barangays
            $options['barangays'] = Applicant::distinct()
                ->pluck('applicant_brgy')
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            // Academic years
            $options['academic_years'] = Applicant::distinct()
                ->pluck('applicant_acad_year')
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            // Schools
            $options['schools'] = Applicant::distinct()
                ->pluck('applicant_school_name')
                ->filter()
                ->sort()
                ->values()
                ->toArray();

            // Statuses
            $options['statuses'] = ['Pending', 'Approved', 'Rejected'];

            return $this->successResponse($options, 'Filter options retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get filter options: ' . $e->getMessage(), 500);
        }
    }
}
