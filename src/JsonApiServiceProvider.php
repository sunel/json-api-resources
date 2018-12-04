<?php

namespace ApiHelper;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Request::macro('getIncludes', function ($include = null) {
            $includeParts = $this->query('include');
            if (! is_array($includeParts)) {
                $includeParts = explode(',', strtolower($this->query('include')));
            }
            $includes = collect($includeParts)->filter();
            if (is_null($include)) {
                return $includes;
            }
            return $includes->contains(strtolower($include));
        });
    }
}
