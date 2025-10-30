<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FamilyIntakeSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IntakeSheetController extends Controller
{
    /**
     * Display a listing of the intake sheets.
     */
    public function index(Request $request)
    {
        $query = FamilyIntakeSheet::with(['applicant', 'lydoPersonnel']);

        // Apply filters if provided
        if ($request->filled('application_personnel_id')) {
            $query->where('application_personnel_id', $request->application_personnel_id);
        }

        if ($request->filled('lydo_personnel_id')) {
            $query->where('lydo_personnel_id', $request->lydo_personnel_id);
        }

        $intakeSheets = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $intakeSheets
        ]);
    }

    /**
     * Store a newly created intake sheet.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_personnel_id' => 'required|integer|exists:tbl_application_personnel,application_personnel_id',
            'lydo_personnel_id' => 'nullable|integer|exists:tbl_lydopers,lydopers_id',
            'applicant_fname' => 'nullable|string|max:255',
            'applicant_mname' => 'nullable|string|max:255',
            'applicant_lname' => 'nullable|string|max:255',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'nullable|string|max:10',
            'head_4ps' => 'nullable|string|max:50',
            'head_ipno' => 'nullable|string|max:50',
            'head_address' => 'nullable|string|max:500',
            'head_zone' => 'nullable|string|max:50',
            'head_barangay' => 'nullable|string|max:100',
            'head_pob' => 'nullable|string|max:100',
            'head_dob' => 'nullable|date',
            'head_educ' => 'nullable|string|max:100',
            'head_occ' => 'nullable|string|max:100',
            'head_religion' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'house_total_income' => 'nullable|numeric',
            'house_net_income' => 'nullable|numeric',
            'other_income' => 'nullable|numeric',
            'house_house' => 'nullable|string|max:255',
            'house_house_value' => 'nullable|numeric',
            'house_lot' => 'nullable|string|max:255',
            'house_lot_value' => 'nullable|numeric',
            'house_house_rent' => 'nullable|numeric',
            'house_lot_rent' => 'nullable|numeric',
            'house_water' => 'nullable|string|max:100',
            'house_electric' => 'nullable|string|max:100',
            'house_remarks' => 'nullable|string|max:1000',
            'family_members' => 'nullable|array',
            'social_service_records' => 'nullable|array',
            'rv_service_records' => 'nullable|array',
            'hc_estimated_cost' => 'nullable|numeric',
            'worker_name' => 'nullable|string|max:255',
            'officer_name' => 'nullable|string|max:255',
            'date_entry' => 'nullable|date',
            'signature_client' => 'nullable|string', // base64 image
            'signature_worker' => 'nullable|string', // base64 image
            'signature_officer' => 'nullable|string', // base64 image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle signature storage
        $signaturePaths = [];
        $signatures = ['signature_client', 'signature_worker', 'signature_officer'];

        foreach ($signatures as $signatureKey) {
            if ($request->has($signatureKey) && $request->$signatureKey) {
                $signatureData = $request->$signatureKey;
                if (strpos($signatureData, 'data:image') === 0) {
                    // Decode base64 image
                    $imageData = explode(',', $signatureData);
                    $image = base64_decode($imageData[1]);

                    // Generate filename
                    $filename = 'signature_' . $request->application_personnel_id . '_' . $signatureKey . '_' . time() . '.png';

                    // Store in storage/signatures folder
                    Storage::put('signatures/' . $filename, $image);

                    $signaturePaths[$signatureKey] = 'signatures/' . $filename;
                }
            }
        }

        $data = $request->all();
        $data = array_merge($data, $signaturePaths);

        $intakeSheet = FamilyIntakeSheet::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Intake sheet created successfully',
            'data' => $intakeSheet
        ], 201);
    }

    /**
     * Display the specified intake sheet.
     */
    public function show($id)
    {
        $intakeSheet = FamilyIntakeSheet::with(['applicant', 'lydoPersonnel'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $intakeSheet
        ]);
    }

    /**
     * Update the specified intake sheet.
     */
    public function update(Request $request, $id)
    {
        $intakeSheet = FamilyIntakeSheet::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'lydo_personnel_id' => 'nullable|integer|exists:tbl_lydopers,lydopers_id',
            'applicant_fname' => 'nullable|string|max:255',
            'applicant_mname' => 'nullable|string|max:255',
            'applicant_lname' => 'nullable|string|max:255',
            'applicant_suffix' => 'nullable|string|max:10',
            'applicant_gender' => 'nullable|string|max:10',
            'head_4ps' => 'nullable|string|max:50',
            'head_ipno' => 'nullable|string|max:50',
            'head_address' => 'nullable|string|max:500',
            'head_zone' => 'nullable|string|max:50',
            'head_barangay' => 'nullable|string|max:100',
            'head_pob' => 'nullable|string|max:100',
            'head_dob' => 'nullable|date',
            'head_educ' => 'nullable|string|max:100',
            'head_occ' => 'nullable|string|max:100',
            'head_religion' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'house_total_income' => 'nullable|numeric',
            'house_net_income' => 'nullable|numeric',
            'other_income' => 'nullable|numeric',
            'house_house' => 'nullable|string|max:255',
            'house_house_value' => 'nullable|numeric',
            'house_lot' => 'nullable|string|max:255',
            'house_lot_value' => 'nullable|numeric',
            'house_house_rent' => 'nullable|numeric',
            'house_lot_rent' => 'nullable|numeric',
            'house_water' => 'nullable|string|max:100',
            'house_electric' => 'nullable|string|max:100',
            'house_remarks' => 'nullable|string|max:1000',
            'family_members' => 'nullable|array',
            'social_service_records' => 'nullable|array',
            'rv_service_records' => 'nullable|array',
            'hc_estimated_cost' => 'nullable|numeric',
            'worker_name' => 'nullable|string|max:255',
            'officer_name' => 'nullable|string|max:255',
            'date_entry' => 'nullable|date',
            'signature_client' => 'nullable|string', // base64 image
            'signature_worker' => 'nullable|string', // base64 image
            'signature_officer' => 'nullable|string', // base64 image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle signature updates
        $signaturePaths = [];
        $signatures = ['signature_client', 'signature_worker', 'signature_officer'];

        foreach ($signatures as $signatureKey) {
            if ($request->has($signatureKey) && $request->$signatureKey) {
                $signatureData = $request->$signatureKey;
                if (strpos($signatureData, 'data:image') === 0) {
                    // Decode base64 image
                    $imageData = explode(',', $signatureData);
                    $image = base64_decode($imageData[1]);

                    // Generate filename
                    $filename = 'signature_' . $intakeSheet->application_personnel_id . '_' . $signatureKey . '_' . time() . '.png';

                    // Store in storage/signatures folder
                    Storage::put('signatures/' . $filename, $image);

                    $signaturePaths[$signatureKey] = 'signatures/' . $filename;

                    // Delete old signature if exists
                    if ($intakeSheet->$signatureKey) {
                        Storage::delete($intakeSheet->$signatureKey);
                    }
                }
            }
        }

        $data = $request->all();
        $data = array_merge($data, $signaturePaths);

        $intakeSheet->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Intake sheet updated successfully',
            'data' => $intakeSheet
        ]);
    }

    /**
     * Remove the specified intake sheet.
     */
    public function destroy($id)
    {
        $intakeSheet = FamilyIntakeSheet::findOrFail($id);

        // Delete associated signature files
        $signatures = ['signature_client', 'signature_worker', 'signature_officer'];
        foreach ($signatures as $signature) {
            if ($intakeSheet->$signature) {
                Storage::delete($intakeSheet->$signature);
            }
        }

        $intakeSheet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Intake sheet deleted successfully'
        ]);
    }
}
