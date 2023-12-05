<?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    
    class Files extends Model
    {
        /**
         * Get the project that owns the file.
         */
        public function project()
        {
            return $this->belongsTo('App\Project');
        }
        
        /**
         * Get the project that owns the file.
         */
        public function announcement()
        {
            return $this->belongsTo('App\Announcement', 'post_id');
        }
        
    }
