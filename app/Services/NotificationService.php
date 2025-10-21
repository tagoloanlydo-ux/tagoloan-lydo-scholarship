<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Get new applications notifications
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNewApplications()
    {
        return DB::table("tbl_application as app")
            ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
            ->select("app.application_id", "a.applicant_fname", "a.applicant_lname", "app.created_at")
            ->orderBy("app.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "application",
                    "name" => $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });
    }

    /**
     * Get new remarks notifications
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNewRemarks()
    {
        return DB::table("tbl_application_personnel as ap")
            ->join("tbl_application as app", "ap.application_id", "=", "app.application_id")
            ->join("tbl_applicant as a", "a.applicant_id", "=", "app.applicant_id")
            ->whereIn("ap.remarks", ["Poor", "Non Poor", "Ultra Poor", "Non Indigenous"])
            ->select("ap.remarks", "a.applicant_fname", "a.applicant_lname", "ap.created_at")
            ->orderBy("ap.created_at", "desc")
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    "type" => "remark",
                    "remarks" => $item->remarks,
                    "name" => $item->applicant_fname . " " . $item->applicant_lname,
                    "created_at" => $item->created_at,
                ];
            });
    }

    /**
     * Get combined notifications (applications and remarks)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCombinedNotifications()
    {
        $newApplications = $this->getNewApplications();
        $newRemarks = $this->getNewRemarks();

        return $newApplications->merge($newRemarks)->sortByDesc("created_at");
    }

    /**
     * Get admin notifications (applications and renewals)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAdminNotifications()
    {
        return DB::table('tbl_application_personnel')
            ->join('tbl_application', 'tbl_application_personnel.application_id', '=', 'tbl_application.application_id')
            ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
            ->select(
                'tbl_applicant.applicant_fname as name',
                'tbl_application_personnel.status as status',
                'tbl_application_personnel.updated_at as created_at',
                DB::raw("'application' as type")
            )
            ->whereIn('tbl_application_personnel.status', ['Approved', 'Rejected'])
            ->unionAll(
                DB::table('tbl_renewal')
                    ->join('tbl_scholar', 'tbl_renewal.scholar_id', '=', 'tbl_scholar.scholar_id')
                    ->join('tbl_application', 'tbl_scholar.application_id', '=', 'tbl_application.application_id')
                    ->join('tbl_applicant', 'tbl_application.applicant_id', '=', 'tbl_applicant.applicant_id')
                    ->select(
                        'tbl_applicant.applicant_fname as name',
                        'tbl_renewal.renewal_status as status',
                        'tbl_renewal.updated_at as created_at',
                        DB::raw("'renewal' as type")
                    )
                    ->whereIn('tbl_renewal.renewal_status', ['Approved', 'Rejected'])
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
