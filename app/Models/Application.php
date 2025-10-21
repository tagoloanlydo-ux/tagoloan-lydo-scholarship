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
        'applicant_id', 'application_letter', 'cert_of_reg',
        'grade_slip', 'brgy_indigency', 'student_id', 'date_submitted'
    ];
}
