<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    protected $fillable = ['title', 'desc', 'seo_title', 'seo_desc', 'seo_keywords'];


    // Language global scope
    protected static function boot()
    {
        parent::boot();
        $locale = session('lang') ?? app()->getLocale();
        static::addGlobalScope('locale', function (Builder $builder) use ($locale){
            $builder->where('locale', $locale);
        });
    }

    public function setImagesAttribute($images)
    {
        if (is_array($images)) {
            $this->attributes['images'] = json_encode($images);
        }
    }

    public function getImagesAttribute($images)
    {
        return json_decode($images);
    }
}
