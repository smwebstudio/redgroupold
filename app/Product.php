<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Product extends Model
{
    protected $guarded = ['id'];

    public function project()
    {
    	return $this->belongsTo('App\Project','post_id', 'project_id')->where('locale', app()->getLocale());
    }

    public function currency()
    {
    	return $this->belongsTo('App\Currency');
    }

    public function checkCookie($id = null) {
    	if(!is_null($id)) {
            $favs = request()->cookie('favs');
            if(!is_null($favs)) {
                $favs = json_decode($favs,true);
	    		if(is_array($favs) && !empty($favs)) {
	    			return in_array($id, $favs) ? true : false;
	    		}
	    	}
    	}
    }


    // Get the short_name attribute of the parent project
    public function shortName() {

        $project =  $this->project()->withoutGlobalScopes()->first();

        return $project ? $project->short_name : '';
    }


    // Get Currency Code
    public function currencyCode() {

        $currency = $this->currency()->first();

        return $code = $currency ? $currency->code : '';
    }
}
