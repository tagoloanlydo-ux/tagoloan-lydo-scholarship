<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Scholar extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'tbl_scholar';
    protected $primaryKey = 'scholar_id';
    public $timestamps = true;

    protected $fillable = [
        'application_id', 'scholar_username', 'scholar_pass',
        'date_activated', 'scholar_status'
    ];

    // Hide sensitive information
    protected $hidden = ['scholar_pass'];

    /**
     * Get the password for the user (for Sanctum authentication)
     */
    public function getAuthPassword()
    {
        return $this->scholar_pass;
    }

    /**
     * Get the column name for the authenticatable "username"
     */
    public function getAuthIdentifierName()
    {
        return 'scholar_username';
    }

    /**
     * Add accessor for email to map to applicant's email
     */
    public function getEmailAttribute()
    {
        return $this->applicant ? $this->applicant->applicant_email : null;
    }

    /**
     * Relationship: Scholar belongs to Application
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    /**
     * Relationship: Scholar has Applicant through Application
     */
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

    /**
     * Relationship: Scholar has many Renewals
     */
    public function renewals()
    {
        return $this->hasMany(Renewal::class, 'scholar_id', 'scholar_id');
    }

    /**
     * (Optional) Handle remember token if you don't have the column
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Do nothing if you don't have remember_token column
    }

    public function getRememberTokenName()
    {
        return null;
    }
}