<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntakeSheetController extends Controller
{
    public function printView($id)
    {
        // Get data from query parameter or session
        $data = [];
        
        if (request()->has('data')) {
            $data = json_decode(request()->get('data'), true) ?? [];
        }
        
        // Validate that we have the necessary data
        if (empty($data)) {
            abort(404, 'Print data not found');
        }

        // Format the data properly for the print view
        $formattedData = [
            'serialNumber' => $data['head']['serial'] ?? 'N/A',
            'head' => $data['head'] ?? [],
            'location' => $data['location'] ?? '',
            'family' => $data['family'] ?? [],
            'house' => $data['house'] ?? [],
            'signatures' => $data['signatures'] ?? [],
            'applicationId' => $id,
            'printDate' => now()->format('F d, Y h:i A')
        ];

        return view('print_intake', $formattedData);
    }

    // Alternative method to load from database if needed
public function printFromDatabase($application_personnel_id)
{
    try {
        $intakeSheet = DB::table('family_intake_sheets')
            ->where('application_personnel_id', $application_personnel_id)
            ->first();

        if (!$intakeSheet) {
            abort(404, 'Intake sheet not found.');
        }

        $applicant = DB::table('tbl_applicant as a')
            ->join('tbl_application as app', 'a.applicant_id', '=', 'app.applicant_id')
            ->join('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
            ->where('ap.application_personnel_id', $application_personnel_id)
            ->select('a.*')
            ->first();

        $familyMembers = [];
        if (!empty($intakeSheet->family_members)) {
            $familyMembers = json_decode($intakeSheet->family_members, true) ?? [];
        }

        // ADD SOCIAL SERVICES DATA
        $socialServices = [];
        if (!empty($intakeSheet->social_services)) {
            $socialServices = json_decode($intakeSheet->social_services, true) ?? [];
        }

        return view('print_intake', [
            'serialNumber' => $intakeSheet->serial_number ?? 'N/A',
            'head' => [
                'fname' => $applicant->applicant_fname ?? '',
                'mname' => $applicant->applicant_mname ?? '',
                'lname' => $applicant->applicant_lname ?? '',
                'suffix' => $applicant->applicant_suffix ?? '',
                '_4ps' => $intakeSheet->head_4ps ?? '',
                'ipno' => $intakeSheet->head_ipno ?? '',
                'address' => $intakeSheet->head_address ?? '',
                'zone' => $intakeSheet->head_zone ?? '',
                'barangay' => $applicant->applicant_brgy ?? '',
                'dob' => $applicant->applicant_bdate ?? '',
                'pob' => $intakeSheet->head_pob ?? '',
                'educ' => $intakeSheet->head_educ ?? '',
                'occ' => $intakeSheet->head_occ ?? '',
                'religion' => $intakeSheet->head_religion ?? '',
                'sex' => $applicant->applicant_gender ?? ''
            ],
            'location' => $intakeSheet->location ?? '',
            'family' => $familyMembers, // ITO ANG TAMA - familyMembers
            'services' => $socialServices, // DAGDAG ITO
            'house' => [
                'total_income' => $intakeSheet->house_total_income ?? '',
                'net_income' => $intakeSheet->house_net_income ?? '',
                'other_income' => $intakeSheet->other_income ?? '',
                'house' => $intakeSheet->house_house ?? '',
                'lot' => $intakeSheet->house_lot ?? '',
                'house_value' => $intakeSheet->house_value ?? '',
                'lot_value' => $intakeSheet->lot_value ?? '',
                'house_rent' => $intakeSheet->house_rent ?? '',
                'lot_rent' => $intakeSheet->lot_rent ?? '',
                'water' => $intakeSheet->house_water ?? '',
                'electric' => $intakeSheet->house_electric ?? ''
            ],
            'signatures' => [
                'client' => $intakeSheet->signature_client ?? ''
            ],
            'applicationId' => $application_personnel_id,
            'printDate' => now()->format('F d, Y h:i A')
        ]);

    } catch (\Exception $e) {
        abort(500, 'Error loading intake sheet for printing.');
    }
}
}