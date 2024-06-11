<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performer extends Model
{
    protected $table = 'model';

    protected $fillable = [
        "nick",
        "external_id",
        "created_at",
        "updated_at",
        "vendor_id",
        "external_sort_order",
        "last_online_at"
    ];

    public function data()
    {
        return $this->hasMany(\App\Models\PerformerData::class, 'model_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(\App\Models\Category::class, 'model_to_category', 'model_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
