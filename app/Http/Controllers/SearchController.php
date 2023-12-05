<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\Project;

class SearchController extends Controller
{
    public function show()
    {
        return view('pages.search');
    }
    
    public function getSearchResult($locale, Request $request)
    {
        $projects = Project::select('id','title', 'content', 'post_type_id', 'slug', 'thumbnail')->where('title', 'LIKE', '%'. $request->search . '%')
            ->orWhere('content', 'LIKE', '%'. $request->search .'%');
        // dd($projects);
        $results = Post::select('id','title', 'content', 'post_type_id', 'slug', 'thumbnail')->where('title', 'LIKE', '%'. $request->search . '%')
                        ->orWhere('content', 'LIKE', '%'. $request->search .'%')
                        ->union($projects)
                        ->paginate();
        $count = count($results);
        $request_search = ($count > 0) ? $request->search : trans('common.no_data_found');
        $search_input_value = ($count > 0) ? $request->search : '';
      
        return view('pages.search')->with(['results' => $results, 'request_search' => $request_search, 'search_input_value' => $search_input_value]);
    }
}
