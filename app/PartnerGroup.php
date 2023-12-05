<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerGroup extends Model
{
    protected $fillable = ['title', 'locale', 'main_id'];

    public function partners()
    {
    	return $this->hasMany('App\Partner', 'group_id')->orderBy("id","asc");
    }
}
