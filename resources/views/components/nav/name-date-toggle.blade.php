<form class="name-date-toggle">
    <span>order by</span>
    <a href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters, ["orderBy" => "name"])) }}" {{ $nameClass }}>name</a>
    <span>&nbsp;/&nbsp;</span>               
    <a href="{{ route(Route::currentRouteName(), array_merge(Route::current()->parameters, ["orderBy" => "updated_at"])) }}" {{ $updateClass }}>last update</a>
</form>