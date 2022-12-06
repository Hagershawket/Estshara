<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $table = "appointments";
    protected $fillable =[

        'consultant_id',
        'date',
        'time_from',
        'time_to',
        'min_num',
        'min_remain',
        'created_at', 
        'updated_at',
    ];

    public function consultant()
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }
}
