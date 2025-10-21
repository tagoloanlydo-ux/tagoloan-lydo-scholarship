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
        'initial_screening', 'remarks', 'status'
    ];
}
