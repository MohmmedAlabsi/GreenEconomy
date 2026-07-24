<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
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
