<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title'];

    public function project()
    {
        return $this->belongsToMany('App\Project', 'category_project', 'category_id', 'project_id', '', 'project_id');
    }

}
