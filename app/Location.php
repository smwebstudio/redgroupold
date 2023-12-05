<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function parent()
    {
    	return $this->hasOne('App\Location', 'id', 'parent_id')->orderBy('title', 'ASC');
    }

    public function childs()
    {
    	return $this->hasMany('App\Location', 'parent_id', 'id')->orderBy('title', 'ASC');
    }
}
