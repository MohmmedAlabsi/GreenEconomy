<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseItem extends Model
{
     protected $fillable = [
        'title',
        'summary',
        'content',
        'type',
        'status',
        'category_id',
        'media_url',
        'file_size_bytes',
        'view_count',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
