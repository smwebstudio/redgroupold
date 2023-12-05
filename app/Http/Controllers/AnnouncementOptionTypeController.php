<?php
	
	namespace App\Http\Controllers;
	
	use App\AnnouncementOptionType;
	use Illuminate\Http\Request;
	
	class AnnouncementOptionTypeController extends Controller
	{
		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			$option_types = AnnouncementOptionType::all();
			// dd($option_types);
			return view('admin.announcement_option_type.index')->with(['option_types' => $option_types]);
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
			$validatedData = $request->validate([
				'title' => 'required|max:255',
				'multiselect' => 'boolean'
			]);
			$validatedData['multiselect'] = (!isset($request->multiselect)) ? 0 : 1;
			AnnouncementOptionType::create($validatedData);
			// return response()->json($validatedData);
			return redirect()->route('announcement_option_type');
		}
		
		/**
		 * Display the specified resource.
		 *
		 * @param \App\AnnouncementOptionType $announcementOptionType
		 * @return \Illuminate\Http\Response
		 */
		public function show(AnnouncementOptionType $announcementOptionType)
		{
			//
		}
		
		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param \App\AnnouncementOptionType $announcementOptionType
		 * @return \Illuminate\Http\Response
		 */
		public function edit(AnnouncementOptionType $announcementOptionType)
		{
			//
		}
		
		/**
		 * Update the specified resource in storage.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param \App\AnnouncementOptionType $announcementOptionType
		 * @return \Illuminate\Http\Response
		 */
		public function update(Request $request, AnnouncementOptionType $announcementOptionType)
		{
			$validatedData = $request->validate([
				'title' => 'required|max:255',
				'multiselect' => 'boolean'
			]);
			$validatedData['multiselect'] = (!isset($request->multiselect)) ? 0 : 1;
			$announcementOptionType->update($validatedData);
			return redirect()->route('announcement_option_type');
		}
		
		/**
		 * Remove the specified resource from storage.
		 *
		 * @param \App\AnnouncementOptionType $announcementOptionType
		 * @return \Illuminate\Http\Response
		 */
		public function destroy(AnnouncementOptionType $announcementOptionType)
		{
			$announcementOptionType->delete();
			return redirect()->route('announcement_option_type');
		}
	}
