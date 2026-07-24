<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
      protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function feasibilityStudies()
    {
        return $this->hasMany(FeasibilityStudy::class);
    }

    public function feasibilityRequests()
    {
        return $this->hasMany(FeasibilityRequest::class);
    }

    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBaseItem::class);
    }
}
