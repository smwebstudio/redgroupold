<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public function products()
    {
    	return $this->hasMany('App\Product', 'currency_id');
    }
    
    public function announcements()
    {
    	return $this->hasMany('App\Announcement', 'price_currency_id');
    }
}
