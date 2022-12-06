<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = "settings";
    protected $fillable =[

        'site_title',
        'site_logo',
        'terms_conditions',
        'about_us',
        'social_media',
        'whatsApp',
        'created_at', 
        'updated_at',
    ];
    protected $casts = ['social_media' => 'array'];
}
