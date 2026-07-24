<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_name',
        'default_language',
        'timezone',
        'date_format',
        'production_api_key',
        'test_api_key',
    ];

    public $timestamps = false;
}