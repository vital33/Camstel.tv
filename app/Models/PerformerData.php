<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformerData extends Model
{
    protected $table = 'model_data';

    public static $types = [
        "Age",
        "Country",
        "Favorite",
        "Gender",
        "GoldShow",
        "HD",
        "Headline",
        "InExclusiveShow",
        "Languages",
        "LiveStatus",
        "NewModel",
        "OnBreak",
        "PartyChat",
        "Phone",
        "PreGoldShow",
        "Rating",
        "showLiveCapture",
        "SpecialShow",
        "Stars",
        "StatusKey",
        "Thumbnail",
    ];
}
