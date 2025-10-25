<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationPersonnel extends Model
{
    use HasFactory;

    protected $table = 'tbl_application_personnel';
    protected $primaryKey = 'application_personnel_id';
    public $timestamps = true;
        public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'application_id', 'lydopers_id',
        'initial_screening', 'remarks', 'status',
        'reviewer_comment', 'is_bad', 'intake_sheet_token',
        'intake_sheet_token_expires_at', 'update_token',
        'rejection_reason', 'application_letter_status',
        'cert_of_reg_status', 'grade_slip_status',
        'brgy_indigency_status', 'student_id_status'
    ];
}
