<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    public static function categoryList()
    {
        return [
            [
                'name' => "Top Categories",
                'icon' => null,
                'children' => [
                    [
                        'slug' => 'babes',
                        'name' => 'Babes',
                    ]
                ]
            ]
        ];
    }

    public function performers()
    {
        return $this->hasManyThrough(\App\Models\Performer::class, 'model_to_category', 'model_id', 'category_id');
    }
}
