<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $table = 'tbl_applicant';
    protected $primaryKey = 'applicant_id';
    public $timestamps = true;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'applicant_fname', 'applicant_mname', 'applicant_lname', 'applicant_suffix',
        'applicant_gender', 'applicant_bdate', 'applicant_civil_status', 'applicant_pob', 'applicant_brgy',
        'applicant_email', 'applicant_contact_number', 'applicant_school_name',
        'applicant_year_level', 'applicant_course', 'applicant_acad_year'
    ];

    /**
     * Get the application for the applicant.
     * Note: Using singular 'application' since one applicant has one application
     */
    public function application()
    {
        return $this->hasOne(Application::class, 'applicant_id', 'applicant_id');
    }

    /**
     * Alternative: If you want to support multiple applications per applicant
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id', 'applicant_id');
    }
}