<?php

namespace App\Http\Controllers;

use App\Partner;
use Illuminate\Http\Request;
use App\Project;
use App\Category;
use App\PartnerGroup;
use App\Page;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partner_groups = PartnerGroup::where('locale', session('lang'))->with('partners')->get();
        return view('pages.partners')->with(compact('partner_groups'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit(Partner $partner)
    {
       // dd(session('lang'));
        $partner_groups = PartnerGroup::where('locale', session('lang'))->with('partners')->orderBy('main_id', 'asc')->get();
        $partner_group_last_id = PartnerGroup::latest('id')->first()->id;
        $partner_last_id = Partner::latest('id')->first()->id;
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $categories = Category::all();
        $pages  = Page::get();
        $default_locale = session('lang') == 'hy' ? true : false;
        return view('admin.pages.partners')->with(compact('partner_groups', 'projects', 'categories', 'pages', 'partner_group_last_id', 'partner_last_id', 'default_locale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $groups = $request->except('_token');
        $partner_group_last_id = PartnerGroup::latest('id')->first()->id;
        $partner_last_id = Partner::latest('id')->first()->id;
        $default_locale = config('app.locale');
        $current_locale = session('lang');
        $all_locales = config('app.available_locales');
        foreach ($groups['groups'] as $group_id => $group) {
        	if (isset($group['title'])) {
	            $partner_group = PartnerGroup::updateOrCreate(['id' => $group_id], ['title' => $group['title'], 'locale' => $current_locale]);
	            if ($group_id > $partner_group_last_id) {
                    $partner_group->main_id = $partner_group->id;
                    $partner_group->save();
	            	foreach ($all_locales as $locale) {
	            		if ($locale != 'hy') {
	            			$partner_group_trans = PartnerGroup::create(['id' => $group_id, 'title' => $group['title'], 'locale' => $locale, 'main_id' => $partner_group->id]);
	            		}
	            	}
	            }
        	}
            if (isset($group['partners'])) {
                foreach ($group['partners'] as $partner_id => $partner) {
                    if (isset($partner['title'])) {
                        $part = Partner::updateOrCreate(['id' => $partner_id], ['title' => $partner['title'], 'group_id' => $partner_group['id'], 'url' => $partner['url'], 'facebook' => $partner['facebook'], 'locale' => $current_locale]);
                        if ($request->hasFile('image_' . $partner_id)) {
                            $partner_image = $request->file('image_' . $partner_id);
                            $filename = $partner_image->getClientOriginalName();
                            $filename_path = public_path('/storage/partners');
                            Storage::putFileAs('public/partners', $request->file('image_'.$partner_id), $filename);
                            $part->image = '/storage/partners/' . $filename;
                            $part->save();
                        }
	                    if (!isset($partner['main_id'])) {
                            $partner_group_id = $partner_group['id'];
                            $part->main_id = $part->id;
                            $part->save();
	                    	foreach ($all_locales as $index => $locale) {
            					if ($locale != 'hy') {
	                    			$part_trans = Partner::create(['title' => $partner['title'], 'group_id' => $partner_group_id, 'url' => $partner['url'], 'facebook' => $partner['facebook'], 'image' => '/storage/partners/' . $filename, 'locale' => $locale, 'main_id' => $part->main_id]);
	                    		}
                                    $partner_group_id++;
	                    	}
	                    }
                    }
                }
            }
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy($partner_id)
    {
        Partner::where('main_id', $partner_id)->delete();
    }

    public function destroyGroup($group_id)
    {
        $groups = PartnerGroup::where('main_id', $group_id)->pluck('id')->toArray();
        PartnerGroup::where('main_id', $group_id)->delete();
        Partner::whereIn('group_id', $groups)->delete();
    }
}
