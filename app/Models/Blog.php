<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $table = "blogs";
    protected $fillable =[

        'user_id',
        'category_id',
        'title',
        'body',
        'image',
        'star_num',
        'share_num',
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
}
