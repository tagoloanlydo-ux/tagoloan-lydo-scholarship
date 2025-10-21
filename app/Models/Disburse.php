<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disburse extends Model
{
    use HasFactory;

    protected $table = 'tbl_disburse';
    protected $primaryKey = 'disburse_id';
    public $timestamps = true;

    protected $fillable = [
        'scholar_id', 'lydopers_id', 'disburse_semester',
        'disburse_acad_year', 'disburse_amount', 'disburse_date'
    ];
}
