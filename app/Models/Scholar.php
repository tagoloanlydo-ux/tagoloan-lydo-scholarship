<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholar extends Model
{
    use HasFactory;

    protected $table = 'tbl_scholar';
    protected $primaryKey = 'scholar_id';
    public $timestamps = true;

    protected $fillable = [
        'application_id', 'scholar_username', 'scholar_pass',
        'date_activated', 'scholar_status'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function applicant()
    {
        return $this->hasOneThrough(
            Applicant::class,
            Application::class,
            'application_id', // Foreign key on Application table
            'applicant_id',   // Foreign key on Applicant table
            'application_id', // Local key on Scholar table
            'applicant_id'    // Local key on Application table
        );
    }
}
