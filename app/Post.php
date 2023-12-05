<?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    
    class Post extends Model
    {
        protected $perPage = 6;
        
        private $post_type_id = 1;
        
        protected $fillable = ['title', 'content', 'post_type_id', 'slug', 'locale', 'view', 'thumbnail'];
        
        public function post_type()
        {
            return $this->belongsTo('App\PostType', 'post_type_id');
        }
        
        public function taxonomies()
        {
            return $this->belongsToMany('App\Taxonomy', 'posts_taxonomies')->withTimestamps();
        }

        public function parent()
        {
            return $this->belongsTo('App\Post', 'parent_id');
        }

        public function children()
        {
            return $this->hasMany('App\Post', 'parent_id');
        }
        
        public function getBlogPosts($locale, $post_count = 5)
        {
            return self::where([['post_type_id', $this->post_type_id], ['locale', $locale]])
                        ->orderBy('id', 'DESC')
                        ->take($post_count)
                        ->get();
        }
    }
