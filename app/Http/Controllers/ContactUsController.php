<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactUs;
use Illuminate\Support\Facades\Mail;
use App\Page;
use Illuminate\Support\Facades\Storage;
use App\Project;
use App\Category;

class ContactUsController extends Controller
{

    public function contact() {
        $page = Page::where('slug','contact')->first();
    	return view('pages.contact')->with(compact('page'));
    }

    public function show() {
        $page = Page::where('slug','contact')->first();
        $page->desc = json_decode($page->desc, true);
    	return view('pages.contact')->with(compact('page'));
    }

    public function contactForm(Request $request) {
    	if(isset($request->body)) {
    		$request->validate([
    			'name' => 'required|string',
    			'email' => 'required|email',
    			'body' => 'required|min:5|max:5000',
    		]);
            Mail::to('info@redgroup.am')->send(new ContactUs($request));

    		return redirect()->back();
    	}	
    }

    public function edit(Request $request)
    {
    	$page = '';
        $page = Page::where('slug','contact')->first();
        $pages  = Page::get();
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $categories = Category::all();
        return view("admin.pages.contact")->with(compact('page','pages','categories','projects'));
    }

    public function update(Request $request)
    {
    	// JSON_UNESCAPED_UNICODE
    	$data = $request->except('_token');
        // dd($request->hasFile('social_icon_image_4'));
        if (isset($request->desc['socials'])) {
            foreach ($data['desc']['socials'] as $key => $social) {
                if ($request->hasFile('social_icon_image_'.$key)) {
                    $icon = $request->file('social_icon_image_'.$key);
                    // dd($icon);
                    $filename = $icon->getClientOriginalName();
                    $filename_path = public_path('/storage/icons');
                    // $icon->save(public_path('storage/icons/' . $filename));
                    Storage::putFileAs('public/icons', $request->file('social_icon_image_'.$key), $filename);
                    $data['desc']['socials'][$key]['icon'] = '/storage/icons/'.$filename;
                    // unset($data['desc']['socials'][$key]['icon_image']);
                    unset($data['social_icon_image_'.$key]);
                }
            }
        }
        // dd(App::getLocale());
        foreach ($data['desc']['contacts'] as $key => &$contact) {
    		$contact['phones'] = explode(', ', $contact['phones']);
    	}
    	foreach ($data['desc']['addresses'] as $key => &$address) {
    		$address['phones'] = explode(', ', $address['phones']);
    	}
    	$data['desc'] = json_encode($data['desc'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $page = Page::where('slug', 'contact')->where('locale', \App::getLocale())->first();
    	$page->update($data);
    	return redirect(route('contact.edit'));
    }

    // public function show($locale)
    // {
    //     return view('pages.contact');
    // }

    // public function send($locale, Request $request)
    // {
    //     Mail::to(env('MAIL_TO_ADDRESS'))->send(new ContactUs($request));
    //     return redirect(route('contact', ['locale' => $locale]));
    // }
}
