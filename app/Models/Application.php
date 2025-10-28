<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'tbl_application';
    protected $primaryKey = 'application_id';
    public $timestamps = true;
        public $incrementing = true;
    protected $keyType = 'int';
    

    protected $fillable = [
        'applicant_id', 'date_submitted', 'application_letter', 'cert_of_reg',
        'grade_slip', 'brgy_indigency', 'student_id'
    ];

    protected $attributes = [
        'application_letter' => null,
        'cert_of_reg' => null,
        'grade_slip' => null,
        'brgy_indigency' => null,
        'student_id' => null,
    ];

    protected $casts = [
        'application_letter' => 'string',
        'cert_of_reg' => 'string',
        'grade_slip' => 'string',
        'brgy_indigency' => 'string',
        'student_id' => 'string',
    ];
}
