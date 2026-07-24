<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     protected $fillable = ['name', 'slug', 'type'];

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    public function feasibilityStudies()
    {
        return $this->hasMany(FeasibilityStudy::class);
    }

    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBaseItem::class);
    }
}
