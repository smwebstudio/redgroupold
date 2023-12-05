<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequiredProject extends Model
{
    protected $fillable = ['user_id', 'phone', 'property_type_id', 'states', 'communities', 'rooms', 'price_min', 'price_max', 'by_sms', 'by_email', 'last_sent_id'];

    // Relations
    public function categories() {
    	return $this->belongsTo("App\Category",'property_type_id');
    }

    public function users() {
    	return $this->belongsTo("App\User",'user_id');
    }

    public function state() {
    	return $this->belongsTo("App\Location",'states');
    }

    public function community() {
        return $this->belongsTo("App\Location",'communities');
    }
}
