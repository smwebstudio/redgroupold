<?php

namespace App\Http\View\Composers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Project;
use App\Product;

class FilterprojComposer
{
      
    public function compose(View $view) {
        $projects = Project::whereNotNull('short_name')->where('visibility', 1)->where(function($query) {
        	$query->whereNull('status')->orWhere('status', '!=', 'sold'); })
            ->orderBy('top', 'DESC')->orderBy('status', 'ASC')->orderBy('project_id', 'DESC')
            ->get(['id', 'short_name', 'project_id']);
        $rooms = Product::max('rooms');
        $view->with(compact('projects','rooms')); 
    }

}