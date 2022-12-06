<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantType extends Model
{
    use HasFactory;
    protected $table = "consultant_types";
    protected $fillable =[

        'consultant_id',
        'name',
        'duration',
        'cost',
        'created_at', 
        'updated_at',
    ];

    public function consultant(){

        return $this->belongsTo(User::class, 'consultant_id');
    }
}
