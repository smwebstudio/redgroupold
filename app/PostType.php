<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    public function posts()
    {
        return $this->hasMany('App\Post', 'post_type_id');
    }
}
