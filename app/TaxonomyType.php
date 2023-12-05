<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaxonomyType extends Model
{
    protected $fillable = ['name'];
    
    public function taxonomies()
    {
        return $this->hasMany('App\Taxonomy', 'taxonomy_type_id');
    }
}
