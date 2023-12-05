<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $fillable = ['project_id', 'title', 'short_name','short_desc', 'slug', 'address', 'locale', 'content', 'coordinates','phone', 'visibility', 'featured_image', 'status', 'location_id', 'property_type_id', 'top', 'viewed','get_rate_type','currency_usd','currency_rub','currency_eur'];

    public function category()
    {
        return $this->belongsToMany('App\Category');
    }

    /**
     * Get the files for the project.
     */
    public function file()
    {
        return $this->hasMany('App\Files', 'post_id', 'project_id');
    }

    /**
     * Get the SEO settings for the project.
     */
    public function seo_settings()
    {
        return $this->hasMany('App\SeoSettings', 'post_id');
    }

    /**
     *  Get the project products
     * @return mixed
     */
    public function products()
    {
        return $this->hasMany('App\Product', 'post_id', 'project_id');
    }

    public function main_project()
    {
        return $this->belongsTo('App\Project', 'project_id')->withoutGlobalScopes();
    }

    // Set language global scope
    protected static function boot()
    {
        parent::boot();

        $locale = session('lang') ?? app()->getLocale();

        static::addGlobalScope('locale', function (Builder $builder) use($locale) {
            // $builder->where('locale',session('lang'));
            $builder->where('locale',$locale);
        });
    }

    // Check whether all products are sold or not
    public function checkProducts() {
        $products = $this->products;
        foreach($products as $prod) {
            if($prod->status !== 'sold') {
                return false;
            }
        }
        return true;
    }

    public function location()
    {
        return $this->belongsTo('App\Location');
    }
}
