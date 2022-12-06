<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = "reviews";
    protected $fillable =[

        'user_id',
        'consultant_id',
        'comment',
        'star_num',
        'is_active',
        'created_at', 
        'updated_at',
    ];
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }

    public function user()
    {

        return $this->belongsTo(User::class, 'user_id');
    }

    public function consultant()
    {

        return $this->belongsTo(User::class, 'consultant_id');
    }
}
