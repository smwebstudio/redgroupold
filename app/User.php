<?php
    
    namespace App;
    
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    
    class User extends Authenticatable
    {
        use Notifiable;
        
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name', 'email', 'click_contact',
        ];
        
        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'password', 'remember_token',
        ];
        
        /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
        ];
        
        public function contact_types()
        {
            return $this->belongsToMany('App\ContactType', 'contact_types_users')->withPivot('value');
        }
        
        public function announcements()
        {
            return $this->hasMany('App\Announcement', 'agent_id');
        }
    
        // /**
        //  *  Get user file relation
        //  *
        //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
        //  */
        // protected function files()
        // {
        //     return $this->hasMany('App\Files', 'post_id');
        // }
        //
        // public function userProfileImg()
        // {
        //     return $this->files()->where('post_type_id', 3)->first();
        // }
    }
