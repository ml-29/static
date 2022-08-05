<?php
include('_core/utils.php');
include('_core/Route.php');
include('_core/Data.php');
include('_core/Page.php');
include('_core/Response.php');
include('_core/Router.php');

$config = yaml_parse_file('config.yml');
$url = '';

function route($u){
	global $url;
	$url = $u;
	$meta = new ReflectionClass(Router::class);

	//try all the methods from RouteScheme until one with a matching route pattern is found
	foreach ($meta->getMethods() as $method) {
		$routes = $method->getAttributes(Route::class);
		//filter routes that are a match for current url
		foreach($routes as $route){
			$route_pattern = new Route($route->getArguments()[0]);
			if($route_pattern->matches($url)){
				//if a match is found, run the method
				$method->invokeArgs(null, $route_pattern->getParamValuesFromURL($url));
				break 2;
			}
		}
	}
	//if no match were found, display a soft 404 error page
	Response::soft404();
}
