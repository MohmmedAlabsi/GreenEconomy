<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseItem extends Model
{
    protected $fillable = ['category_id', 'author_id', 'title', 'content', 'views_count'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
