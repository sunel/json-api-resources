<?php

namespace ApiHelper\Http\Resources\Json;

use ApiHelper\Http\Resources\IncludeRegistery;

class RelationshipResource extends \Illuminate\Http\Resources\Json\Resource
{
    protected $casts = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $request->getIncludes()->mapWithKeys(function ($include) use ($request) {
            return $this->callRealtion($include, $request);
        });
    }

    protected function callRealtion($include, $request)
    {
        if (method_exists($this, $include)) {
            return [ $include => $this->$include($request) ];
        }

        // We are basically looking at a relationships that is nested
        
        $relations = collect(explode('.', $include));

        return $relations->filter(function ($relationship, $key) {
            return method_exists($this, $relationship);
        })->each(function ($relationship, $key) {
            $relationship = array_get($this->casts, $relationship, $relationship);
            if (!$this->resource->relationLoaded($relationship)) {
                $this->resource->load($relationship);
            }
        })->mapWithKeys(function ($relationship) use ($request) {
            return $this->callRealtion($relationship, $request);
        });
    }

    /**
     * Add the resource that are included through the relationship.
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource $resource
     * @return void
     */
    protected function addInclude($resource)
    {
        $resource->noRelation = true;

        IncludeRegistery::add($resource);
    }
}
