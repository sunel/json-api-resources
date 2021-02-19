<?php

namespace ApiHelper\Http\Resources\Json;

use Illuminate\Support\Str;
use ApiHelper\IncludeRegistery;
use ApiHelper\Http\Concerns\InteractsWithRequest;

if (class_exists(\Illuminate\Http\Resources\Json\Resource::class)) {
    class RelationshipResourceMiddleManClass extends \Illuminate\Http\Resources\Json\Resource { }
} else {
    class RelationshipResourceMiddleManClass extends \Illuminate\Http\Resources\Json\JsonResource { }
}

class RelationshipResource extends RelationshipResourceMiddleManClass
{
    use InteractsWithRequest;

    protected $casts = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->getIncludes($request)->mapWithKeys(function ($include) use ($request) {
            return $this->callRealtion($include, $request);
        });
    }

    protected function callRealtion($include, $request)
    {
        list($pass, $call) = $this->canCall($include);
        if ($pass) {
            return [ $include => $this->$call($request) ];
        }

        // We are basically looking at a relationships that is nested
        $relations = collect(explode('.', $include));

        return $relations->filter(function ($relationship, $key) {
            list($pass, $call) = $this->canCall($relationship);
            return $pass;
        })->mapWithKeys(function ($relationship) use ($request) {
            return $this->callRealtion($relationship, $request);
        });
    }

    public function canCall($relationship)
    {
        $call = array_get($this->casts, $relationship, $relationship);
        return [$this->resource->relationLoaded($relationship) && method_exists($this, $call), $call];
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
