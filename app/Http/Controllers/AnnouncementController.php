<?php
    
    namespace App\Http\Controllers;
    
    use App\Announcement;
    use App\AnnouncementSearch\AnnouncementSearch;
    use App\User;
    use Lang;
    use App;
    use App\Currency;
    use App\AnnouncementType;
    use App\AnnouncementOptionType;
    use App\PropertyType;
    use App\Place;
    use App\PlaceType;
    use Illuminate\Http\Request;
    use App\Http\Requests\StoreAnnouncement;
    use App\Setting;
    use App\AnnouncementOption;
    use Illuminate\Support\Facades\Hash;
    
    class AnnouncementController extends Controller
    {

        private $post_type_id = 2;

        private $user_seller_type_id = 2;

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index($locale, $announcement_type, $order_by='updated_at', $order_dir='DESC', Request $request)
        {
            // return response()->json($request);
            // $property_types = PropertyType::paginate(1);
            $announcement_type = ($announcement_type == 1) ? [1] : [2,3];
            $announcements = Announcement::whereIn('announcement_type_id', $announcement_type)->whereHas('statuses', function ($query) {
                $query->where('status_id', '>', 1);
            })->with('property_type', 'announcement_type', 'currency_price', 'place.parent.parent')->orderBy($order_by, $order_dir)->paginate(8); //

            $property_types = PropertyType::where('locale', $locale)->get();
            $announcement_types = AnnouncementType::where('locale', $locale)->get();

            $states = Place::whereHas('type', function($query) use ($locale){
                    $query->where([['locale', $locale], ['parent_id', 2]]);
                })->with('children.children')->get();

            $opt_types = AnnouncementOptionType::where('locale', $locale)->whereIn('parent_id', [13, 52, 109, 106, 22, 1, 4, 46, 103, 73, 94, 130, 133, 136, 97, 139, 142, 145, 148, 106, 100])->with(['options' => function($query) use ($locale){ $query->where('locale',$locale); }])->get();
            $option_types = array();
            $option_parents = array();
            foreach ($opt_types as $option_type) {
                $option_types[$option_type->id] = $option_type;
                $option_parents[$option_type->parent_id] = $option_type;
            }

            $currencies = Currency::where('locale', $locale)->get();

            $currency = Setting::where('name', 'price_options')->first()->value;
            $currency_ids = json_decode($currency, true);

            $currency_area = Setting::where('name', 'area_price_options')->first()->value;
            $currency_area_ids = json_decode($currency_area, true);
            
            if (isset($request->page)) {
                return response()->json($announcements);
            }
            return view('pages.announcement.index')->with([
                'announcements' => $announcements,
                'property_types' => $property_types,
                'announcement_types' => $announcement_types,
                'states' => $states,
                'option_types' => $option_types,
                'option_parents' => $option_parents,
                'currencies' => $currencies,
                'currency_ids' => $currency_ids,
                'currency_area_ids' => $currency_area_ids
                ]);
            }
            
            /**
             * Display a listing of the filtered resource.
             *
             * @return \Illuminate\Http\Response
             */
            public function filter(Request $request, $ajax = false)
            {
                $locale = $this->changeLang('hy');
                $announcements = AnnouncementSearch::apply($request)->with('place.parent.parent', 'announcement_type', 'currency_price', 'property_type')
                ->whereHas('statuses', function($query) {
                    $query->where(['status_id' => 4, 'current' => 1]);
                })
                ->orderBy($request->order_by,$request->order_dir);
                $property_types = PropertyType::where('locale', $locale)->get();
                $announcement_types = AnnouncementType::where('locale', $locale)->get();
                // dd($announcements->total());
                $states = Place::whereHas('type', function($query) use ($locale){
                    $query->where([['locale', $locale], ['parent_id', 2]]);
                })->with('children.children')->get();
                $opt_types = AnnouncementOptionType::where('locale', $locale)->whereIn('parent_id', [13, 52, 109, 106, 22, 1, 4, 46, 103, 73, 94, 130, 133, 136, 97, 139, 142, 145, 148, 106, 100])->with(['options' => function($query) use ($locale){
                    $query->where('locale',$locale);
                }])->get();
                $option_types = array();
                $option_parents = array();
                foreach ($opt_types as $option_type)
                {
                    $option_types[$option_type->id] = $option_type;
                    $option_parents[$option_type->parent_id] = $option_type;
                }
                $steets = [];
                if(isset($request->place['community_id'])) {
                    $steets = Place::whereIn('parent_id',$request->place['community_id'])->get();
                }
                $currencies = Currency::where('locale', $locale)->get();
                
                $currency = Setting::where('name', 'price_options_1')->first()->value;
                $currency_ids = json_decode($currency, true);

                $currency_area = Setting::where('name', 'area_price_options')->first()->value;
                $currency_area_ids = json_decode($currency_area, true);
                
                if ($request->coords && !empty($request->coords) ) {
                    $request->merge(['filter' => $request->params]);
                    return $this->filterAnnouncements($request);
                }
                if (isset($request->page)) {
                    return response()->json($announcements->paginate(8));
                }
                if( $ajax ) {
                    return $announcements->get();
                } else {
                    return view('pages.announcement.index')->with([
                        'announcements' => $announcements->paginate(8),
                        'property_types' => $property_types,
                        'announcement_types' => $announcement_types,
                        'states' => $states,
                        'option_types' => $option_types,
                        'currencies' => $currencies,
                        'currency_ids' => $currency_ids,
                        'currency_area_ids' => $currency_area_ids,
                        'streets' => $steets
    
                    ]);
                }
        }

        public function getPlaceChildren($lang, Request $request)
        {
            $places = [];
            $place_type_id = (int)$request->place_type_id + 1 ;
            if ( !empty($request->parent_id) ) {
                $places = Place::where('locale', $lang)->whereIn('parent_id', $request->parent_id)->with(['children' => function($query) use($lang) {
                    $query->where('locale', $lang);
                }])->get();
            } else {
                $places = Place::where('locale', $lang)->whereIn( 'place_type_id', $place_type_id )->with(['children' => function($query) use($lang) {
                    $query->where('locale', $lang);
                }])->get();
            }
            return response()->json($places);
        }

        public function getPrice($lang, Request $request)
        {
            if ($request->currency_id) {
                $currency = Setting::where('name', 'price_options_'.$request->announcement_type_id)->first()->value;
                $currency_type = json_decode($currency,true)[$request->currency_id];
                $prices = AnnouncementOption::where('locale', $lang)->where('type_id', $currency_type )->get();
            }
            return response()->json($prices);
        }

        public function getAreaPrice($lang, Request $request)
        {
            if ($request->currency_id) {
                $currency_area = Setting::where('name', 'area_price_options')->first()->value;
                $currency_area_type = json_decode($currency_area,true)[$request->currency_id];
                $prices_area = AnnouncementOption::where('locale', $lang)->where('type_id', $currency_area_type )->get();
            }
            return response()->json($prices_area);
        }
        
        /**
         * Show the form for creating a new resource.
         *
         * @param Request $request
         * @return \Illuminate\Http\Response
         */
        public function create(Request $request)
        {
            // dd($request);

            $lang = app()->getLocale();
            // $announcement_options = AnnouncementType::all();
            $announcement_option_types = AnnouncementOptionType::with(['options' => function($query) use($lang) {
                $query->where('locale', $lang);
            }])->get();
            $additional = AnnouncementOptionType::where('multiselect', 1)->get();
            $users = User::all();
            $announcement_types = AnnouncementType::all();
            $currencies = Currency::all();
            $property_types = PropertyType::all();
            
            $place_types = PlaceType::whereIn('parent_id', [1, 2])->where('locale', app()->getLocale())->get();

            $ceiling_heights = Announcement::getCeilingHeights($lang, 79);
            return view('pages.announcement.create')->with([
                'users' => $users,
                'announcement_types' => $announcement_types,
                'announcement_option_types' => $announcement_option_types,
                // 'announcement_options' => $announcement_options,
                'currencies' => $currencies,
                'property_types' => $property_types,
                'ceiling_heights' => $ceiling_heights,
                'additional' => $additional,
                'place_types' => $place_types            
            ]);
        }
        
        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         *
         *
         */
        public function store($lang, StoreAnnouncement $request)
        {
            $request->request->add(['code' => rand()]);
            $default_thumb = Setting::getOptionByName('announcement_default_thumb_url')->value;
            $request->request->add(['thumbnail' => $default_thumb]);
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $request->request->add(['seller_id' => $user->id]);
            } else {
                $user_data = array(
                    'name' => $request->name,
                    'email' => $request->email,
                );
                $user = User::create($user_data);
                $request->request->add(['seller_id' => $user->id]);
            }
            $announcement = Announcement::create($request->all());
            $announcement->options()->attach($request->option_type);
            $announcement->statuses()->attach('1');
            
            if ($request->hasfile('image')) {
                $announcement->storeAnnouncementImage($request->file('image'), $announcement->id, $request->code);
            }
            $announcement->from_site = 1;
            $currency_rate = Currency::find($request->price_currency_id)->rate;
            $announcement->price_in_amd = $request->price * $currency_rate;
            $announcement->save();
    
            return view('pages.home');
        }

        /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     *
     */
    public function storeClientSide($lang, StoreAnnouncement $request)
    {
        // return response()->json($request);
        // $request = $request[0];
        $request = $request->toArray();
//        return $request['tel'];
        // dd($request);
        $default_thumb = Setting::getOptionByName('announcement_default_thumb_url')->value;
        $request['thumbnail'] = $default_thumb;
        $user = User::where('email', $request['email'])->first();
         if ($user) {
            $request['seller_id'] = $user->id;
        } else {
            $user_data = array(
                'name' => $request['name'],
                'email' => $request['email'],
                'user_type_id' => $this->user_seller_type_id,
                'rating' => 0,
                'avatar' => url('/storage/'.env('USER_DEFAULT_AVATAR')),
                'password' =>  Hash::make('password', [
                    'rounds' => 12
                ])
            );
            $user = User::create($user_data);
            $request['seller_id'] = $user->id;
        }
        $request['code'] = rand();
        $announcement = Announcement::create($request);
        if (isset($request['option_type'])) {
            $announcement->options()->attach($request['option_type']);
        }
        $announcement->statuses()->attach('1');
        if (isset($request['image'])) {
            $announcement->storeAnnouncementImage($request['image'], $announcement);
        }
        $announcement->save();

        return view('pages.announcement.success')->with(['message' => "Success"]);

        // return 'Ձեր հայտարարությունը ուղարկված է։';
        // return response()->json();
        // return redirect(env('ANNOUNCEMENT_RESPONSE_URL') . '/' . $lang . '/announcement/success');
    }
        
        public function success($lang)
        {
            if(url()->previous() == route('announcement.create', $lang))
            return view('pages.announcement.success')->with(['message' => "Success"]);
            return redirect(route('error', $lang));
        }
        
        /**
         * Display the specified resource.
         *
         * @param \App\Announcement $announcement
         * @return \Illuminate\Http\Response
         */
        
        public function show($announcement_code)
        {
            $locale = $this->changeLang('hy');
            $announcement = Announcement::whereHas('statuses', function($query) {
                $query->where(['status_id' => 4, 'current' => 1]);
            })->where('code', $announcement_code)->with('prices')->first();
            // dd($announcement);
            if (!$announcement) {
                return redirect(route('error', $locale));
            }

            $option_type_groups = array(
                                    'house' => [126, 52, 61, 58, 37],
                                    'land' => [116, 31, 118, 10, 55]
                                    );

            $amenities = $announcement->options()->whereHas('optionType', function($query) {
                $query->where('parent_id', 112);
            })->pluck('option_id')->toArray();
            $all_amenities = AnnouncementOption::whereHas('optionType', function($query) {
                $query->where('parent_id', 112);
            })->get();
            $option_types = AnnouncementOptionType::whereHas('options', function($query) use($announcement) {
                $query->whereHas('announcements', function($quer) use($announcement) {
                    $quer->where('announcement_id', $announcement->id);
                });
            })->with(['options' => function($que) use($announcement) {
            	$que->whereHas('announcements', function($que) use($announcement) {
                    $que->where('announcement_id', $announcement->id);
                });
            }])->orderBy('order_'.$announcement->property_type_id, 'ASC')->whereNotIn('parent_id', [112,19])->get();
            $main_options = $announcement->options()->whereNotIn('type_id', [112])->with('optionType')->get();
            $ann_images = $announcement->files()->where('post_type_id', 2)->get();
            $comments = $announcement->comments->where('post_type_id', 2);

            $announcement->view += 1;
            $announcement->save();

            //dd($comments);
            // dd($main_options);
            $searchOptions = [
                'announcement_type_id' => $announcement->announcement_type_id,
                'property_type_id' => $announcement->property_type_id,
                'place_id' => $announcement->place_id,
                'announcement_id' => $announcement->id
            ];
            $related_announcements = $announcement->getRelatedAnnouncements($searchOptions);
            return view('pages.announcement.show')->with([
                'announcement' => $announcement,
                'ann_images' => $ann_images,
                'amenities' => $amenities,
                'all_amenities' => $all_amenities,
                'main_options' => $main_options,
                'option_type_groups' => $option_type_groups,
                'comments' => $comments,
                'related_announcements' => $related_announcements,
                'option_types' => $option_types
            ]);
        }

        public function addClickCount( Request $request ) 
        {
            $announcement = Announcement::find($request->id);

            $announcement->click_contact += 1;
            $announcement->save();
        }

        public function filterAnnouncements( Request $request )
        {
            $request_coords = $request->coords;
            $filter = [];

            parse_str($request->filter, $filter);

            // return $filter;
            if( isset($filter['place']) ) {
                unset($filter['place']);
            }
            
            $locale = $request->locale;
            $filter['order_by'] = 'created_at';
            $filter['order_dir'] = 'DESC';
            $data = new Request($filter);
            $ajax = true;
            $announcements = $this->filter($data, $ajax);

            
            $outer_html = '';
            foreach ($announcements as $key => $ann) {
                if(isset($ann['coords'])) {
                    $ann_coords = explode(',',$ann['coords']);

                    $vertices_x = [];
                    $vertices_y = [];

                    foreach ($request_coords as $coord) {
                        $vertices_x[] = (float)$coord[0];
                        $vertices_y[] = (float)$coord[1];
                    };

                    $points_polygon = count($vertices_x) - 1;
                    $longitude_x = (float)$ann_coords[0];
                    $latitude_y = (float)$ann_coords[1];

                    if ($this->is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)) {
                        
                        $address = $ann["place"]["parent"]["name"].','.$ann["place"]["name"];

                        $outer_html .=  '<div class="col-md-6 announcement-item">
                                            <a href="'.route('announcement.show', [app()->getLocale(), $ann["code"]]).'">
                                                <div class="thumbnail">
                                                    <img src="'.$ann["thumbnail"].'" alt="No image">
                                                    <span class="announcement-type">'.$ann["announcement_type"]["title"].'</span>
                                                    <p class="announcement-price">
                                                        <span class="property-price">'.number_format($ann["price"]).' '.$ann["currency_price"]["title"].'</span>
                                                    </p>';

                                                    if(isset($ann["advertised"])) {
                                                       $outer_html .= '<span class="advertised"><i class="fas fa-star"></i></span>';
                                                    }
                                $outer_html .=  '</div>';
                                $outer_html .=  '<div class="property-info">
                                                    <h4>'.$ann["property_type"]["title"].' '.$address.'</h4>
                                                    <p class="address">'.__('common.address').' &#32'.$address.'</p>
                                                    <p>
                                                        <span class="rooms">
                                                            <i class="far fa-moon"></i>
                                                            <span class="rooms-count">'.$ann["rooms"].'</span>
                                                            <span>'.__('common.rooms').'</span>
                                                        </span>
                                                        <span class="area">
                                                            <i class="fas fa-vector-square"></i>
                                                            <span class="area-count">'.$ann["area"]. __('common.area_point').'</span>
                                                            <span>'. __('common.area'). '</span>
                                                        </span>
                                                    </p>
                                                </div>';
                            $outer_html .= '</a>
                                        </div>';
                    }
                    else {
                        unset($announcements[$key]);
                    }
                }
                else {
                    unset($announcements[$key]);
                }
            }

            
            // return response()->json($outer_html);
            return response()->json(['data' => $announcements, 'current_page' => 2, 'last_page' => 2 ]);

        }



        public function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
        {
            $i = $j = $c = 0;
            for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
                if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
                $c = !$c;
            }
            return $c;
        }

        
        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\Announcement $announcement
         * @return \Illuminate\Http\Response
         */
        public function edit(Announcement $announcement)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Announcement $announcement
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, Announcement $announcement)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param \App\Announcement $announcement
         * @return \Illuminate\Http\Response
         */
        public function destroy(Announcement $announcement)
        {
            //
        }

        private function changeLang($lang) {
			if(isset($lang)) {
				app()->setLocale($lang);
				session(['lang' => $lang]);
				$new_lang = app()->getLocale();
				return $new_lang;
			}
		}
    }
