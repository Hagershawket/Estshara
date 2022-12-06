<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantCategory extends Model
{
    use HasFactory;
    protected $table = "consultant_category";
    protected $fillable =[

        'consultant_id',
        'category_id',
        'subcategory_id',
        'created_at', 
        'updated_at',
    ];

    public function consultant(){

        return $this->belongsTo(User::class, 'consultant_id');
    }
}
