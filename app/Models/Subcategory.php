<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $table = "subcategories";
    protected $fillable =[

        'name',
        'is_active',
        'created_at', 
        'updated_at',
    ];
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
}
