<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementStatusType extends Model
{
    public function announcements()
    {
        return $this->belongsToMany('App\Announcement', 'announcement_statuses', 'status_id', 'announcement_id')
            ->withPivot('archive_end_date', 'archive_note', 'rent_start_date', 'rent_end_date', 'rent_start_price', 'rent_start_price_currency_id', 'rent_end_price', 'rent_end_price_currency_id', 'rent_agent_id', 'rent_renter_id', 'rent_note', 'current')
            ->withTimestamps();
        
    }
}
