<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // << important
use Illuminate\Notifications\Notifiable;

class Lydopers extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_lydopers';
    protected $primaryKey = 'lydopers_id';
    public $timestamps = true; // set to true kung may created_at/updated_at

    protected $fillable = [
        'lydopers_fname','lydopers_mname','lydopers_lname','lydopers_suffix',
        'lydopers_bdate','lydopers_address','lydopers_email','lydopers_contact_number',
        'lydopers_username','lydopers_pass','lydopers_role','lydopers_status'
    ];

    protected $hidden = ['lydopers_pass'];

    // Para gumana ang Auth::attempt gamit ang custom password column
    public function getAuthPassword()
    {
        return $this->lydopers_pass;
    }

    // (Optional) kung wala kayong remember_token column
    public function setRememberToken($value) {}
    public function getRememberTokenName(){ return null; }
}
