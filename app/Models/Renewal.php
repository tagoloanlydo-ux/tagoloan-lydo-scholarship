<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    use HasFactory;

    protected $table = 'tbl_renewal';
    protected $primaryKey = 'renewal_id';
    public $timestamps = true;

    protected $fillable = [
        'scholar_id', 'renewal_cert_of_reg', 'renewal_grade_slip',
        'renewal_brgy_indigency', 'renewal_semester', 'renewal_acad_year',
        'renewal_start_date', 'renewal_deadline',
        'date_submitted', 'renewal_status'
    ];

    protected $casts = [
        'renewal_start_date' => 'date',
        'renewal_deadline' => 'date',
        'date_submitted' => 'date',
    ];

    public function scholar()
    {
        return $this->belongsTo(Scholar::class, 'scholar_id', 'scholar_id');
    }
}
