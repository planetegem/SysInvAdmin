<?php
if (! function_exists('query_route')) {
    function query_route($name, $parameters = [], $absolute = true)
    {
	    $parameters = Arr::wrap($parameters);
        return route($name, array_merge($parameters, request()->query()), $absolute);
    }
}
?>