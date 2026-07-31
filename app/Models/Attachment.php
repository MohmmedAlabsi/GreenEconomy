<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'file_type',
        'user_id',
        'file_name',
        'file_size',
        'url',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
