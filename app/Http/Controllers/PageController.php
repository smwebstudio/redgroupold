<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Page;
use App\Project;
use App\Category;

class PageController extends Controller
{



    public function show($lang, $slug)
    {
        $post = Post::where(['slug' => $slug, 'post_type_id' => 4])->firstOrFail();
        $post_trans = $post->children()->where('locale', $lang)->firstOrFail();
        $page_all_lang = Post::where('parent_id', $post->parent_id)->get();
        $slugs = array();
        foreach ($page_all_lang as $page_lang) {
            $slugs[$page_lang->locale] = $page_lang->slug;
        }
        if (view()->exists('pages.' . $post_trans->slug)) {
            return view('pages.' . $post_trans->slug)->with(['post' => $post_trans, 'slugs' => $slugs]);
        }
        return view("pages.posts.page")->with(['post' => $post_trans, 'slugs' => $slugs]);


    }

    public function index() {

        $page = Page::where('slug','about')->first();
        return view('pages.about-us')->with(compact('page'));
    }

    public function edit($id,Request $request) {
        $page = '';
        if($request->id) {
            $page = Page::where('parent_id',$id)->orWhere('id',$id)->first();
        }
        $pages  = Page::get();
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $categories = Category::all();

        return view("admin.pages.standard")->with(compact('page','pages','categories','projects'));
    }
    
    public function update(Request $request) {
        $request->validate([
            'desc' => 'required'
        ]);
        $data = $request->except('images','_token');
        $images = [];      
        if($request->hasFile('images')) {            
            $files = $request->file('images');
            foreach($files as $file) {                
                $filename = $file->getClientOriginalName();
                $filename_path = storage_path('app/public/pages/' . $request->id);
                if (!is_dir(dirname($filename_path.'/'.$filename))) {
                    mkdir(dirname($filename_path.'/'.$filename), 0775, true);
                }
                $images[] = 'storage/pages/'. $request->id.'/'.$filename;
                $file->storeAs('public/pages/'. $request->id,$filename);               
            }
            $data['images'] = json_encode($images);
        }
        $page = Page::where('id', $request->id)->update($data);
        return redirect()->back();
    }
}
