<?php

namespace App\Http\Controllers;

use App\PropertyType;
use Illuminate\Http\Request;
use App\AnnouncementOptionType;
use App\AnnouncementOption;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $property_types = PropertyType::all();
        return view('pages.options.index')->with('property_types', $property_types);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $property_types = PropertyType::all();
        $opt_types = AnnouncementOptionType::all();
        $opts = AnnouncementOption::all();
        return view('pages.options.create')->with([
            'opt_types' => $opt_types,
            'options' => $opts,
            'property_types' => $property_types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($locale, Request $request)
    {
        $serArr = array();
        foreach ($request->prop as $key => $prop)
        {
            $serArr[$key][] = serialize($prop);
        }
        dd( $serArr);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PropertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function show(PropertyType $propertyType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PropertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, PropertyType $propertyType)
    {
        dd($propertyType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PropertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PropertyType $propertyType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PropertyType  $propertyType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PropertyType $propertyType)
    {
        //
    }
}
