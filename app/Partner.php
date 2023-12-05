<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['title', 'url', 'facebook', 'image', 'group_id', 'locale', 'main_id'];
}
