<?php

namespace ApiHelper\Http\Concerns;

trait InteractsWithRequest
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function getIncludes($request)
    {
        $includeParts = $request->query('include');
        if (! is_array($includeParts)) {
            $includeParts = explode(',', strtolower($request->query('include')));
        }
        $includes = collect($includeParts)->filter();
        return $includes;
    }
}
