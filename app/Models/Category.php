<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    public function performers()
    {
        return $this->hasManyThrough(\App\Models\Performer::class, 'model_to_category', 'model_id', 'category_id');
    }
}
