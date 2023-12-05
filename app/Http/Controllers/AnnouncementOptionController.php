<?php
    
    namespace App\Http\Controllers;
    
    use App\AnnouncementOption;
    use App\AnnouncementOptionType;
    use App\PropertyType;
    use Illuminate\Http\Request;
    
    class AnnouncementOptionController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            // $property_types = PropertyType::all();
            // $opt_types = AnnouncementOptionType::all();
            // $opts = AnnouncementOption::all();
            // return view('pages.options.index')->with([
            //     'opt_types' => $opt_types,
            //     'options' => $opts,
            //     'property_types' => $property_types
            // ]);
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
         * @param \App\AnnouncementOption $announcementOption
         * @return \Illuminate\Http\Response
         */
        public function show(AnnouncementOption $announcementOption)
        {
            //
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\AnnouncementOption $announcementOption
         * @return \Illuminate\Http\Response
         */
        public function edit(AnnouncementOption $announcementOption)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\AnnouncementOption $announcementOption
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, AnnouncementOption $announcementOption)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param \App\AnnouncementOption $announcementOption
         * @return \Illuminate\Http\Response
         */
        public function destroy(AnnouncementOption $announcementOption)
        {
            //
        }
    }
