<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'tbl_settings';

    public $timestamps = false;

    protected $fillable = [
        'application_start_date',
        'application_deadline',
        'renewal_start_date',
        'renewal_deadline',
        'renewal_semester',
    ];

    protected $casts = [
        'application_start_date' => 'date',
        'application_deadline' => 'date',
        'renewal_start_date' => 'date',
        'renewal_deadline' => 'date',
    ];
}
