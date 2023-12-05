<?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    
    class Taxonomy extends Model
    {
        protected $fillable = ['name', 'taxonomy_type_id'];
        
        public function taxonomy_type()
        {
            return $this->belongsTo('App\TaxonomyType', 'taxonomy_type_id');
        }
        
        public function posts()
        {
            return $this->belongsToMany('App\Post', 'posts_taxonomies')->withTimestamps();
        }
    }
