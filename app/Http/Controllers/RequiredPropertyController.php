<?php

namespace App\Http\Controllers;

use App\RequiredProperty;
use Illuminate\Http\Request;
use App\PropertyType;
use App\PlaceType;
use App\Place;
use App\AnnouncementType;
use App\AnnouncementOptionType;



class RequiredPropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($lang , Request $request)
    {
        $request->merge(['location_id' => serialize($request->location_id)]);
        $request->merge(['place_id' => serialize($request->place_id)]);
        $request->merge(['rooms' => serialize($request->rooms)]);
        $request->request->add(['user_id' => $id]);
        $required_property = RequiredProperty::create($request->all());

        $options = $request->pivot;
        if (isset($options)) {

            foreach ($options as $key => $value) {
                foreach ($value as $_value) {
                    $data[$required_property->id] = $_value;
                    $required_property->options()->attach($data);
                }
            }
        }

        return response()->json($required_property);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RequiredProperty  $requiredProperty
     * @return \Illuminate\Http\Response
     */
    public function show($locale, RequiredProperty $requiredProperty)
    {
        $property_types = PropertyType::where('locale', app()->getLocale())->get();
        $announcement_types = AnnouncementType::where('locale', app()->getLocale())->get();
        $place_types = PlaceType::whereIn('parent_id', [1, 2])->where('locale', app()->getLocale())->get();
        $opt_types = AnnouncementOptionType::where('locale', $locale)->whereIn('parent_id', [1, 13, 14, 109, 106, 22, 4, 103, 31, 94, 52])->with(['options' => function($query) use ($locale){
                    $query->where('locale',$locale);
                }])->get();
        $option_types = array();
        foreach ($opt_types as $option_type) {
            $option_types[$option_type->id] = $option_type;
        }

        return view('pages.requirement')->with([
            'property_types' => $property_types,
            'announcement_types' => $announcement_types,
            'place_types' => $place_types,
            'option_types' => $option_types
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RequiredProperty  $requiredProperty
     * @return \Illuminate\Http\Response
     */
    public function edit(RequiredProperty $requiredProperty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RequiredProperty  $requiredProperty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequiredProperty $requiredProperty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RequiredProperty  $requiredProperty
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequiredProperty $requiredProperty)
    {
        //
    }
}
