<?php
    
    namespace App\Http\Controllers;
    
    use App\User;
    use App\Place;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    
    class RealtorController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $realtors = User::where('user_type_id', 4)->orderByRaw("RAND()")->limit(4)->get();
            return view('pages.realtor.index')->with(['realtors' => $realtors]);
        }
        
        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            //
        }
        
        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            //
        }
        
        /**
         * Display the specified resource.
         *
         * @param \App\User $realtor
         * @return \Illuminate\Http\Response
         */
        public function show($local, User $realtor)
        {
            $contacts_arr = [];
            $contacts = $realtor->contact_types;
            foreach ($contacts as $contact) {
                $contacts_arr[$contact->pivot->contact_type_id] = $contact->pivot->value;
            }
            $other_realtors = User::where([['user_type_id', 1], ['id', '!=', $realtor->id]])->orderByRaw("RAND()")->limit(4)->get();
            // dd($other_realtors);
            return view('pages.realtor.show')->with([
                'realtor' => $realtor,
                'contacts' => $contacts_arr,
                'other_realtors' => $other_realtors
            ]);
        }
        
        public function loadAnnouncements($locale, Request $request)
        {
            // dd($request);
            $realtor = User::find($request->realtor_id);
            if ($request->ajax()) {
                if ($request->id > 0) {
                    // $data = DB::table('announcements')
                    //     ->where([['id', '<', $request->id], ['agent_id', $request->realtor_id]])
                    //     ->orderBy('id', 'DESC')
                    //     ->limit(5)
                    //     ->get();
                    $data = $realtor->announcements->where('id', '<', $request->id)->take(5);
                } else {
                    $data = $realtor->announcements->take(5);
                }
                $output = '';
                $last_id = '';
                if (!$data->isEmpty()) {
                    $output = '<div class="row">';
                    foreach ($data as $row) {
                        if ($row->statuses()->orderBy('id', 'DESC')->first()->id == 2) {
                            $community = Place::find($row->place->parent_id)->name;
                            $address = $community . ',' . $row->place->name;
                            $advertised = ($row->advertised) ? '<span class="advertised"><span><i class="fas fa-star"></i></span></span>' : '';
                            $output .= '
                            <div class="col-md-4 suggestion-item">
                                <a href="' . route('announcement.show', ['locale' => $locale, 'announcement_id' => $row->id]) . '">
                                    <div class="thumbnail">
                                        <img src="' . $row->thumbnail . '" alt="No image">
                                        ' . $advertised . '
                                        <div class="announcement-info">
                                             <span class="announcement-type">' . $row->announcement_type->title . '</span>
                                             <p>
                                                 <span class="announcement-price">' . $row->currency_price->code . $row->price . '</span>
                                                 <span class="announcement-title">' . $row->property_type->title . '</span>
                                                 <span class="announcement-adress">' . $address . '</span>
                                             </p>
                                        </div>
                                    </div>
                                </a>
                            </div>';
                            $last_id = $row->id;
                        }
                    }
                    $output .= '
                        </div><!-- .row -->
                       <div id="load_more" class="text-center">
                        <button type="button" name="load_more_button" class="btn btn-success form-control" data-id="' . $last_id . '" id="load_more_button">' . trans("common.load_more") . '</button>
                       </div>
                       ';
                    
                } else {
                    $output .= '
                       <div id="load_more">
                        <button type="button" name="load_more_button" class="btn btn-info form-control">' . trans("common.no_data_found") . '</button>
                       </div>
                       ';
                }
                echo $output;
            }
            
        }
        
        public function loadRealtors(Request $request, $locale)
        {
            if ($request->ajax()) {
                if ($request->id > 0) {
                    $data = User::where([['id', '<', $request->id], ['user_type_id', '=', 1]])->orderBy('id', 'DESC')->limit(8)->get();
                    //$data = $realtor->announcements->where('id', '<', $request->id)->take(6);
                } else {
                    $data = User::where('user_type_id', '=', 1)->orderBy('id', 'DESC')->limit(8)->get();
                    //$data = $realtor->announcements->take(6);
                }
                $output = '';
                $last_id = '';
                
                if (!$data->isEmpty()) {
                    $output = '<div class="row">';
                    foreach ($data as $row) {
                        $stars = '';
                        $socials = '';
                        $contacts = [];
                        foreach ($row->contact_types as $contact) {
                            $contacts[$contact->pivot->contact_type_id] = $contact->pivot->value;
                        }
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $row->rating) {
                                $stars .= '<span class="rating-star checked"><i class="fas fa-star"></i></span>';
                            } else {
                                $stars .= ' <span class="rating-star"><i class="far fa-star"></i></span>';
                            }
                        }
                        if (isset($contacts['3'])) {
                            $socials .= '<a class="social-fb" target="_blank" href="' . $contacts['3'] . '"><i class="fab fa-facebook-f"></i></a>';
                        }
                        if (isset($contacts['4'])) {
                            $socials .= '<a class="social-twit" target="_blank" href = "' . $contacts['4'] . '" ><i class="fab fa-twitter" ></i ></a >';
                        }
                        $output .= '<div class="col-md-3 other-realtor">
                    <a href="' . route('realtor.show', ['locale' => $locale, $row->id]) . '">
                    <div class="avatar"><img src="' . $row->avatar . '" alt="No image"></div>
                    <div class="other-realtor-info">
                        <h5>' . $row->name . '</h5>
                        <div class="rating">' . $stars . '</div>
                        </div>
                    </a>
                    <div class="socials">' . $socials . '</div>
                </div>';
                        $last_id = $row->id;
                    }
                    $output .= '
                        </div><!-- .row -->
                       <div id="load_more">
                        <button type="button" name="load_more_button" class="btn btn-success form-control" data-id="' . $last_id . '" id="load_more_button">' . trans("common.load_more") . '</button>
                       </div>
                       ';
                } else {
                    $output .= '
                       <div id="load_more">
                        <button type="button" name="load_more_button" class="btn btn-info form-control">' . trans("common.no_data_found") . '</button>
                       </div>
                       ';
                }
                echo $output;
            }
            
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\User $realtor
         * @return \Illuminate\Http\Response
         */
        public
        function edit(User $realtor)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\User $realtor
         * @return \Illuminate\Http\Response
         */
        public
        function update(Request $request, User $realtor)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param \App\User $realtor
         * @return \Illuminate\Http\Response
         */
        public
        function destroy(User $realtor)
        {
            //
        }
    }
