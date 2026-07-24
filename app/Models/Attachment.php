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
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
