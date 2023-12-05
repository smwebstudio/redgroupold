<?php

namespace App\AnnouncementSearch;

use App\Announcement;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AnnouncementSearch
{
    public static function apply(Request $filters)
    {
        // dd($filters);
        $query = static::applyDecoratorsFromRequest($filters, (new Announcement)->newQuery());

        return $query;
        // return static::getResults($query);
    }

    private static function applyDecoratorsFromRequest(Request $request, Builder $query)
    {
        $data = $request->all();
        $rate = isset($data['currency']) ? $data['currency'] : null ;
        foreach ($data as $filterName => $value) {
            if (!is_null($value) && !in_array($filterName , ['_token', 'currency'])) {
                $decorator = static::createFilterDecorator($filterName);

                if (static::isValidDecorator($decorator)) {
                    // $query = $decorator::apply($query, $value, $data['currency']);
                    $query = $decorator::apply($query, $value, $rate);
                }
            }
        }
        return $query;
    }

    private static function createFilterDecorator($name)
    {
        return __NAMESPACE__ . '\\Filters\\' . Str::studly($name);
    }

    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }

    private static function getResults(Builder $query)
    {
        return $query->whereHas('statuses', $filter = function ($query) {
                $query->where('status_id', '>', 1);
            })->with('statuses')->orderBy('updated_at', 'DESC')->get();
    }
}
