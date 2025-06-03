<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/users', 'UserController@store'); // Called from auth-service
$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    $router->get('/users/me', 'UserController@me');      // Get profile
    $router->put('/users/me', 'UserController@update');  // Update profile
    $router->delete('/users/me', 'UserController@delete'); // Delete
});