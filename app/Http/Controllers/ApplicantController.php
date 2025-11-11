<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Lydopers;
use App\Models\ApplicationPersonnel;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $applicants = Applicant::paginate(15);
        return response()->json($applicants);
    }

    /**
     * Store a new applicant AND create an application record in one submission.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('=== APPLICATION SUBMISSION STARTED ===');

            // Validate applicant data using request validate
            $validatedApplicant = $request->validate([
                'applicant_fname' => 'required|string|max:255',
                'applicant_mname' => 'nullable|string|max:255',
                'applicant_lname' => 'required|string|max:255',
                'applicant_suffix' => 'nullable|string|max:50',
                'applicant_gender' => 'required|string|max:50',
                'applicant_bdate' => 'required|date',
                'applicant_civil_status' => 'required|string|max:50',
                'applicant_pob' => 'nullable|string|max:255',
                'applicant_brgy' => 'required|string|max:255',
                'applicant_email' => 'required|email|unique:tbl_applicant,applicant_email',
                'applicant_contact_number' => 'required|string|max:20',
                'applicant_school_name' => 'required|string|max:255',
                'applicant_year_level' => 'required|string|max:50',
                'applicant_course' => 'required|string|max:255',
                'applicant_acad_year' => 'required|string|max:20',
            ]);

            Log::info('Creating applicant record...');

            // Create applicant
            $applicant = Applicant::create($validatedApplicant);
            Log::info('Applicant created successfully', [
                'applicant_id' => $applicant->applicant_id,
                'name' => $applicant->applicant_fname . ' ' . $applicant->applicant_lname
            ]);

            // Prepare application data
            $applicationData = [
                'applicant_id' => $applicant->applicant_id,
                'date_submitted' => now(),
            ];

            Log::info('Processing document files...');

            // Handle document file paths
            $fileFields = [
                'application_letter',
                'cert_of_reg',
                'grade_slip',
                'brgy_indigency',
                'student_id'
            ];

            foreach ($fileFields as $field) {
                if ($request->has($field) && !empty($request->$field)) {
                    $applicationData[$field] = $request->$field;
                    Log::info("Document added to application: $field");
                } else {
                    Log::info("Document field empty or missing: $field");
                }
            }

            Log::info('Creating application record...');

            // Create application record
            $application = Application::create($applicationData);
            Log::info('Application created successfully', [
                'application_id' => $application->application_id,
                'applicant_id' => $application->applicant_id
            ]);

            // 🔥 CRITICAL FIX: Find available mayor staff and assign properly
            $mayorStaff = DB::table('tbl_lydopers')
                ->where('lydopers_role', 'mayor_staff')
                ->where('lydopers_status', 'active')
                ->first();

            if (!$mayorStaff) {
                // Fallback: get any active staff
                $mayorStaff = DB::table('tbl_lydopers')
                    ->where('lydopers_status', 'active')
                    ->first();
            }

            // 🔥 CRITICAL FIX: Create ApplicationPersonnel record with VALID remarks
            $applicationPersonnelData = [
                'application_id' => $application->application_id,
                'lydopers_id' => $mayorStaff ? $mayorStaff->lydopers_id : 1, // Default to 1 if no staff found
                'initial_screening' => 'Pending',
                'remarks' => 'Poor', // 🔥 CHANGE FROM 'Pending' TO VALID POVERTY LEVEL
                'status' => 'Waiting',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $applicationPersonnel = ApplicationPersonnel::create($applicationPersonnelData);
            Log::info('ApplicationPersonnel record created successfully', [
                'application_personnel_id' => $applicationPersonnel->application_personnel_id,
                'application_id' => $applicationPersonnel->application_id,
                'lydopers_id' => $applicationPersonnel->lydopers_id,
                'remarks' => $applicationPersonnel->remarks // Log the actual value
            ]);

            DB::commit();

            Log::info('=== APPLICATION SUBMISSION COMPLETED SUCCESSFULLY ===');

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'data' => [
                    'applicant' => $applicant,
                    'application' => $application,
                    'application_personnel' => $applicationPersonnel,
                    'submission_date' => $application->date_submitted
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('APPLICATION SUBMISSION FAILED: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Application submission failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $applicant = Applicant::find($id);
        return response()->json($applicant);
    }

    public function update(Request $request, $id)
    {
        $applicant = Applicant::find($id);
        $applicant->update($request->all());
        return response()->json($applicant);
    }

    public function destroy($id)
    {
        Applicant::destroy($id);
        return response()->json(['message' => 'Applicant deleted']);
    }

    public function meta()
    {
        $meta = [
            'total' => Applicant::count(),
            'by_gender' => Applicant::selectRaw('applicant_gender, COUNT(*) as count')->groupBy('applicant_gender')->get(),
            'by_barangay' => Applicant::selectRaw('applicant_brgy, COUNT(*) as count')->groupBy('applicant_brgy')->get(),
        ];
        return response()->json($meta);
    }

    public function distributionByBarangay()
    {
        $distribution = Applicant::selectRaw('applicant_brgy, COUNT(*) as count')
            ->groupBy('applicant_brgy')
            ->orderBy('count', 'desc')
            ->get();
        return response()->json($distribution);
    }

    public function distributionBySchool()
    {
        $distribution = Applicant::selectRaw('applicant_school_name, COUNT(*) as count')
            ->groupBy('applicant_school_name')
            ->orderBy('count', 'desc')
            ->get();
        return response()->json($distribution);
    }
}
