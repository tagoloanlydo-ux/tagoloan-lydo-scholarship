<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyIntakeSheet extends Model
{
    use HasFactory;

    protected $table = 'family_intake_sheets';

    protected $fillable = [
        // Foreign keys
        'application_personnel_id',
        'lydo_personnel_id',

        // Head of Family
        'head_4ps',
        'head_ipno',
        'head_address',
        'head_zone',
        'head_dob',
        'head_pob',
        'head_educ',
        'head_occ',
        'head_religion',
        'serial_number',
        'location',

        // Household Info
        'house_total_income',
        'house_net_income',
        'other_income',
        'house_house',
        'house_lot',
        'house_water',
        'house_electric',
        'house_remarks',

        // JSON fields
        'family_members',
        'social_service_records',

        // Health & Signatures
        'hc_estimated_cost',
        'worker_name',
        'officer_name',
        'date_entry',
        'signature_client',
        'signature_worker',
        'signature_officer',
    ];

    /**
     * Cast JSON fields automatically.
     */
    protected $casts = [
        'family_members' => 'array',
        'social_service_records' => 'array',
        'head_dob' => 'date',
        'date_entry' => 'date',
    ];

    /**
     * Relationship: belongs to an applicant (tbl_application_personnel)
     */
    public function applicant()
    {
        return $this->belongsTo(ApplicationPersonnel::class, 'application_personnel_id');
    }

    /**
     * Relationship: belongs to a LYDO personnel (tbl_lydo_personnel)
     */
    public function lydoPersonnel()
    {
        return $this->belongsTo(LydoPersonnel::class, 'lydo_personnel_id');
    }
}
