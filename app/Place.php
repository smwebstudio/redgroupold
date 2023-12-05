<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    public function type()
    {
        return $this->belongsTo('App\PlaceType', 'place_type_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Place', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Place', 'parent_id');
    }
}
