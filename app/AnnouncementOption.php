<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementOption extends Model
{
    public function optionType()
    {
        return $this->belongsTo('App\AnnouncementOptionType', 'type_id');
    }
    
    public function announcements()
    {
        return $this->belongsToMany('App\Announcement', 'announcement_opts','option_id', 'announcement_id')->withTimestamps();
    }
}
