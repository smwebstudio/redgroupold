<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementType extends Model
{
	public function announcements()
	{
		return $this->belongsTo('App\Announcement');
	}
}
