<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Lydopers extends Authenticatable
{
    protected $table = 'tbl_lydopers';
    protected $primaryKey = 'lydopers_id';
    
    protected $fillable = [
        'lydopers_fname',
        'lydopers_mname', 
        'lydopers_lname',
        'lydopers_suffix',
        'lydopers_email',
        'lydopers_password',
        'lydopers_address',
        'lydopers_contact_number',
        'lydopers_bdate',
        'lydopers_role',
        'lydopers_status'
    ];

    protected $hidden = [
        'lydopers_password'
    ];

    public function getAuthPassword()
    {
        return $this->lydopers_password;
    }
}