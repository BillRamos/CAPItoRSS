<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here are the routing rules for the CAPItoRSS service.
|
*/

/*
|--------------------------------------------------------------------------
| RESTful API Service
|--------------------------------------------------------------------------
|
| This is the router for the RESTful API service that polls the NBA's
| Content API and returns a formatted RSS feed at the time of request.
| This route will only be registered if API_ENABLED is set to true in your
| .env file. This is on by default.
|
*/

if(config('app.api')){
  $router->get('/api/{endpoint:.*}','NBAAPIController@returnRSS');
}

/*
|--------------------------------------------------------------------------
| Catch-All Route
|--------------------------------------------------------------------------
|
| This route will catch all mismatching URL requests that pass through the
| server. You can customize what is returned in the catchAll() function in
| the app/Http/Controllers/WebController.php file.
|
*/

$router->get('{all:.*}','WebController@catchAll');
