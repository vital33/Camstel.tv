<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    public static function categoryListShort()
    {
        return [

            ['slug' => '/','label' => 'All Cams','icon' => 'house',],
            ['slug' => 'girls','label' => 'Girls','icon' => 'gender-female',],
            ['slug' => 'boys','label' => 'Boys','icon' => 'gender-male',],
            ['slug' => 'couples','label' => 'Couples','icon' => 'gender-ambiguous',],
            ['slug' => 'trans','label' => 'Trans','icon' => 'gender-trans',],
            /***************** REGION ********************/
            ['label' => 'REGION:','icon' => 'globe',],
            /*********************************************/
            ['slug' => 'usa_can','label' => 'USA/Canada',],
            ['slug' => 'south_america','label' => 'South America',],
            ['slug' => 'europe','label' => 'Europe',],
            ['slug' => 'asia','label' => 'Asia',],
            /***************** RACE ********************/
            ['label' => 'RACE:','icon' => 'person-circle',],
            /*********************************************/
            ['slug' => 'white','label' => 'White',],
            ['slug' => 'black','label' => 'Black',],
            ['slug' => 'latino','label' => 'Latino',],
            ['slug' => 'asian','label' => 'Asian',],
            /******************** AGE ********************/
            ['label' => 'AGE:','icon' => 'sort-down-alt',],
            /*********************************************/
            ['slug' => '18_25','label' => '18-25',],
            ['slug' => '25_40','label' => '25-40',],
            ['slug' => '40_plus','label' => '40+',],
            /************ TOP CATEGORIES *****************/
            ['label' => 'TOP CATEGORIES:','icon' => 'bar-chart-line-fill',],
            /*********************************************/
            ['slug' => 'big_boobs','label' => 'Big Boobs',],
            ['slug' => 'squirt','label' => 'Squirt',],
            ['slug' => 'lovense','label' => 'Lovense',],
            ['slug' => 'anal','label' => 'Anal',],
            ['slug' => 'teen','label' => 'Teen',],
            ['slug' => 'big_ass','label' => 'Big Ass',],
            ['slug' => 'latina','label' => 'Latina',],
            ['slug' => 'natural','label' => 'Natural',],
            ['slug' => 'shy','label' => 'Shy',],
            ['slug' => 'feet','label' => 'Feet',],
            ['slug' => 'milf','label' => 'MILF',],
            ['slug' => 'small_tits','label' => 'Small Tits',],
            ['slug' => 'skinny','label' => 'Skinny',],
            ['slug' => 'pornstar','label' => 'Pornstar',],
            ['slug' => 'all_categories','label' => 'ALL CATEGORIES','icon' => 'gem',]
        ];
    }

    public function performers()
    {
        return $this->hasManyThrough(\App\Models\Performer::class,'model_to_category','model_id','category_id');
    }
}
