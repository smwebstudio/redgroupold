<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\User', 'contact_types_users')->withPivot('value');
    }
}
