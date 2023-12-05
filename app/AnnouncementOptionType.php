<?php
	
	namespace App;
	
	use Illuminate\Database\Eloquent\Model;
	
	class AnnouncementOptionType extends Model
	{
		public $fillable = ['title', 'multiselect'];
        
        public function options()
        {
            return $this->hasMany('App\AnnouncementOption', 'type_id');
        }
	}
