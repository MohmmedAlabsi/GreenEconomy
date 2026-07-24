<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBaseItem extends Model
{
    use HasFactory;
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
