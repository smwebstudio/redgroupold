<?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Faker\Provider\File;
    use phpDocumentor\Reflection\Types\Array_;
    use Image;
    
    
    class Announcement extends Model
    {
        protected $fillable = [
            'code',
            'announcement_type_id',
            'announcement_type_id',
            'property_type_id',
            'agent_id',
            'seller_id',
            'place_id',
            'street',
            'coords',
            'building',
            'apartment',
            'floor',
            'building_floor',
            'rooms',
            'area',
            'land_area',
            'price',
            'price_currency_id',
            'service_fee',
            'service_fee_currency_id',
            'intercom',
            'advertised',
            'urgent',
            'top',
            'professional_note',
            'why_note',
            'other_note'
        ];

        private $post_type_id = 2;
        
        public function announcement_type()
        {
            return $this->belongsTo('App\AnnouncementType', 'announcement_type_id');
        }
        
        public function options()
        {
            return $this->belongsToMany('App\AnnouncementOption', 'announcement_opts', 'announcement_id', 'option_id')->withTimestamps();
        }
        
        public function source()
        {
            return $this->belongsTo('App\Source');
        }
        
        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
         */
        public function statuses()
        {
            return $this->belongsToMany('App\AnnouncementStatusType', 'announcement_statuses', 'announcement_id', 'status_id')
                ->withPivot('archive_end_date', 'archive_note', 'rent_start_date', 'rent_end_date', 'rent_start_price', 'rent_start_price_currency_id', 'rent_end_price', 'rent_end_price_currency_id', 'rent_agent_id', 'rent_renter_id', 'rent_note', 'current')
                ->withTimestamps();
            
        }
        
        public function place()
        {
            return $this->belongsTo('App\Place', 'place_id');
        }
        
        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function currency_price()
        {
            return $this->belongsTo('App\Currency', 'price_currency_id');
        }
        
        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function currency_service_fee()
        {
            return $this->belongsTo('App\Currency', 'service_fee_currency_id');
        }
        
        public function agent()
        {
            return $this->belongsTo('App\User', 'agent_id');
        }
        
        public function files()
        {
            return $this->hasMany('App\Files', 'post_id')->where('visible', 1);
        }
        
        public function property_type()
        {
            return $this->belongsTo('App\PropertyType', 'property_type_id');
        }
        
        public function comments()
        {
            return $this->hasMany('App\Comment', 'post_id');
        }
        
        public function getHotOffer()
        {
            $id = Setting::where('name', 'hot_offer')->first('value');
            $announcement = self::find($id)->first();
            if (isset($announcement) && $announcement->statuses()->where('current', 1)->first()->id == 4)
                return $announcement;
            return null;
        }
        
        public static function getAgents()
        {
            return User::where('user_type_id', 1)->get();
        }
        
        public static function getCeilingHeights($locale, $option_type_id)
        {
            $data = DB::table('announcement_options')
                ->where('type_id', $option_type_id)
                ->where('locale', $locale)
                ->get();
            if (isset($data))
                return $data;
            return false;
        }
        
        // public function storeAnnouncementImage($images, $announcement_id, $code)
        // {
        //     $imgStored = [];
        //     /*ff*/
        //     foreach ($images as $image) {
        //         $img_name = $image->getClientOriginalName();
        //         $filename = time() . $img_name;
        //         $url = '/storage/announcements/' . $code . '/' . $filename;
        //         $filename_path = storage_path('app/public/announcements/' . $code);
                
        //         $image->move($filename_path, $filename);
        //         $imgStored[] = array(
        //             'url' => url($url),
        //             'alt' => $img_name,
        //             'title' => $img_name,
        //             'block_id' => 'bloc_id',
        //             'post_id' => $announcement_id
        //         );
        //     }
        //     return $imgStored;
        // }

        public function storeAnnouncementImage($files, Announcement $announcement, $thumbnail_id = null)
    {
        if(is_array($files)){
            // $setting = Setting::where('name','watermark_url')->first();
            // $watermark = $setting->value;
            $folder_name = $announcement->code;
            $file_url = url('/').'/storage/announcements/'. $folder_name;
            if (!is_dir(dirname(public_path('storage/announcements/' . $folder_name . '/filename')))) {
                mkdir(dirname(public_path('storage/announcements/' . $folder_name . '/filename')), 0775, true);
                // return response()->json('mkdir ' . $a);
            }
            // return is_dir(dirname(public_path('storage/announcements/' . $folder_name . '/filename')));
            foreach ($files as $file) {
                $avatar = $file;
                $filename  = $file->getClientOriginalName();
                $img = Image::make($avatar);
                // $img->insert($watermark, 'top-right', 0, 0);            
                $img->save( public_path('storage/announcements/'. $folder_name . '/' . $filename));

                $file_insert = new Files;
                $file_insert->url = $file_url . '/' . $filename;
                $file_insert->alt = $filename;
                $file_insert->title = $filename;
                $file_insert->post_id = $announcement->id;
                $file_insert->post_type_id = $this->post_type_id;
                $file_insert->save();

            }

            if (isset($thumbnail_id)) {
                $file_name  = $files[$thumbnail_id]->getClientOriginalName();  
                $announcement->thumbnail = $file_url . '/' . $file_name;
                $announcement->save();
            } else {
                $file_name  = $files[0]->getClientOriginalName();  
                $announcement->thumbnail = $file_url . '/' . $file_name;
                $announcement->save();
            }

            return true;
        }else{
            return false;
        }        
    }
        public function agents() {
            return $this->belongsToMany('App\User', 'user_announcements');
        }

        public function getTopAnnouncementsBy($criteria, $status_id = 4, $count = 3)
        {
            // $status = AnnouncementStatusType::find($status_id);
            // return $status->announcements()->where($sort_by, 1)
            //     ->take($count)
            //     ->get();
            return $this->where($criteria, 1)->whereHas('statuses', function($query) use($status_id) {
                $query->where(['status_id' => $status_id, 'current' => 1]);
            })->take($count)->get();
        }
        
        
        public function getRelatedAnnouncements(array $options, $status_id = 2, $count = 4)
        {
            $status = AnnouncementStatusType::find($status_id);
            return $status->announcements()
                ->where([
                    ['announcements.id', '!=', $options['announcement_id']],
                    ['announcement_type_id', $options['announcement_type_id']],
                    ['property_type_id', $options['property_type_id']],
                    ['place_id', $options['place_id']],
                ])
                ->with('place')
                ->take($count)
                ->get();
        }

        public function prices()
        {
            return $this->hasMany('App\PriceLog');
        }
    }

