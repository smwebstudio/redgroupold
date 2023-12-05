<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function getOptionByName($opt_name)
    {
        return self::where('name', $opt_name)->first();
    }
}
