<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
    use HasFactory;

    protected $table = 'tbl_announce';
    protected $primaryKey = 'announce_id';
    public $timestamps = true;

    protected $fillable = [
        'lydopers_id', 'announce_title', 'announce_content',
        'announce_type', 'date_posted'
    ];

    protected $casts = [
        'date_posted' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
