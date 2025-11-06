<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Add this for Sanctum

class Lydopers extends Authenticatable
{
    use Notifiable, HasApiTokens; // Add HasApiTokens trait

    protected $table = 'tbl_lydopers';
    protected $primaryKey = 'lydopers_id';
    public $timestamps = true;

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

    // Specify the column name for authentication (username/email)
    public function getAuthIdentifierName()
    {
        return 'lydopers_email';
    }

    // (Optional) kung wala kayong remember_token column
    public function setRememberToken($value) {}
    public function getRememberTokenName(){ return null; }
}