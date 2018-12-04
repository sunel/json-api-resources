<?php

namespace ApiHelper\Http\Resources;

use Illuminate\Http\Resources\MissingValue;

class IncludeRegistery
{
    
    /**
     * Hold the collected resource that are included through relations.
     *
     * @var array
     */
    protected static $includes = [];

    /**
     * Return the collected included resource.
     *
     * @return Array | \Illuminate\Http\Resources\MissingValue
     */
    public static function load()
    {
        if (count(static::$includes)) {
            return collect(static::$includes)->unique(function ($item) {
                return get_class($item) . $item->resource->getKey();
            })->values()->all();
        }

        return new MissingValue;
    }

    /**
     * Add the resource that are included through the relationship.
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource $resource
     * @return void
     */
    public static function add($resource)
    {
        static::$includes[] = $resource;
    }

    /**
     * Return the collected included resource.
     *
     * @return Array
     */
    public static function get()
    {
        return static::$includes;
    }
}
