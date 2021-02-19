<?php

namespace ApiHelper\Http\Resources\Json;

use Illuminate\Http\Resources\MissingValue;
use ApiHelper\IncludeRegistery;


if (class_exists(\Illuminate\Http\Resources\Json\Resource::class)) {
    class ResourceMiddleManClass extends \Illuminate\Http\Resources\Json\Resource { }
} else {
    class ResourceMiddleManClass extends \Illuminate\Http\Resources\Json\JsonResource { }
}

class Resource extends ResourceMiddleManClass
{
    /**
     * Removes the relation data when enabled.
     *
     * @var boolean
     */
    public $noRelation = false;

    /**
     * Return the relation if it is requested.
     *
     * @param  RelationshipResource $relation
     * @param  \Illuminate\Http\Request  $request
     * @return Array | \Illuminate\Http\Resources\MissingValue
     */
    protected function loadRelation(RelationshipResource $relation, $request)
    {
        if (!$this->noRelation) {
            $data = $relation->resolve($request);

            if (!blank($data)) {
                return $data;
            }
        }

        return new MissingValue;
    }

    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $include = IncludeRegistery::load();

        if (!($include instanceof MissingValue)) {
            $data = $response->getData(true);

            $data['included'] = $include;

            $response->setData($data);
        }
    }
}
