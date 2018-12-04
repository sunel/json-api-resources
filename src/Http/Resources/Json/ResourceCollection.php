<?php

namespace ApiHelper\Http\Resources\Json;

use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;
use ApiHelper\Http\Resources\IncludeRegistery;

class ResourceCollection extends \Illuminate\Http\Resources\Json\ResourceCollection
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
                    ? (new PaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
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
